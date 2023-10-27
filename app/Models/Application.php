<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = "application_detail";
    protected $fillable = ['id', 'number', 'login','venue_id', 'audition_id', 'program', 'app_type', 'payment_status', 'free_audition', 'note', 'apecialise_area','register','stage','submitted'];
}
