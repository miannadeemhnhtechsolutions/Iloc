<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class RegistrationController extends Controller
{
    public function register(Request $request){
//dd(1);
        $findEmail=User::where(['email'=>$request->email,'user_verified'=>0])->first();
        if ($findEmail){
            $otp=mt_rand(1000,9999);
            $findEmail->email_otp=$otp;
            $findEmail->save();
            Mail::raw('Your verification code is'.$otp ,function ($message) use ($findEmail) {
                $message->from(env('MAIL_USERNAME'));
                $message->to($findEmail->email);
                $message->subject('Verification Code');
            });
            $response = [
                'status' => true,
                'data'    => $findEmail,
                'message' => "As user is already been registered but not verified so a verification code has been sent to your registered email",
            ];
            return response()->json($response, 200);

        }

        $validator = Validator::make($request->all(), [

            "first_name"=>"required|min:1|max:100",
            "last_name"=>"required|min:1|max:100",

            "address"=>"required|max:10000",
            "email"=>"required|unique:users|email|max:100|min:1",
            "phone"=>"required|min:3|max:30",
            "password"=>"required|min:4|max:50",
//            'image' => 'required|array|min:1',
//            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'


        ]);
//        abc


        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }

        $otp=mt_rand(1000,9999);
//        $images = $request->file('image');
        try {
//dd($request->all());

            $user=User::create(['first_name'=>$request->first_name,'last_name'=>$request->last_name, 'email' => request()->email,
                'password' => bcrypt(request()->password),'role_id'=>2,'slug'=>"client",'email_otp'=>$otp,'status'=>'active','phone'=>request()->phone,
                'address'=>$request->address]);

//            foreach ($images as $image) {
//                $filename = $user->id . '_pet_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
//                $image->move(public_path('images'), $filename);
//                Image::create([
//                    'user_id' => $user->id,
//                    'image' => 'https://petavengers.dev-mn.xyz/images/' . $filename,
//                ]);
//            }

            Mail::raw('Your verification code is'.$otp ,function ($message) use ($user) {
                $message->from(env('MAIL_FROM_ADDRESS'),'ILOC');
                $message->to($user->email);
                $message->subject('Verification Code');
            });

            $response = [
                'status' => true,
                'data'    => $user,
                'message' => "Verification code has been sent to your registered email",
            ];
            return response()->json($response, 200);

        }catch (\Exception $e){
            $response = [
                'status' => false,
                'errors'    => $e->getMessage(),
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }


    }

    public function verify(Request $request){
        $validator = Validator::make($request->all(), [

            "user_id"=>"required|numeric|exists:users,id|digits_between:1,111111111",
            "email_otp"=>"required|digits_between:1,9999|exists:users,email_otp",
        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $user=User::where(['email_otp'=>request()->email_otp,'id'=>request()->user_id])->first();
        if ($user){
            $user->user_verified=true;
            $user->email_verified=true;
            $user->email_otp=null;

            $user->save();
            $response = [
                'status' => true,
                'data'    => $user,
                'message' => "User has been verified",
            ];

            return response()->json($response, 200);
        }else{

            $errors=array(
                'errors'=>["Verification failed"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "Try again",
            ];
            return response()->json($response, 404);
        }
    }
}
