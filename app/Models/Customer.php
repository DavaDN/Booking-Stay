<?php

// app/Models/Customer.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
