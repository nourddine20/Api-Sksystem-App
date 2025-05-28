<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Http\Controllers\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\CityRamasageCity;
use App\Models\RamasageCity;
use App\Models\User;
use App\Models\Statu;
use Illuminate\Support\Facades\DB;


class ParcelController extends Controller
{
    
    
//   public function welcomeData()
//     {
//         // Mock data representing the content for the welcome screen
//         $welcomeData = [
//             [
//                 'title' => 'Nos Avantages',
//                 'body' => "Ramassage Gratuit, Retour Gratuit, Stockage gratuit , Livraison en 24h partout auMaroc. \n A chaque client professionnel ou particulier une offre de prestation personnalisée et complète.",
//                 'image' => 'img2.png'
//             ],
//             [
//                 'title' => 'Livrez Plus, Économisez Plus !',
//                 'body' => "Remises Avantageuses : Chaque livraison \n effectuée avec Sksystem Assure vous\n rapproche de remises exclusives. Plus vous \n livrez, plus vous économisez.\n Points Cadeaux : Accumulez des points à \n chaque livraison, échangeables contre une\n variété de cadeaux attractifs. Votre fidélité\n mérite d'être récompensée.",
//                 'image' => 'img4.png'
//             ],
//             [
//                 'title' => 'Support 24/7',
//                 'body' => "Que ce soit une question, une préoccupation ou \n un besoin d'assistance, notre équipe est\n disponible 24 heures sur 24, 7 jours sur 7 \n Nous sommes là pour vous, à tout moment.",
//                 'image' => 'img3.png'
//             ]
//         ];

//         return response()->json($welcomeData);
//     }
    
    
    public function getParcelClaim(Request $request){
        
          $choose_parcel_id = $request->parcel_id;
        
      
          try {
     
               $claim_parcel = DB::table('delevery_claims')
                    ->where('claims_customer', Auth::user()->customers_id)
                    ->whereNotNull('parcel_id')
                    ->where('parcel_id', $choose_parcel_id)
                    ->get();
                
                $claim_parcel_count = DB::table('delevery_claims')
                    ->where('claims_customer', Auth::user()->customers_id)
                    ->whereNotNull('parcel_id')
                    ->where('parcel_id', $choose_parcel_id)
                    ->count();

            
         
   
          
                    if ($claim_parcel_count > 0 ) {
                        
                       
                        return response([
                            'data'=>$claim_parcel[0],
                            'message' => 'The Parcel was deleted successfully',
                        ], 200);
                        
                        
                    } else {
                        return response([
                            'message' => 'Oops! The Parcel was not deleted',
                        ], 500);
                    }
                    
                    
                } catch (\Exception $e) {
                    // Log the exception for debugging
                    \Log::error($e);
            
                    return response([
                        'message' => 'Internal Server Error',
                        'error' => $e->getMessage()
                    ], 500);
                }
        
    }
    
