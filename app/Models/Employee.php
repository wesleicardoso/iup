<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    // O funcionário pertence a uma empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // O funcionário possui uma função (cargo)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}