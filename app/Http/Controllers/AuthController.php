<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Abonnement;
use App\Models\Marchand;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class AuthController extends Controller
{
    //Authentification Marchand

    //Inscription
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'email' => 'required|email',
            'telephone' => 'required|digits:10',
            'password' => 'required|min:4',
            'role' => 'required|string|in:marchand,client'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            if($request->role == 'marchand'){
                $marchand = Marchand::where('email_marchand', $request->email)
                ->orWhere('tel_marchand', $request->telephone)
                ->first();
            
                $code_otp = rand(1000, 9999);

                if ($marchand) {
                    if ($marchand->is_verify) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cet email ou numéro de téléphone est déjà vérifié.'
                        ], 409);
                    }

                    // Mise à jour si le marchand n'est pas vérifié
                    $marchand->update([
                        'nom_marchand' => $request->nom,
                        'password_marchand' => Hash::make($request->password),
                        'code_otp' => $code_otp,
                        'otp_expire_at' => now()->addMinutes(10),
                    ]);

                } else {
                    // Création d'un nouveau compte
                    $marchand = Marchand::create([
                        'nom_marchand' => $request->nom,
                        'email_marchand' => $request->email,
                        'tel_marchand' => $request->telephone,
                        'password_marchand' => Hash::make($request->password),
                        'code_otp' => $code_otp,
                        'otp_expire_at' => now()->addMinutes(10),
                    ]);
                }

                try{
                    Mail::to($marchand->email_marchand)->send(new OtpMail($code_otp, $marchand));
                }
                catch(QueryException $e){
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lors de l’envoi d’email',
                        'erreur' => $e->getMessage()
                    ],500);
                }

                return response()->json([
                    'success' => true,
                    'role' => 'marchand',
                    'message' => 'Inscription réussie. Un nouveau code OTP a été envoyé par mail.',
                ], 200);
            }
            $client = new User();
            $client->nom_client = $request->nom;
            $client->email_client = $request->email;
            $client->tel_client = $request->telephone;
            $client->password_client = Hash::make($request->password);
            $client->save();

            $token = $client->createToken('ClientToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'data' => [
                    'nom' => $client->nom_client,
                    'email' => $client->email_client,
                    'telephone' => $client->tel_client,
                    'password' => $client->password_client,
                    'token' => $token,
                    'role' => 'client'
                ],
                'message' => 'Inscription reussie'
                ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’inscription',
                'erreur' => $e->getMessage()
            ], 500);
        }
    }


public function verify_otp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email_marchand' => 'required|email',
        'code_otp' => 'required|digits:4'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first()
        ], 422);
    }

    try {
        $marchand = Marchand::where('email_marchand', $request->email_marchand)->first();

        if (!$marchand) {
            return response()->json([
                'success' => false,
                'message' => 'Marchand introuvable'
            ], 404);
        }

        if ($marchand->is_verify) {
            return response()->json([
                'success' => false,
                'message' => 'Cet email est déjà vérifié'
            ], 409);
        }

        if (
            hash_equals($marchand->code_otp, $request->code_otp) &&
            Carbon::parse($marchand->otp_expire_at)->isFuture()
        ) {

            $token = $marchand->createToken('MarchandToken')->plainTextToken;
            $abonnement = Abonnement::where('type_abonnement', 'debutant')->first();
            $marchand->update([
                'is_verify' => true,
                'code_otp' => null,
                'otp_expire_at' => null,
                'id_abonnement' => $abonnement->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vérification réussie',
                'data' => [
                    'nom_marchand' => $marchand->nom_marchand,
                    'email_marchand' => $marchand->email_marchand,
                    'tel_marchand' => $marchand->tel_marchand,
                    'type_abonnement' => $abonnement->type_abonnement,
                    'token' => $token
                ],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Code OTP invalide ou expiré'
        ], 403);

    } catch (QueryException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur lors de la vérification',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function resend_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'email_marchand' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $marchand = Marchand::where('email_marchand', $request->email_marchand)->first();

        if (!$marchand) {
            return response()->json([
                'success' => false,
                'message' => 'Marchand introuvable'
            ], 404);
        }

        if ($marchand->is_verify) {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte a déjà été vérifié.'
            ], 409);
        }

        $code_otp = rand(1000, 9999);

        $marchand->update([
            'code_otp' => $code_otp,
            'otp_expire_at' => now()->addMinutes(10),
        ]);

        Mail::to($marchand->email_marchand)->send(new OtpMail($code_otp, $marchand));

        return response()->json([
            'success' => true,
            'message' => 'Nouveau code OTP envoyé.'
        ]);
    }
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'L’email est obligatoire',
            'email.email' => 'L’email doit être de type email',
            'password.required' => 'Le mot de passe est obligatoire'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $marchand = Marchand::where('email_marchand', $request->email)->first();
            if ($marchand && Hash::check($request->password, $marchand->password_marchand)) {
                $token = $marchand->createToken('MarchandToken')->plainTextToken;
                $marchand->load('abonnement');
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'data' => [
                        'nom' => $marchand->nom_marchand,
                        'email' => $marchand->email_marchand,
                        'telephone' => $marchand->tel_marchand,
                        'role' => 'marchand',
                        'type_abonnement' => $marchand->abonnement->type_abonnement,
                        'token' => $token
                    ]
                ], 200);
            }

            $client = User::where('email_client', $request->email)->first();
            if ($client && Hash::check($request->password, $client->password_client)) {
                $token = $client->createToken('ClientToken')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'data' => [
                        'nom' => $client->nom_client,
                        'email' => $client->email_client,
                        'telephone' => $client->tel_client,
                        'role' => 'client',
                        'token' => $token
                    ]
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects',
            ], 401);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
                'erreur' => $e->getMessage()
            ], 500);
        }
    }

}
