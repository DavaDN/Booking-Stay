<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'name',
        'room_id',
        'check_in_date',
        'check_in_time',
        'check_out_date',
        'check_out_time',
        'status'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