    public function checknewParcels(){
        
         try {
     
     $check_parcel_count = DB::table('delevery_parcel')
    ->where([
        'parcel_status' => "NEW_PARCEL",
        'parcel_customer' => Auth::user()->customers_id
    ])->count();


        if ($check_parcel_count > 0) {
            
           
            return response([
                'message' => 'The Parcel was deleted successfully',
            ], 200);
            
            
        } else {
            return response([
                'message' => 'Oops! The Parcel was not deleted',
            ], 500);
        }
        
        
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
        
    }
    
   public function GetParcels(Request $request)
{
    // Authenticate user
    if (!Auth::check()) { 
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    try {
        $customerId = Auth::id();  // Use Auth::id() to get the authenticated user's ID

        $parcels = DB::table('delevery_parcel')
            ->join('delevery_cities', 'delevery_parcel.parcel_city', '=', 'delevery_cities.cities_id')
            ->join('delevery_parcel_history', function ($join) {
                $join->on('delevery_parcel.parcel_id', '=', 'delevery_parcel_history.parcel_history_parcel')
                     ->whereColumn('delevery_parcel_history.parcel_history_id', '=', DB::raw("(SELECT MAX(parcel_history_id) FROM delevery_parcel_history WHERE parcel_history_parcel = delevery_parcel.parcel_id)"));
            })
            ->leftJoin('delevery_claims', 'delevery_parcel.parcel_id', '=', 'delevery_claims.parcel_id')
            ->where('delevery_parcel.parcel_customer', $customerId)
            ->select('delevery_parcel.*', 'delevery_cities.cities_nom', 'delevery_parcel_history.parcel_history_status', 'delevery_parcel_history.parcel_history_data AS reported_date', 'delevery_claims.*')
            ->orderByDesc('delevery_parcel_history.parcel_history_id')
            ->get();

        return response()->json([
            'message' => 'Parcel data retrieved successfully',
            'data' => $parcels
        ], 200);

    } catch (\Exception $e) {
        Log::error($e->getMessage());

        return response()->json([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    
  public function deleteparcel(Request $request)
{
    try {
        $choose_parcel_id = $request->id_parcel;
        $choose_parcel_statut_code = $request->parcel_statut_code;

        $deleted_parcel = DB::table('delevery_parcel')
            ->where([
                'parcel_id' => $choose_parcel_id,
                'parcel_customer' => Auth::user()->customers_id
            ])->delete();


        if ($deleted_parcel) {
            
            if($choose_parcel_statut_code == "WAITING_PICKUP"){
                
                Parcel::removeParcelFromDn($choose_parcel_id);
            }
            return response([
                'message' => 'The Parcel was deleted successfully',
            ], 200);
        } else {
            return response([
                'message' => 'Oops! The Parcel was not deleted',
            ], 500);
        }
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    
    public function updatestatut(Request $request){
        
        $choose_statut = $request->status;
          $choose_parcel_id = $request->parcel;
          $parcel_city = Parcel::getParceInfo("city",$choose_parcel_id) ?? NULL;
         $parcel_livreur = Parcel::getParceInfo("livreur",$choose_parcel_id) ?? NULL;
                
       
        
        if($choose_statut == "ONCALL"){
            
           
            $res = Parcel::addParcelHistory($choose_parcel_id,$parcel_city,"NOT_PAID","ONCALL",null,0);
            
            var_dump($parcel_city);
            
        }elseif($choose_statut == "ONCALL_DELIVERY"){
             
            $res = Parcel::addParcelHistory($request->parcel,$parcel_city,"NOT_PAID","ONCALL_DELIVERY",$parcel_livreur,0);
            
        }
        
        
        if($res){
            
             return response([
            'message' => 'The statut  was changed successfully',
          
             ], 200);
        
        }
        

        
       return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
        
       
        
    }

    
 public function getdeliveredparcels(){
    try {
        $customerId = Auth::user()->customers_id;

        $DeliveredParcels = DB::table('delevery_parcel')
            ->join('delevery_cities', 'delevery_parcel.parcel_city', '=', 'delevery_cities.cities_id')
            ->join('delevery_parcel_history', function ($join) {
                $join->on('delevery_parcel.parcel_id', '=', 'delevery_parcel_history.parcel_history_parcel')
                     ->whereColumn('delevery_parcel_history.parcel_history_id', '=', DB::raw("(SELECT MAX(parcel_history_id) FROM delevery_parcel_history WHERE parcel_history_parcel = delevery_parcel.parcel_id)"));
            })
            ->where('delevery_parcel.parcel_customer', $customerId)
            ->where('delevery_parcel.parcel_status','DELIVERED')
            ->select('delevery_parcel.*', 'delevery_cities.cities_nom', 'delevery_parcel_history.parcel_history_status', 'delevery_parcel_history.parcel_history_data AS reported_date')
            ->orderByDesc('delevery_parcel_history.parcel_history_id')
            ->get();
     
        return response([
            'message' => 'The Statistics data was retrieved successfully',
            'data' => $DeliveredParcels
        ], 200);
        
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    
  public function getdistrubitionparcels(){
    try {
        $customerId = Auth::user()->customers_id;

        $DistrubitionParcels = DB::table('delevery_parcel')
            ->join('delevery_cities', 'delevery_parcel.parcel_city', '=', 'delevery_cities.cities_id')
            ->join('delevery_parcel_history', function ($join) {
                $join->on('delevery_parcel.parcel_id', '=', 'delevery_parcel_history.parcel_history_parcel')
                     ->whereColumn('delevery_parcel_history.parcel_history_id', '=', DB::raw("(SELECT MAX(parcel_history_id) FROM delevery_parcel_history WHERE parcel_history_parcel = delevery_parcel.parcel_id)"));
            })
            ->where('delevery_parcel.parcel_customer', $customerId)
            ->whereIn('delevery_parcel.parcel_status', ['WAITING_PICKUP', 'NEW_PARCEL'])
            ->select('delevery_parcel.*', 'delevery_cities.cities_nom', 'delevery_parcel_history.parcel_history_status', 'delevery_parcel_history.parcel_history_data AS reported_date')
            ->orderByDesc('delevery_parcel_history.parcel_history_id')
            ->get();

        return response([
            'message' => 'The Distribution parcels data was retrieved successfully',
            'data' => $DistrubitionParcels
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error($e);
        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}



    
  public function getpreparationparcels(){
    try {
        $customerId = Auth::user()->customers_id;

        $DistrubitionParcels = DB::table('delevery_parcel')
            ->join('delevery_cities', 'delevery_parcel.parcel_city', '=', 'delevery_cities.cities_id')
            ->join('delevery_parcel_history', function ($join) {
                $join->on('delevery_parcel.parcel_id', '=', 'delevery_parcel_history.parcel_history_parcel')
                     ->whereColumn('delevery_parcel_history.parcel_history_id', '=', DB::raw("(SELECT MAX(parcel_history_id) FROM delevery_parcel_history WHERE parcel_history_parcel = delevery_parcel.parcel_id)"));
            })
            ->where('delevery_parcel.parcel_customer', $customerId)
            ->whereIn('delevery_parcel.parcel_status', ['PICKED_UP', 'SENT','RECEIVED'])
            ->select('delevery_parcel.*', 'delevery_cities.cities_nom', 'delevery_parcel_history.parcel_history_status', 'delevery_parcel_history.parcel_history_data AS reported_date')
            ->orderByDesc('delevery_parcel_history.parcel_history_id')
            ->get();

        return response([
            'message' => 'The Distribution parcels data was retrieved successfully',
            'data' => $DistrubitionParcels
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error($e);
        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}





    
      public function getinprogressparcels(){
          try {
         $allParcels = DB::table('delevery_parcel')->where(['parcel_status'=>"IN_PROGRESS",'parcel_customer'=> Auth::user()['customers_id']])->get();
            
           
         
        return response([
            'message' => 'The Statistics data was retrieved successfully',
            'data' => $allParcels
        ], 200);
        
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
    }
    
    
    public function filterparcelsbystatus(Request $request){
        
      
       
      try {
            $result = DB::table('delevery_parcel')
        ->select('delevery_parcel.*')
        ->where('delevery_parcel.parcel_customer', Auth::user()['customers_id'])
        ->where(function ($query) use ($request) {
            $query->where('delevery_parcel.parcel_status', $request->with_status)
                ->orWhere('delevery_parcel.parcel_status_second', $request->with_status);
        })
        ->get();



       
            return response([
                'message' => 'Parcels data was retrieved successfully',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            \Log::error($e);
        
            return response([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
   
    }
    
    
  public function filterparcelsbysearch(Request $request)
{
    try {
        
        
        $result = DB::table('delevery_parcel')
            ->select('delevery_parcel.*')
            ->where(function ($query) use ($request) {
                $query->where('delevery_parcel.parcel_code', $request->filter_by)
                    ->orWhere('delevery_parcel.parcel_phone', $request->filter_by);
            })
            ->where('delevery_parcel.parcel_customer', Auth::user()['customers_id'])
            ->get();

     

        return response([
            'message' => 'Parcels data was retrieved successfully',
            'data' => $result
        ], 200);
    } catch (\Exception $e) {
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}
    
    
    // public function filterparcelsbysearch(Request $request){
        
    //   var_dump('my req');
     
    //   var_dump($request->filter_by);
       
       
    //   try {
    //         $result = DB::table('delevery_parcel')
    //             ->select('delevery_parcel.*')
    //             ->where(['delevery_parcel.parcel_customer'=> Auth::user()['customers_id'],'delevery_parcel.parcel_code'=> $request->filter_by])->get();
        
    //     var_dump($result);
    //         return response([
    //             'message' => 'Parcels data was retrieved successfully',
    //             'data' => $result
    //         ], 200);
    //     } catch (\Exception $e) {
    //         \Log::error($e);
        
    //         return response([
    //             'message' => 'Internal Server Error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
   
    // }
    
      public function getcanceledparcels(){
          
    //     var_dump('get canceled');
          
    //          try {
    //     $db = \Config\Database::connect();

            
    //     $mysql_query = "SELECT delevery_parcel.*, delevery_cities.cities_nom, delevery_parcel_history.parcel_history_status, delevery_parcel_history.parcel_history_data AS reported_date
    //                     FROM delevery_cities, delevery_parcel
    //                     INNER JOIN (
    //                         SELECT parcel_history_parcel, MAX(parcel_history_id) AS max_history_id
    //                         FROM delevery_parcel_history
    //                         GROUP BY parcel_history_parcel
    //                     ) AS max_history ON delevery_parcel.parcel_id = max_history.parcel_history_parcel
    //                     INNER JOIN delevery_parcel_history ON max_history.parcel_history_parcel = delevery_parcel_history.parcel_history_parcel
    //                         AND max_history.max_history_id = delevery_parcel_history.parcel_history_id
    //                     WHERE delevery_parcel.parcel_city = delevery_cities.cities_id
    //                         AND delevery_parcel.parcel_customer = ?
    //                         AND (
    //                             delevery_parcel.parcel_status = 'RETURNED'
    //                             OR delevery_parcel.parcel_status = 'IN_PROGRESS'
    //                         )
    //                         AND (
    //                             delevery_parcel.parcel_status_second = 'CANCELED'
    //                             OR delevery_parcel.parcel_status_second ='REFUSE'
    //                             OR delevery_parcel.parcel_status_second ='OUT_OF_AREA'
    //                         )
    //                     ORDER BY delevery_parcel_history.parcel_history_id DESC;";

    //     $query = $db->query($mysql_query, [Auth::user()['customers_id']]);
    //     $result = $query->getResultArray();

    //     var_dump($result);
    //     return response([
    //         'message' => 'The canceled parcels data was retrieved successfully',
    //         'data' => $result
    //     ], 200);

    // } catch (\Exception $e) {
    //     \Log::error($e);

    //     return response([
    //         'message' => 'Internal Server Error',
    //         'error' => $e->getMessage()
    //     ], 500);
    // }
    
    
        
        try {
         $allParcels = DB::table('delevery_parcel')->where(['parcel_status'=>"CANCELED" ,'parcel_customer'=> Auth::user()['customers_id']])->get();
            
           
          
         
        return response([
            'message' => 'The Statistics data was retrieved successfully',
            'data' => $allParcels
        ], 200);
        
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
    
    }
    
    
    public function trackingparcel(Request $request){
         try {
     
    
       
        $parcel_id = Parcel::getParcelId($request->parcel_code);
        $parcel_histories = [];
        
     
        if($parcel_id){
          
      
    $parcel_histories = DB::table('delevery_parcel_history')
    ->where('parcel_history_parcel', '=', $parcel_id)
    ->where('parcel_history_situation', '!=', 'INVOICED')
    ->get();
    
         
            if($parcel_histories != [] && $parcel_histories != null ){
                $fullarray_histories = [];
                foreach($parcel_histories as $item){
                   
                  
                   $time_history_parcel = date("Y-m-d", $item->parcel_history_time);
                   
                  
                   
                    $array= [];
                    $array = [
                        
                    'parcel_history_id'=>$item->parcel_history_id,
                    'parcel_history_code'=>$request->parcel_code,
                    'parcel_history_time'=>$time_history_parcel,
                    'parcel_history_statu_name'=>Statu::getstatu($item->parcel_history_status,'name'),
                    'parcel_history_statu_color'=>Statu::getstatu($item->parcel_history_status,'color'),
                    'parcel_history_situation_name'=>Statu::getstatu($item->parcel_history_situation,'name'),
                    'parcel_history_situation_color'=>Statu::getstatu($item->parcel_history_situation,'color'),
                    'parcel_history_livreur'=>Parcel::getParceHistoryInfo('livreur',$item->parcel_history_id),
                    'parcel_history_comment'=>Parcel::getParceHistoryInfo('comment',$item->parcel_history_id)
                    
                    ];
                    
                    array_push($fullarray_histories,$array);
                }
                
            
                  return response([
                'message' => 'The tracking parcel is work with Success',
                'data'=>$fullarray_histories
            ], 200);
            }else{
                  return response([
            'message' => 'Colis Historique Introuveble',
            
        ], 500);
                
            }
            
        
        }else{
             return response([
            'message' => 'Colis Introuveble',
           
        ], 500);
        
        
        }
    
            
         } catch (\Exception $e) {
      
        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
    
    
    }
  
  
  public function getActiveCities(){
      
    try {
            
         $allActiveCities = DB::table('delevery_cities')->where('statut',1)->get();
            
            
              
        return response([
            'message' => 'The Cities data was retrieved successfully',
            'data' => $allActiveCities
        ], 200);
        
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
    
    
  }
  
   public function CreateParcel(Request $request){

           
          
         
    $fields = $request->validate([
       
        'parcel_receiver' => 'required|string',
        'parcel_phone' => 'required|string',
        'parcel_city' => 'required|integer',
        'parcel_address' => 'required|string',
        'parcel_price' => 'required|integer',
        'parcel_product_name' => 'required|string',
        'parcel_product_qty' => 'required|integer'
    ]);



    if(Custom::checkPhone($fields['parcel_phone']) == 0){
        return response([
            'message' => "Error: Le téléphone n'est pas valide"
        ], 401);
    }  

    if(City::getCityStatut($fields['parcel_city']) != 1 || City::checkCity($fields['parcel_city']) == 0){
        return response([
            'message' => "Error:  Veuillez choisir une ville"
        ], 401); 
    }

    if(Custom::checkCityRamasageStatut(Auth::user()['customers_id']) == 0){
        return response([
            'message' => "Error: Votre Ville de Ramassage a été Désactivée, Contactez le Support"
        ], 401);
      
    }


    if(User::customer_info('customers_delivered_price',Auth::user()['customers_id']) != 0){
        $parcel_delivered_fees =  User::customer_info('customers_delivered_price',Auth::user()['customers_id']);
        }else{

            if(Auth::user()['customers_isactive_pers'] == 1){
              $parcel_delivered_fees = CityRamasageCity::getCityFeesByName($fields['parcel_city'],Auth::user()['customers_ramassage_cities_id'],Auth::user()['customers_id'],'prix_livraison');
            }else{
                $parcel_delivered_fees = CityRamasageCity::getCityFeesByName($fields['parcel_city'],Auth::user()['customers_ramassage_cities_id'],null,'prix_livraison');
            }
           
           
        }
    // if(User::customer_info('customers_delivered_price',Auth::user()['customers_id']) != 0){
      
    //     $parcel_delivered_fees =  User::customer_info('customers_delivered_price',Auth::user()['customers_id']);
    // }else{
    //     $parcel_delivered_fees  = City::getcityFees($fields['parcel_city'],Auth::user()['customers_id']);
    // }
 
    if(User::customer_info('customers_returned_price',Auth::user()['customers_id']) != 0){
        $parcel_returned_fees =  User::customer_info('customers_returned_price',Auth::user()['customers_id']);
        }else{

            if(Auth::user()['customers_isactive_pers'] == 1){
              $parcel_returned_fees = CityRamasageCity::getCityFeesByName($fields['parcel_city'],Auth::user()['customers_ramassage_cities_id'],Auth::user()['customers_id'],'prix_retour');
            }else{
                $parcel_returned_fees = CityRamasageCity::getCityFeesByName($fields['parcel_city'],Auth::user()['customers_ramassage_cities_id'],null,'prix_retour');
            }
           
           
        }


        if(User::customer_info('customers_refused_price',Auth::user()['customers_id']) != 0){
            $parcel_refused_fees =  User::customer_info('customers_refused_price',Auth::user()['customers_id']);
 
            }else{
    
                if(Auth::user()['customers_isactive_pers'] == 1){
                  $parcel_refused_fees = CityRamasageCity::getCityFeesByName($fields['parcel_city'],Auth::user()['customers_ramassage_cities_id'],Auth::user()['customers_id'],'prix_refuse');
                }else{
                    $parcel_refused_fees = CityRamasageCity::getCityFeesByName($fields['parcel_city'],Auth::user()['customers_ramassage_cities_id'],null,'prix_refuse');
                }
               
               
            }
 
 
 


    $parcel = Parcel::create([
       
      
        'parcel_receiver' => $fields['parcel_receiver'],
        'parcel_phone' => $fields['parcel_phone'],
        'parcel_city' => $fields['parcel_city'],
        'parcel_address' => $fields['parcel_address'],
        'parcel_agence' => $request->parcel_agence,
        'parcel_ramassage_city' => $request->parcel_ramassage_city,
        'parcel_peux_ovrire' => $request->parcel_peux_ovrire,
        'parcel_fragile' => $request->parcel_fragile,
        'parcel_district' => $request->parcel_district,
        'parcel_price' => $fields['parcel_price'] ?? 0,
     
        'parcel_note' => $request->parcel_note,
        'parcel_situation' => 'NOT_PAID',
        'parcel_status' => 'NEW_PARCEL',
        'parcel_delivered_fees' =>$parcel_delivered_fees ?? 0,
        'parcel_returned_fees' => $parcel_returned_fees ?? 0,
        'parcel_refused_fees' => $parcel_refused_fees ?? 0,
        'parcel_customer' => Auth::user()['customers_id'],
        'parcel_time' => time(),
        'parcel_product_name' => $fields['parcel_product_name'],
        'parcel_product_qty' => $fields['parcel_product_qty'] ?? 0,
        'parcel_replace' => $request->parcel_replace ?? 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

     if ($parcel) {
            $new_parcel_code = Parcel::newParcelCode(
                City::getcityCode($fields['parcel_city']),  // Call the model function
                Auth::user()['customers_id'],
                $parcel->parcel_id
            );

            Parcel::find($parcel->parcel_id)->update([
                'parcel_code' => $new_parcel_code,
            ]);

            if(User::customer_info("stock",Auth::user()['customers_id']) == 1){
                Parcel::find($parcel->parcel_id)->update([
                    'parcel_from_stock' => $request->parcel_from_stock,
                ]);
            }

            Parcel::addParcelHistory($parcel->parcel_id,$fields['parcel_city'],"NOT_PAID","NEW_PARCEL",NULL,0);

            return response([
                'message' => 'The parcel was created with Success'
            ], 201);
        } else {
            return response([
                'message' => 'Error: Cannot create your parcel'
            ], 401);
        }
  
    }
}
