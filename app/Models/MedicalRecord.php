<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $guarded = [];

    public function attachments() {
        return $this->hasMany(MedicalAttachment::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
    
    public function appointment() {
        return $this->belongsTo(Appointment::class);
    }
}