<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prime extends Model
{
    use HasFactory;
    protected $primaryKey = 'prime_id';
    public function salarier(){
        return $this->belongsTo(Salarier::class);
    }
    public static $types = [
        'fix',
        'variable',
        'bonus'
    ];
}
