<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
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

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
