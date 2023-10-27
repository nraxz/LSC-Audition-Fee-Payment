<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = "venue";
    protected $fillable = ['id', 'venue_name','address_1', 'address_1', 'town_city', 'county', 'country', 'postcode'];
}
