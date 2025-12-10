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
        'hotel_id',
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

    /**
     * Many-to-many relation to hotels for room types assigned to multiple hotels
     */
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_room_type', 'room_type_id', 'hotel_id');
    }

    /**
     * Get count of available rooms
     */
    public function getAvailableRoomsAttribute()
    {
        return $this->rooms()
            ->where('status', 'available')
            ->count();
    }
}
