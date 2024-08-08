<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaire extends Model
{
    use HasFactory;
    public function salarier(){
        return $this->belongsTo(Salarier::class);
    }
    protected $casts = [
        'date_sup' => 'array',
    ];
}
