<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'total',
        'status',
        'payment_date',
        'customer_id',
        'midtrans_order_id',
        'payment_type',
        'midtrans_status',
        'midtrans_response',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
