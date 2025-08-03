<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['number', 'floor_id', 'room_type_id', 'status'];
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
