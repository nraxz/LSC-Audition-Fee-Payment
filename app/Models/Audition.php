<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audition extends Model
{
    protected $table = "audition";
    protected $fillable = ['id', 'venue_id', 'audition_date','audition_title', 'audition_fee'];
}
