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
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
