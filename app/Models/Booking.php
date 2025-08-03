<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['customer_id', 'room_id', 'check_in', 'check_out', 'status'];
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
