<?php

namespace App\Http\Controllers;

use App\Models\Marchand;
use App\Models\Plat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MarchandController extends Controller
{
    public function marchand(Request $request, $id){
        try {
            $marchand = Marchand::find($id);

            if (!$marchand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Marchand non trouvé'
                ], 404);
            }

            $plats = Plat::where('id_marchand', $id)
                ->where('is_active', true)
                ->get();

            if ($plats->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $marchand->id,
                        'nom_marchand' => $marchand->nom_marchand,
                        'localite' => $marchand->commune->localite ?? null,
                        'plat_restant' => 0,
                        'pourcentage' => 0,
                        'plats_dispo' => []
                    ],
                    'message' => 'Aucun plat disponible'
                ], 200);
            }

            $plats_dispo = $plats->map(function ($plat) {
                $pourcentage = $plat->prix_origine > 0 
                    ? round((($plat->prix_origine - $plat->prix_reduit) / $plat->prix_origine) * 100, 2)
                    : 0;

                return [
                    'nom_plat' => $plat->nom_plat,
                    'image_couverture' => $plat->image_couverture,
                    'quantite_plat' => $plat->quantite_plat,
                    'prix_origine' => $plat->prix_origine,
                    'prix_reduit' => $plat->prix_reduit,
                    'reduction_percent' => $pourcentage
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $marchand->id,
                    'nom_marchand' => $marchand->nom_marchand,
                    'localite' => $marchand->commune->localite ?? null,
                    'plat_restant' => $plats->count(),
                    'pourcentage' => $plats_dispo->avg('reduction_percent') . "%",
                    'plats_dispo' => $plats_dispo
                ],
                'message' => 'Informations du marchand'
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’affichage du marchand',
                'erreur' => $e->getMessage()
            ], 500);
        }
    }


}
