<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sousfonction extends Model
{
    protected $table = 'sousfonctions';
    public $timestamps = false;

    protected $fillable = ['sousfonction_name', 'fonction_id'];

    public function fonction()
    {
        return $this->belongsTo(Fonction::class, 'fonction_id');
    }
}
