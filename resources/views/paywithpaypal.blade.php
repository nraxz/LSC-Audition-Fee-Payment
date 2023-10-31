<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Bootswatch: Journal</title>
     <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://bootswatch.com//5/journal/bootstrap.css">
    <link rel="stylesheet" href="https://bootswatch.com//_vendor/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://bootswatch.com//_vendor/prismjs/themes/prism-okaidia.css">
    <link rel="stylesheet" href="https://bootswatch.com//_assets/css/custom.min.css">
<body>
<div class="container">
        <div class="row bg-primary  mb-3">
            <div class="col ">
                <img src="https://www.londonstudiocentre.org/wp-content/themes/lsc/assets/dist/img/londonstudiocentre-logo.svg" alt="London Studio Centre Logo" style="width: 350px; height: auto;">
            </div>
        </div>
      
        <h2 class="navbar-brand">London Studio Centre Audition Fee Payment</h2>     
   
       
    	<form class="card mb-3" method="POST" id="payment-form"
          action="{!! URL::to('paypal') !!}">
          <div class="card-header text-white bg-primary ">Paywith Paypal</div>
          <div class="card-body">
            {{ csrf_field() }}
            <h2 class="card-text">{{$applicant_name}}</h2>
            <p class="card-text">Audition Date & Time: {{$audition_date}}<br/>
            Application Fee: {{$application_fees}}<br/>
            Payment Status: {{$payment_status}}
            </p>
          
            
            <input type="hidden" name="application_id" value="{{$application_id}}">
            <input type="hidden" name="amount" value="{{$application_fees}}">
            <input type="hidden" name="applicant_email" value="{{$applicant_email}}">
            <input type="hidden" name="application_login" value="{{$application_login}}">
            <input type="hidden" name="audition_date" value="{{$audition_date}}">
            <input type="hidden" name="applicant_name" value="{{$applicant_name}}">
            <input type="hidden" name="audition_type" value="{{$audition_type}}">        
          </div>
          <div class="card-footer">
            @if($payment_status == 'Complete')
              
            @else
              @if($application_fees > 0)
                <button class="w3-btn w3-blue">Pay with PayPal</button>
              @endif
            @endif
          </div>
    	</form>
    </div>
</body>
</html>