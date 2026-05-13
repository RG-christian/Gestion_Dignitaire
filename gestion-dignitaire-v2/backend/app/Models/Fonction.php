<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fonction extends Model
{
    protected $table = 'fonctions';
    public $timestamps = false;

    protected $fillable = ['fonction_name'];

    public function sousfonctions()
    {
        return $this->hasMany(Sousfonction::class, 'fonction_id');
    }
}
