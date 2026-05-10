<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Langue extends Model
{
    use HasFactory;

    protected $table = 'langue'; // Table au singulier
    public $timestamps = false;

    protected $fillable = ['nom'];
}
