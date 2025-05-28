<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NewUser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    

    // public function showRegisterForm(){
        
      
    //     return view('vendor.registerForm');
    // }
    
      public function verifyToken(Request $request)
    {
        // Your token verification logic here
        // Example: Check if the user associated with the token exists
        if (auth()->check()) {
            return response()->json(['message' => 'Token is valid'], 200);
        } else {
            return response()->json(['message' => 'Token is invalid'], 401);
        }
    }
    

    public function register(Request $request){
      
       
        $fields = $request->validate([
            'new_customers_name'=>'required|string',
            'new_customers_city'=>'required|string',
            'new_customers_email'=>'required|string|unique:delevery_new_customers,new_customers_email',
            'new_customers_phone'=>'required|string|unique:delevery_new_customers,new_customers_phone',
            'new_customers_password'=>'required|string|confirmed'
        ]);

     

        $imgdb = "";
        $path = 'Media/assets/images/newusers/';

    

     if(isset($request->image_store)){
            $imgdb= app('App\Http\Controllers\Custom')->uploadimage($request->image_store,$path);
        }

       
       
        if($imgdb != ''){

            if(file_exists($request->old_image_store)){
                unlink($request->old_image_store);
            }
          
        }else{
            $imgdb = $request->old_image_store;
        }
           
        $getRandCode = rand(100000, 999999);
        $new_customers_active_code = Hash::make($getRandCode);
        
       
        $NewUser = NewUser::create([
            'new_customers_name'=>$fields['new_customers_name'],

            'new_customers_logo'=>$imgdb,
            'new_customers_city'=>$fields['new_customers_city'] ? $fields['new_customers_city'] : '',
               'new_customers_email'=>$fields['new_customers_email'] ? $fields['new_customers_email'] : '',
            'new_customers_phone'=>$fields['new_customers_phone'] ? $fields['new_customers_phone'] : '',
            'new_customers_password'=>$fields['new_customers_password'],
            'new_customers_rc'=> $request->new_customers_rc ?? '',
			'new_customers_website'=> $request->new_customers_website ?? '',
            'new_customers_company_type'=> $request->new_customers_company_type ?? '',
            'new_customers_brand'=> $request->new_customers_brand ?? '',
            'new_customers_active_code'=>$new_customers_active_code,
            'new_customers_statut'=>0,
            'new_customers_active'=>0,
            'new_customers_time'=>time(),
            'created_at'=>time(),
            'new_customers_password_confirmation'=>$fields['new_customers_password']
        ]);

        // $token = $NewUser->createToken('myapptoken')->plainTextToken;

    if($NewUser){
         if (! $request->expectsJson()) {
            
            Session::flash('message', 
                'inscription est réussie, attendez jusqu à ce que vous soyez autorisé à vous connecter  !'
            );

            return redirect()->back();

        }else{

            $response = [
                'New User'=>$NewUser,
                'message'=>'Client registered successfully',
                // 'token'=>$token
            ];
            return response($response,200);
        }
    }else{
          if (! $request->expectsJson()) {
                Session::flash('message', 
                "Erreur en l'inscription essayer autre fois ou bien contacter le support  !"
            );
          }else{
               $response = [
                'message'=>'Error : Can not create client',
                // 'token'=>$token
            ];
            return response($response,400);
          }
        
    }
       
     
    }


    public function clientlogin(Request $request){
        
       
      
        $fields = $request->validate([
         
            'customers_email'=>'required|string',
            'customers_password'=>'required|string'
        ]);


 
        $user  = User::where('customers_email',$fields['customers_email'])->first();
  
        if(!$user || md5(sha1($fields['customers_password'])) != $user->customers_password){


            return response([
                'message'=>'the email or password is incorrect'
            ],401);

        }elseif($user != null && $user->customers_active != 1){

            return response([
                'message'=>'Your Account is Disactive , Contact Your Support ! '
            ],401);


        }



        $token = $user->createToken('myapptoken')->plainTextToken;

 // Update the user's customer_api_token column with the generated token
    $user->update(['customers_api' =>1,'customers_api_key' => $token]);


        $response = [
            'user'=>$user,
            'token'=>$token
        ];
        
        
        return response($response,201);
    }


    public function clientlogout() {
  var_dump("hello logout ");

           
      
        if (Auth::check()) {
          
            $user = Auth::user();
          
            if ($user) {
                $user->tokens()->delete();

 // Update the user's customer_api_token column with the generated token
    $user->update(['customers_api' => 0,'customers_api_key' => NULL]);

        
                 $response = [
                    'user'=>$user,
                    'message' => 'Logged out Successfully'
                 ];
                 
                 return response($response,200);
            }
        }
    
       
    }
}
