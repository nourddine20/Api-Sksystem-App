<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WelcomeController extends Controller
{
    public function welcomeData()
    {
        
        // var_dump('my name is nour');
        
        // Mock data representing the content for the welcome screen
        $welcomeData = [
            [
                'title' => 'Nos Avantages',
                'body' => "Ramassage Gratuit, Retour Gratuit, Stockage gratuit , Livraison en 24h partout auMaroc. \n A chaque client professionnel ou particulier une offre de prestation personnalisée et complète.",
                'image' => 'img1.png'
            ],
            [
                'title' => 'Livrez Plus, Économisez Plus !',
                'body' => "Remises Avantageuses : Chaque livraison \n effectuée avec Sksystem Assure vous\n rapproche de remises exclusives. Plus vous \n livrez, plus vous économisez.\n Points Cadeaux : Accumulez des points à \n chaque livraison, échangeables contre une\n variété de cadeaux attractifs. Votre fidélité\n mérite d'être récompensée.",
                'image' => 'img2.png'
            ],
            [
                'title' => 'Support 24/7',
                'body' => "Que ce soit une question, une préoccupation ou \n un besoin d'assistance, notre équipe est\n disponible 24 heures sur 24, 7 jours sur 7 \n Nous sommes là pour vous, à tout moment.",
                'image' => 'img3.png'
            ]
        ];

        // Get the base URL of the 'media' directory
        $baseUrl = asset('Media/assets/images/welcome');

        // Append the base URL to the 'image' field of each item
        foreach ($welcomeData as &$item) {
            $item['image'] = $baseUrl . '/' . $item['image'];
        }
        
      

        return response()->json($welcomeData);
    }
}
