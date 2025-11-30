<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guarded = [];

    // Relação: Um exame pode ser exigido por funções
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}