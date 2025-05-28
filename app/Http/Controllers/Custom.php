<?php 

namespace App\Http\Controllers;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class Custom extends Controller
{

  

    function checkPhone($phoneNumber){

        if(strlen($phoneNumber) != 10){
            $checkPhone = 0;
        }
        elseif(substr($phoneNumber, 0, 1) != 0){
            $checkPhone = 0;    
        }
        elseif(substr($phoneNumber, 1, 1) != 5 && substr($phoneNumber, 1, 1) != 6 && substr($phoneNumber, 1, 1) != 7 && substr($phoneNumber, 1, 1) != 8){
            $checkPhone = 0;    
        }
        else{
            $checkPhone = 1;
    
        }
    
        return $checkPhone;
    }

     public function checkCityRamasageStatut($customer_id)
    {
        $customer = DB::table('delevery_customers')
            ->where('customers_id', $customer_id)
            ->first();
    
        if ($customer) {
            $ramassageCity = DB::table('delevery_ramassage_cities')
                ->where('ramassage_cities_id', $customer->customers_ramassage_cities_id)
                ->first();
    
            if ($ramassageCity) {
                return $ramassageCity->ramassage_cities_statut;
            }
        }
    
        return null;
    }

public function uploadimage($file,$path)
{
    $file_extension = $file->getClientOriginalExtension();
   
    $imageName = time() . '_' . mt_rand(1000, 9999);


    $file_name = $imageName.'.'.$file_extension;

    $file->move($path,$file_name);

   $imgdb = $path.$file_name;

   return $imgdb;

}


public function getImage(Request $request)
{
    $imagePath = public_path($request->imageName);
    $file = File::get($imagePath);
    $type = File::mimeType($imagePath);

    $response = response($file, 200)->header('Content-Type', $type);
    return $response;
}


public function SheetData($sheetId,$sheetName,$date){
    //     $url='https://sheet.digicomplatform.xyz/api?id='.urlencode($sheetId).'&sheet='.urlencode($sheetName).'&columns=false&q='.urlencode($date);
      
    // $url = 'https://sheets.googleapis.com/v4/spreadsheets/'.urlencode($sheetId).'/values/'.
   $url ='https://opensheet.elk.sh/'.$sheetId.'/1';
$ch = curl_init();

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$result = curl_exec($curl);


curl_close($curl);




// curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
// curl_setopt( $ch, CURLOPT_HEADER, 0 );
// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
// curl_setopt( $ch, CURLOPT_URL, $url );
// curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
// $result=curl_exec($ch);
// curl_close($ch);


     try {
           
                if($result==false){
                    return ["res"=>false,"data"=>"Api url not working"];
                }
                else{
                     $results=json_decode($result);
                       
                  
                       if(!isset($results->error)){
                       
                        
                        
                            if(count($results)){
                                return ["res"=>true,"data"=>$results,"rows"=>count($results)];
                            }else{
                                return ["res"=>false,"msg"=>"erreur"];
                            }
                          
                       }else{
                           return ["res"=>false,"msg"=>'the sheet is not working'];
                       }
                   }

                
 
           } catch (ModelNotFoundException $e) {
              
             return ["res"=>false,"data"=>$e->getMessage()];
            
        }
        
    }



}