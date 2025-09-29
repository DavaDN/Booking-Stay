<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'city', 'image', 'description'];

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function facility()
    {
        return $this->belongsToMany(FacilityHotel::class, 'hotel_facility_hotel');
    }
}
