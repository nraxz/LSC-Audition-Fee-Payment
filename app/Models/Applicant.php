<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $table = "sec_users";   
    protected $fillable = ['login', 'name','email', 'active'];
}
