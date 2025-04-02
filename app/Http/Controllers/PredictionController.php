<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="MRC-API",
 *     version="1.0",
 *     description="API pour la prédiction des maladies rénales chroniques"
 * )
 *
 * @OA\Post(
 *     path="/api/predict",
 *     summary="Prédire le stade de la maladie rénale chronique",
 *     tags={"Prédiction"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"creatinine", "gfr", "albumin"},
 *             @OA\Property(property="creatinine", type="number", format="float", example=1.5),
 *             @OA\Property(property="gfr", type="number", format="float", example=50),
 *             @OA\Property(property="albumin", type="number", format="float", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Prédiction réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="predicted_stage", type="string", example="Stade 3a - Insuffisance rénale modérée")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     )
 * )
 */

class PredictionController extends Controller
{
    public function predict(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'creatinine' => 'required|numeric|min:0',
            'gfr' => 'required|numeric|min:0',
            'albumin' => 'required|numeric|min:0',
        ]);

        $gfr = $request->input('gfr');
        $creatinine = $request->input('creatinine');
        $albumin = $request->input('albumin');

        // Détermination du stade basé sur le GFR
        if ($gfr >= 90) {
            $stage = "Stade 1 - Fonction rénale normale";
        } elseif ($gfr >= 60) {
            $stage = "Stade 2 - Insuffisance rénale légère";
        } elseif ($gfr >= 45) {
            $stage = "Stade 3a - Insuffisance rénale modérée";
        } elseif ($gfr >= 30) {
            $stage = "Stade 3b - Insuffisance rénale modérée avancée";
        } elseif ($gfr >= 15) {
            $stage = "Stade 4 - Insuffisance rénale sévère";
        } else {
            $stage = "Stade 5 - Insuffisance rénale terminale (Dialyse)";
        }

        // Ajout d'une précision avec le taux d'albumine (optionnel)
        if ($albumin > 300) {
            $stage .= " avec forte albuminurie (protéinurie élevée)";
        }

        return response()->json([
            // 'creatinine' => $creatinine,
            // 'gfr' => $gfr,
            // 'albumin' => $albumin,
            'predicted_stage' => $stage
        ]);
    }
}
