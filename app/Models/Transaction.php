<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['booking_id', 'amount', 'status', 'payment_method'];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
