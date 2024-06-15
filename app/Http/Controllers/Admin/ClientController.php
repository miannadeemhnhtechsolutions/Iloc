<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client\Image;
use App\Models\NewSubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index(){
        $users=User::where('role_id', '!=',1)->get();
        $response = [
            'status' => true,
            'data'    => $users,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function change_client_status(Request $request){

        $validator = Validator::make($request->all(), [
            "status" => ["required", "string", "in:active,inactive"],
            "user_id"=>"required|numeric|exists:users,id|between:1,99999999",

        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }


        $user=User::where('id',$request->user_id)->first();

        if ($user){
            $user->status=$request->status;
            $user->save();
            $response = [
                'status' => true,
                'message' => "success",
            ];
            return response()->json($response, 200);
        }
        $errors=array(
            'errors'=>["invalid id"],
        );
        $response = [
            'status' => false,
            'errors'    => $errors,
            'message' => "failed",
        ];
        return response()->json($response, 404);
    }
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "first_name"=>"required|min:1|max:100",
            "last_name"=>"required|min:1|max:100",
            "pet_name"=>"required|min:1|max:100",
            "pet_breed"=>"required|min:1|max:100",
            "pet_color"=>"required|min:1|max:100",
            "microchip_no"=>"required|min:3|max:50",
            "address"=>"required|max:10000",
            'email' => "required|max:100|email|unique:users,email,{$request->id}",
            "phone"=>"required|min:3|max:30",
            "id"=>"required|numeric|exists:users,id|between:1,999999999",
            'image' => 'nullable|array',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
        $data['user_id']=$request->user()->id;
        $user=User::where('id',$request->id)->first();
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->pet_color=$request->pet_color;
        $user->pet_name=$request->pet_name;
        $user->pet_breed=$request->pet_breed;
        $user->microchip_no=$request->microchip_no;
        $user->phone=$request->phone;
        $user->email=$request->email;
        $user->address=$request->address;
        $user->save();

        if ($request->has('image') && $request->image!=null){
            Image::where('user_id',$user->id)->delete();
            $images=$request->file('image');
            foreach ($images as $image) {
                $filename = $user->id . '_pet_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
                Image::create([
                    'user_id' => $user->id,
                    'image' => 'https://petavengers.dev-mn.xyz/images/' . $filename,
                ]);
            }
        }

        $userDetails=User::where('id',$user->id)->with('images','pet_status')->get();

        $response = [
            'status' => true,
            'data'    => $userDetails,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function del_client($id){
        $subscribed_user=NewSubscriptionPlan::where('user_id',$id)->first();
        if ($subscribed_user){
            if ($subscribed_user->expiry_date>=Carbon::now()->toDateString()){
                $response = [
                    'status' => false,
                    'message' => "User cannot be deleted as user is having active subscription",
                ];
                return response()->json($response, 404);

            }else{
                $subscribed_user->delete();
            }
        }
        $user=User::where('id',$id)->where('role_id','!=',1)->first();


        if ($user){
            $user->delete();
            $response = [
                'status' => true,
                'message' => "success",
            ];
            return response()->json($response, 200);
        }
        $errors=array(
            'id'=>["invalid id"],
        );
        $response = [
            'status' => false,
            'errors'    => $errors,
            'message' => "failed",
        ];
        return response()->json($response, 404);
    }

    public function single_client($id){
        $user=User::where('id',$id)->where('role_id','!=',1)->first();
        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function profile(){
        $user=User::where('id',request()->user()->id)->where('role_id','!=',1)->with('images','pet_status')->first();
        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }


}
