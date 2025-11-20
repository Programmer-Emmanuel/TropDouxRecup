<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AbonnementController extends Controller
{
    public function ajout_abonnement(Request $request){
        $validator = Validator::make($request->all(),[
            'type_abonnement' => 'required',
            'montant' => 'required',
            'duree' => 'required|in:mois,illimite,trimestre,semestre,annee',
            'avantages' => 'required|array',
            'avantages.*' => 'uuid|exists:avantages,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ],422);
        }

        try{
            $abonnement = new Abonnement();
            $abonnement->type_abonnement = $request->type_abonnement;
            $abonnement->montant = $request->montant;
            $abonnement->duree = $request->duree;
            $abonnement->save();

            $abonnement->avantages()->attach($request->avantages);

            $avantages = $abonnement->avantages()->select('avantages.id', 'avantages.nom_avantage')->get()->makeHidden('pivot');


            return response()->json([
                'success' => true,
                'data' => [
                    'abonnement' => [
                        'id' => $abonnement->id,
                        'type_abonnement' => $abonnement->type_abonnement,
                        'montant' => $abonnement->montant,
                        'duree' => $abonnement->duree
                    ],
                    'avantages' => $avantages
                    ],
                'message' => 'Abonnement créé avec succès',
            ]);
        }
        catch(QueryException $e){
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’ajout de l’abonnement',
                'erreur' => $e->getMessage()
            ],500);
        }
    }

    public function liste_abonnement(){
        try{
            $abonnement = Abonnement::select('id', 'type_abonnement', 'montant', 'duree')->with(['avantages:id,nom_avantage'])->get();
            $abonnement->each(function ($a) {
                $a->avantages->makeHidden('pivot');
            });
            
            return response()->json([
                'success' => true,
                'data' => $abonnement,
                'message' => 'Liste des abonnements affichés avec succès.'
            ],200);

        }
        catch(QueryException $e){
            return response()->json([
                'success' => true,
                'message' => 'Echec lors de l’affichage de la liste des abonnements',
                'erreur' => $e->getMessage()
            ],500);
        }
    }

    public function update_abonnement(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'type_abonnement' => 'required',
            'montant' => 'required',
            'duree' => 'required|in:mois,illimite,trimestre,semestre,annee',
            'avantages' => 'required|array',
            'avantages.*' => 'uuid|exists:avantages,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ],422);
        }

        try{
            $abonnement = Abonnement::find($id);
            if(!$abonnement){
                return response()->json([
                    'success' => false,
                    'message' => 'Abonnement non trouvé'
                ],404);
            }
            $abonnement->type_abonnement = $request->type_abonnement;
            $abonnement->montant = $request->montant;
            $abonnement->duree = $request->duree;
            $abonnement->save();

            $abonnement->avantages()->sync($request->avantages);

            $avantages = $abonnement->avantages()->select('avantages.id', 'avantages.nom_avantage')->get()->makeHidden('pivot');
            

            return response()->json([
                'success' => true,
                'data' => [
                    'abonnement' => [
                        'id' => $abonnement->id,
                        'type_abonnement' => $abonnement->type_abonnement,
                        'montant' => $abonnement->montant,
                        'duree' => $abonnement->duree
                    ],
                    'avantages' => $avantages
                    ],
                'message' => 'Abonnement modifié avec succès',
            ],200);
        }
        catch(QueryException $e){
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification de l’abonnement',
                'erreur' => $e->getMessage()
            ],500);
        }
    }

    public function delete_abonnement($id){
        try{
            $abonnement = Abonnement::find($id);
            if(!$abonnement){
                return response()->json([
                    'success' => false,
                    'message' => 'Abonnement non trouvé'
                ],404);
            }

            $abonnement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Abonnement supprimé avec succès'
            ],200);
        }
        catch(QueryException $e){
            return response()->json([
                'success' => true,
                'message' => 'Erreur lors de la suppression de l’abonnenemt',
                'erreur' => $e->getMessage()
            ],500);
        }
    }
}
