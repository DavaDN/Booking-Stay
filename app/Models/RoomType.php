<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'capacity',
        'description',
        'image',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facilities::class, 'facility_room_type', 'room_type_id', 'facility_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
