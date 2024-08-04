<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_cost',
        'order_date',
        'city',
        'state_province',
        'country',
        'total_amount',
        'status',
        'shipment_tracking_number',
    ];
}
