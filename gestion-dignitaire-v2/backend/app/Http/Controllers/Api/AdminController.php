<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Fonction;
use App\Models\Sousfonction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Liste de tous les utilisateurs avec leurs droits
     */
    public function index(): JsonResponse
    {
        $users = DB::select("
            SELECT 
                u.id,
                u.username,
                u.nom_complet,
                u.email,
                u.role_id,
                r.role_name,
                (SELECT GROUP_CONCAT(f.fonction_name SEPARATOR ', ')
                 FROM user_fonctions uf
                 JOIN fonctions f ON uf.fonction_id = f.id
                 WHERE uf.user_id = u.id
                ) AS fonctions,
                (SELECT GROUP_CONCAT(sf.sousfonction_name SEPARATOR ', ')
                 FROM user_sousfonctions usf
                 JOIN sousfonctions sf ON usf.sousfonction_id = sf.id
                 WHERE usf.user_id = u.id
                ) AS sousfonctions
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id DESC
        ");

        return response()->json($users);
    }

    /**
     * Liste des rôles
     */
    public function roles(): JsonResponse
    {
        $roles = Role::orderBy('role_name')->get();
        return response()->json($roles);
    }

    /**
     * Liste des fonctions
     */
    public function fonctions(): JsonResponse
    {
        $fonctions = Fonction::orderBy('fonction_name')->get();
        return response()->json($fonctions);
    }

    /**
     * Liste des sous-fonctions avec leur fonction parente
     */
    public function sousfonctions(): JsonResponse
    {
        $sousfonctions = DB::table('sousfonctions as sf')
            ->select('sf.id', 'sf.sousfonction_name', 'sf.fonction_id', 'f.fonction_name')
            ->join('fonctions as f', 'sf.fonction_id', '=', 'f.id')
            ->orderBy('sf.sousfonction_name')
            ->get();

        return response()->json($sousfonctions);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'nom_complet' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'fonctions' => 'required|array|min:1',
            'fonctions.*' => 'exists:fonctions,id',
            'sousfonctions' => 'required|array|min:1',
            'sousfonctions.*' => 'exists:sousfonctions,id',
        ]);

        DB::beginTransaction();
        try {
            // Créer l'utilisateur
            $user = User::create([
                'username' => $validated['username'],
                'nom_complet' => $validated['nom_complet'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
            ]);

            // Attacher les fonctions
            foreach ($validated['fonctions'] as $fonctionId) {
                DB::table('user_fonctions')->insert([
                    'user_id' => $user->id,
                    'fonction_id' => $fonctionId
                ]);
            }

            // Attacher les sous-fonctions
            foreach ($validated['sousfonctions'] as $sousfonctionId) {
                DB::table('user_sousfonctions')->insert([
                    'user_id' => $user->id,
                    'sousfonction_id' => $sousfonctionId
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'nom_complet' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'fonctions' => 'required|array|min:1',
            'fonctions.*' => 'exists:fonctions,id',
            'sousfonctions' => 'required|array|min:1',
            'sousfonctions.*' => 'exists:sousfonctions,id',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour l'utilisateur
            $user->username = $validated['username'];
            $user->nom_complet = $validated['nom_complet'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            
            $user->save();

            // Supprimer les anciennes associations
            DB::table('user_fonctions')->where('user_id', $id)->delete();
            DB::table('user_sousfonctions')->where('user_id', $id)->delete();

            // Attacher les nouvelles fonctions
            foreach ($validated['fonctions'] as $fonctionId) {
                DB::table('user_fonctions')->insert([
                    'user_id' => $id,
                    'fonction_id' => $fonctionId
                ]);
            }

            // Attacher les nouvelles sous-fonctions
            foreach ($validated['sousfonctions'] as $sousfonctionId) {
                DB::table('user_sousfonctions')->insert([
                    'user_id' => $id,
                    'sousfonction_id' => $sousfonctionId
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Supprimer les associations
            DB::table('user_fonctions')->where('user_id', $id)->delete();
            DB::table('user_sousfonctions')->where('user_id', $id)->delete();
            
            // Supprimer l'utilisateur
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}
