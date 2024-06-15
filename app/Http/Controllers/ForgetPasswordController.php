<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
//    abc
    public function ForgetPasswordStore(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }

        $token = Str::random(64);
        $createdAt = Carbon::now()->addMinutes(5);
        $deleted = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => $createdAt
        ]);
//        $resetLink = 'https://pet.avengers/reset-password/'.$token;
//        $resetLink=$resetLink.'/'.$token;
        $resetLink='https://Pet-avengers.dev-bt.xyz/updatepassword/'.$token;
//dd($resetLink);

        Mail::raw("Please reset your password by clicking the following link: $resetLink", function($message) use($request){
            $message->to($request->email);
            $message->from(env('MAIL_FROM_ADDRESS'),'Pet.Avengers');
            $message->subject('Reset Password');
//              abc
        });

        $response = [
            'status' => true,
//            'email' => $request->email,
//            'token' => $token,
            'message'=>"Email has been sent to your registered email",
        ];
        return response()->json($response, 200);
    }
    public function ResetPasswordStore(Request $request,$id) {
        $request->validate([
//            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
//            'token'=>"required",
        ]);

        $update = DB::table('password_reset_tokens')->where([ 'token' => $id])->first();

        if(!$update){
            $response=[
                "status"=>false,
                "data"=>['token'=>"invalid token"],
                "message"=>"failed",
            ];
            return response()->json($response,400);
        }
        $now = Carbon::now();

        if ($now->lessThan($update->created_at)) {
            $user = User::where('email', $update->email)->update(['password' => Hash::make($request->password)]);

            // Delete password_resets record
            DB::table('password_reset_tokens')->where(['token'=>$id])->delete();


            $response = [
                'status' => true,

                'message'=>"Success",
            ];
            return response()->json($response, 200);
        }
        $response=[
            "status"=>false,
            "data"=>['error'=>"token expired"],
            "message"=>"failed",
        ];
        return response()->json($response,400);


    }
}
