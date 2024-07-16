<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salarier extends Model
{
    use HasFactory;
    public function prime(){
        return $this->hasMany(Prime::class);
    }
    public static $emplois = [
        'Technicien',
        'Technicien Specialise'
    ];
    public function salaire(){
        return $this->hasMany(Salaire::class);
    }
    public function bulletin(){
        return $this->hasMany(Bulletin::class);
    }
}
