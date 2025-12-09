<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'room_type_id',
        'check_in',
        'check_out',
        'number_of_rooms',
        'booking_code',
        'total_price',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
