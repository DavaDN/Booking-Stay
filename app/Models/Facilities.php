<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facilities extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'name',
        'image',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
