<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $guarded = [];
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

  
}
