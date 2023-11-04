<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Application;
use App\Models\Audition;
use App\Models\Applicant;
use App\Models\Venue;
use App\Mail\BookingConfirm;
use App\Models\EmailBodyMessage;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use App\Http\Controllers\Session;

class PaymentController extends Controller
{
    private $_api_context;
    public function __construct()
    {
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    
    public function index()
    {
        if (!isset($_GET['pk'])) {
            return view('wrongparameters');
        }
        $pk = $_GET['pk'];
        $pks = explode('-', $pk);

        if (count($pks) != 2) {
            return view('wrongparameters');
        }

        [$aid, $sid] = $pks;
        $application = Application::find($aid);

        if (!$application) {
            return view('noapplicationfound');
        }
        if ($application->login != $sid) {
            return view('noapplicationfound');
        }

        $audition = Audition::find($application->audition_id);
        $venue = Venue::find($application->venue_id);
        $applicant = Applicant::where('login', $application->login)->first();

        if (!$audition || !$venue || !$applicant) {
            return view('noapplicationfound');
        }

        $application_id = $application->id;
        $applicant_name = $applicant->firstname . ' ' . $applicant->lastname;
        $applicant_email = $applicant->email;
        $application_fees = $audition->audition_fee;
        $audition_date = $audition->audition_date;
        $audition_title = $audition->audition_title;
        $application_login = $application->login;
        $audition_type = $audition->type;
        $payment_status = $application->payment_status;

        
        return view('paywithpaypal', compact(
            'application_id',
            'applicant_name',
            'audition_date',
            'application_fees',
            'applicant_email',
            'application_login',
            'audition_type',
            'payment_status',
            'audition_title'
        ));
    }


    public function payWithpaypal(Request $request)
    {
        // Get the session object.
       

        // Create a new Payer object.
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Create a new Item object.
        $item_1 = new Item();
        $item_1->setName('LSC Audition '.$request->get('audition_date'))
            ->setCurrency('GBP')
            ->setQuantity(1)
            ->setPrice($request->get('amount'));

        // Create a new ItemList object.
        $item_list = new ItemList();
        $item_list->setItems([$item_1]);

        // Create a new Amount object.
        $amount = new Amount();
        $amount->setCurrency('GBP')
            ->setTotal($request->get('amount'));

        // Create a new Transaction object.
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Audition Fee');

        // Create a new RedirectUrls object.
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(url('status')) // Specify return URL
            ->setCancelUrl(url('status'));

        // Create a new Payment object.
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions([$transaction]);

        try {
            // Create the payment.
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            // Handle connection timeout.
            if (config('app.debug')) {
                $session->put('error', 'Connection timeout');
                return redirect()->route('failed');
            } else {
                $session->put('error', 'Some error occur, sorry for inconvenient');
                return redirect()->route('failed');
            }
        }

        // Get the redirect URL.
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() === 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        // Store the payment ID in the session.
        
        \Session::put('paypal_payment_id', $payment->getId());

        // Store the applicant information in the session.
        \Session::put('applicant_email', $request->applicant_email);
        \Session::put('applicant_name', $request->applicant_name);
        \Session::put('application_id', $request->application_id);
        \Session::put('application_fees', $amount);
        \Session::put('application_login', $request->application_login);
        \Session::put('audition_date', $request->audition_date);
        \Session::put('audition_type', $request->audition_type);

        // If the redirect URL is set, redirect to PayPal.
        if (isset($redirect_url)) {
            return redirect()->away($redirect_url);
        }

        // Otherwise, set an error message and redirect to the failed page.
        \Session::put('error', 'Unknown error occurred');
        return redirect()->route('failed');
    }


    public function getPaymentStatus(Request $request)
    {
        /** Get the payment ID before session clear **/

        //return $request;
       
        $payment_id = \Session::get('paypal_payment_id');
       
       
        $applicant_email = $request->session()->pull('applicant_email');
        $applicant_name = $request->session()->pull('applicant_name');
        $audition_type = $request->session()->pull('audition_type');
        $application_fees = $request->session()->pull('application_fees');
        $application_login = $request->session()->pull('application_login');
        $application_id = $request->session()->pull('application_id');
        $audition_date = $request->session()->pull('audition_date');
        $audition_type = $request->session()->pull('audition_type');

        $details = array(
            'application_id' => $application_id,
            'applicant_name' => $applicant_name,
            'applicant_email' => $applicant_email,
            'paymentId' => $request->paymentId,
            'payerId' => $request->PayerID,
            'token' => $request->token,
            'application_fees'=> $application_fees,
            'application_login' => $application_login,
            'payment_status' => 'Complete'           
           
        );      


        /** clear the session payment ID **/
        //Session::forget('paypal_payment_id');
        \Session::forget(
            'paypal_payment_id',             
            'application_id',
            'applicant_name',
            'audition_date',
            'application_fees', 
            'applicant_email', 
            'application_login', 
            'audition_type'
        );
        $payerId = $request->input('PayerID');
        $token = $request->input('token');
    
        if (empty($payerId) || empty($token)) {
            session()->put('error', 'Payment failed');
            return redirect()->route('cancel');
        }
        

        $payment = Payment::get($payment_id, $this->_api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
             $result = $payment->execute($execution, $this->_api_context);
             if ($result->getState() === 'approved') 
             {
                \Session::put('success', 'Payment success');
                $this->recordUpdate($application_id);
                $this->createPaymentLog($details);

                $email_name = 'booking_confirmed';

                $email_body = EmailBodyMessage::where('email_name', $email_name)->first();


                $objDemo = new \stdClass();
                $objDemo->name = $applicant_name;
                $objDemo->subject = 'Booking Confirmation';
                //$objDemo->email = $applicant_email;
                $objDemo->email = 'thenraxz@gmail.com';
                $objDemo->content = $email_body->email_body;
            
 
            //Mail::to($applicant->user->email)->cc($company_email)->send(new AuditionFeesPaid($objDemo));
               Mail::to($applicant_email)->send(new BookingConfirm($objDemo));
            
        
            return Redirect('/success');
        
             }

            } catch (\Exception $ex) {
                session()->put('error', 'Payment failed');
            }
        
        return redirect()->route('failed');
    }            

    public function success()
    {
        return view('success');
    }

    public function failed()
    {
        return view('failed');
    }

    public function cancel()
    {
        return view('cancel');
    }

    private function recordUpdate($applicationId)
    {
        DB::table('application_detail')->where('id', $applicationId)->update(['payment_status' => 'Complete']);

    }

    private function createPaymentLog($details)
    {
        DB::table('transaction_log')->insert([
            'application_id' => $details['application_id'],
            'applicant_name' => $details['applicant_name'],
            'applicant_email' => $details['applicant_email'],
            'paymentId' => $details['paymentId'],
            'payerId' => $details['payerId'],
            'token' => $details['token'],
            'application_fees' => $details['application_fees'],
            'application_login' => $details['application_login'],
            'payment_status' => $details['payment_status'],
        ]);
    }
}  