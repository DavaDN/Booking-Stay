<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = ['name', 'price', 'description'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
