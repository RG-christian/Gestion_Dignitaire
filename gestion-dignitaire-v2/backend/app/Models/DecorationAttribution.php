<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DecorationAttribution extends Model
{
    use SoftDeletes;
    protected $table = 'decoration_dignitaire';
    public $timestamps = false;

    protected $fillable = [
        'dignitaire_id', 'decoration_id', 'date_attribution', 'poste_id',
        'attestation_path', 'attestation_nom_original', 'attestation_mime',
        'attestation_taille', 'attestation_sha256', 'attestation_legacy',
    ];

    protected $casts = ['date_attribution' => 'date'];
}
