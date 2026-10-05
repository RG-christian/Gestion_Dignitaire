<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HistoricalArchiveController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'dignitaires' => DB::table('dignitaire')->whereNotNull('deleted_at')->select('id', DB::raw("CONCAT(prenom, ' ', nom) as libelle"), 'deleted_at')->get(),
            'nominations' => DB::table('nominations')->whereNotNull('deleted_at')->select('id', DB::raw("COALESCE(fonction, CONCAT('Nomination #', id)) as libelle"), 'deleted_at')->get(),
            'postes' => DB::table('postes')->whereNotNull('deleted_at')->select('id', 'intitule as libelle', 'deleted_at')->get(),
            'affectations' => DB::table('affectations')->whereNotNull('deleted_at')->select('id', DB::raw("COALESCE(type_affectation, CONCAT('Affectation #', id)) as libelle"), 'deleted_at')->get(),
            'conjoints' => DB::table('conjoints')->whereNotNull('deleted_at')->select('id', DB::raw("CONCAT(prenom, ' ', nom) as libelle"), 'deleted_at')->get(),
            'decoration-attributions' => DB::table('decoration_dignitaire as dd')->join('decoration as d', 'd.deco_id', '=', 'dd.decoration_id')->whereNotNull('dd.deleted_at')->select('dd.id', 'd.deco_nom as libelle', 'dd.deleted_at')->get(),
        ]);
    }
}
