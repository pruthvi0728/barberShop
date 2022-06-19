<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class slotsBooked extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'client_count',
        'category_id'
    ];
}
