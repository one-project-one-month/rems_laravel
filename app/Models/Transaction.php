<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'agent_id',
        'transaction_date',
        'sale_price',
        'commission',
        'status'
    ];
}
