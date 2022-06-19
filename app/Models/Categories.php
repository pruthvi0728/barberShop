<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'time_of_slot',
        'clean_up_time',
        'max_client',
        'future_days_to_book',
        'mon_fri_from_time',
        'mon_fri_to_time',
        'sat_from_time',
        'sat_to_time'
    ];
}
