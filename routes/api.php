<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParcelController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\PickuprequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\NewUser;
use App\Models\User;

use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', function () {
    return response()->json(['message' => 'API working!']);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register-client',[AuthController::class,'register']);
Route::post('/check-existingClient',function(Request $request){

    //   $fields = $request->validate([
    //          'new_customers_email'=>'required|email',
    //           'new_customers_phone'=>'required|string|unique:delevery_new_customers,new_customers_phone',
    //          ]);


if (NewUser::where('new_customers_email', '=', $request->email)->first() == null && User::where('customers_email', '=', $request->email)->first() == null) {

  // User does not exist
   $response = [
                'message'=>'this is new email ',
                'exists'=>false,
                'email'=>$request->email
                // 'token'=>$token
            ];
            return response($response,200);

} else {
  // User exits
   $response = [
                'New User'=>' email is exist',
                'exists'=>true,
                'email'=>$request->email
                // 'token'=>$token
            ];
            return response($response,200);
}



});

Route::post('/check-existingPhone',function(Request $request){

    //   $fields = $request->validate([
    //          'new_customers_email'=>'required|email',
    //           'new_customers_phone'=>'required|string|unique:delevery_new_customers,new_customers_phone',
    //          ]);


if (NewUser::where('new_customers_phone', '=', $request->phone)->first() == null && User::where('customers_phone', '=', $request->phone)->first() == null) {

  // User does not exist
   $response = [
                'message'=>'this is new phone ',
                'exists'=>false,
                'phone'=>$request->phone
                // 'token'=>$token
            ];
            return response($response,200);

} else {
  // User exits
   $response = [
                'New User'=>' phone is exist',
                'exists'=>true,
                'phone'=>$request->phone
                // 'token'=>$token
            ];
            return response($response,200);
}



});
Route::post('/client-login',[AuthController::class,'clientlogin']);
Route::post('/tracking-parcel',[ParcelController::class,'trackingparcel']);
Route::get('/getwelcomedata',[WelcomeController::class,'welcomeData']);




// Route::get('/products',[ProductController::class,'index']);
// Route::get('/products/{id}',[ProductController::class,'show']);
// Route::get('/products/search/{name}',[ProductController::class,'search']);

// Route::post('/products/create',[ProductController::class,'store']);

// Route::resource('products','ProductController');

Route::group(['middleware' => ['auth:sanctum']], function() {

    // Route::post('/products',[ProductController::class,'store']);
    // Route::put('/products/{id}',[ProductController::class,'update']);
    // Route::delete('/products/{id}',[ProductController::class,'destory']);



    Route::post('/verifyToken', [AuthController::class, 'verifyToken']);



Route::get('/general_statistics',[StatisticsController::class,'get_generalStatistics']);



/// claims requests routes ::: Reclamations ::::
Route::post('/claim/getclaims', [ClaimController::class, 'getclaims']);
// Route::get('/claim/getclaims/{claims_type}', [ClaimController::class, 'getclaims']);
Route::post('/claim/add',[ClaimController::class,'createclaim']);
Route::post('/claim/chat/get_messages', [ClaimController::class, 'getchatclaim']);
Route::post('/claim/chat/save_messages', [ClaimController::class, 'save_messages_claim']);
/// end  claims requests routes ::: Reclamations ::::



/// pickup requests requests routes ::: Demande Ramassage ::::
Route::get('/pickuprequest/getpickuprequests', [PickuprequestController::class, 'getpickuprequests']);
// Route::get('/claim/getclaims/{claims_type}', [PickuprequestController::class, 'getclaims']);
Route::post('/pickuprequest/create',[PickuprequestController::class,'createpickuprequest']);

/// end  claims requests routes ::: Reclamations ::::


/// gestion parcels requests routes ::: Colis ::::

 Route::post('/parcel/create',[ParcelController::class,'CreateParcel']);
 Route::get('/parcel/getActiveCities',[ParcelController::class,'getActiveCities']);

Route::get('/parcel/getparcels',[ParcelController::class,'GetParcels']);
Route::get('/parcel/getcanceledparcels',[ParcelController::class,'getcanceledparcels']);
Route::get('/parcel/getdeliveredparcels',[ParcelController::class,'getdeliveredparcels']);
Route::get('/parcel/getdistrubitionparcels',[ParcelController::class,'getdistrubitionparcels']);
Route::get('/parcel/getpreparationparcels',[ParcelController::class,'getpreparationparcels']);
Route::get('/parcel/getinprogressparcels',[ParcelController::class,'getinprogressparcels']);

//// filtrage types ::::  filtrer par search et status :::::
Route::post('/parcel/filterparcelsbysearch',[ParcelController::class,'filterparcelsbysearch']);
Route::post('/parcel/filterparcelsbystatus',[ParcelController::class,'filterparcelsbystatus']);
Route::post('/parcel/updatestatut',[ParcelController::class,'updatestatut']);
Route::post('/parcel/delete-parcel',[ParcelController::class,'deleteparcel']);
Route::get('/parcel/check-newParcels',[ParcelController::class,'checknewParcels']);
Route::post('/parcel/getParcelClaim',[ParcelController::class,'getParcelClaim']);

////  end filtrage types ::::  filtrer par search et status :::::

/// end gestion parcels requests routes ::: Colis ::::





/// start gestion Notes requests routes ::: les Bones ::::

Route::get('/note/getDeliverynotes',[NotesController::class,'getDeliverynotes']);
Route::get('/note/getReturnednotes',[NotesController::class,'getReturnednotes']);
Route::get('/note/getInvoicenotes',[NotesController::class,'getInvoicenotes']);
Route::post('/note/getParcelsByTypeEdit',[NotesController::class,'getParcelsByTypeEdit']);
Route::post('/note/AddParcelToDeliverynote',[NotesController::class,'AddParcelToDeliverynote']);
Route::post('/note/DeleteParcelFromDeliverynote',[NotesController::class,'DeleteParcelFromDeliverynote']);
Route::post('/note/DeleteDeliverynote',[NotesController::class,'DeleteDeliverynote']);
Route::post('/note/CreateDeliverynote',[NotesController::class,'CreateDeliverynote']);

/// end gestion notes requests routes :::  les Bones ::::


    Route::post('/client-logout',[AuthController::class,'clientlogout']);



});