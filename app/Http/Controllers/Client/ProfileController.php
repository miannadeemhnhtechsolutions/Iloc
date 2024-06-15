<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client\Image;
use App\Models\NewSubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
//    abc
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "first_name"=>"required|min:1|max:100",
            "last_name"=>"required|min:1|max:100",
            "address"=>"required|max:10000",
//            "password"=>"required|min:4|max:16",
//            'image' => 'nullable|array',
//            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'email' => "required|max:30|min:11|email|unique:users,email,{$request->user()->id}",
            "phone"=>"required|min:1|max:30",


        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $data=$request->all();
//        dd($request->user()->id);



        $user=User::where('id',$request->user()->id)->first();
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;

        $user->phone=$request->phone;
        $user->address=$request->address;
        $user->save();

//        if ($request->has('image')){
//            Image::where('user_id',$user->id)->delete();
//            $images=$request->file('image');
//            foreach ($images as $image) {
//                $filename = $user->id . '_pet_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
//                $image->move(public_path('images'), $filename);
//                Image::create([
//                    'user_id' => $user->id,
//                    'image' => 'https://petavengers.dev-mn.xyz/images/' . $filename,
//                ]);
//            }
//        }
//        $userDetails=User::where('id',$user->id)->();
        $response = [
            'status' => true,
            'data'    => $user,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|different:current_password',
            'confirm_password' => 'required|string|same:new_password',
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }

        // Get the authenticated user
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            $errors=array(
                'current_password'=>["The current password is incorrect"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->save();

        $response = [
            'status' => true,
            'data'    => $user,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function profile(){
        $user=User::where('id',request()->user()->id)->where('role_id','!=',1)->first();
        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function subscription_details(){
    $data=NewSubscriptionPlan::where('user_id',request()->user()->id)->with('user','plan')->first();
        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
}
