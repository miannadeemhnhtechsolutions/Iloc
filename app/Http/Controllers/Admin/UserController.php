<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            "first_name"=>"required|max:50",
//            "role_id"=>"required|numeric|exists:roles,id|between:1,3",
            "last_name"=>"required|max:50",
            "email"=>"required|unique:users|email|max:200",
            "phone"=>"required|max:30",
            "password"=>"required|min:4|max:8",
            "address"=>"required|max:1000",


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

        $data['password']=bcrypt($request->password);
        $data['role_id']=3;
        $data['slug']="sub admin";

        $data['status']='active';

         $data['phone_verified']=1;
        $data['user_verified']=1;
        $data['email_verified']=1;

        $user=User::create($data);
        $response = [
            'status' => true,
            'data'    => $user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function index(){
        $users=User::where('role_id',3)->get();
        $response = [
            'status' => true,
            'data'    => $users,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function single_user($id){
        $user=User::where('id',$id)->first();


        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "first_name"=>"required|max:50",
            "last_name"=>"required|max:50",
            'email' => "required|max:100|email|unique:users,email,{$request->id}",
            "phone"=>"required|max:30",
            "address"=>"required|max:1000",
            "id"=>"required|numeric|exists:users,id|between:1,999999999"
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
        $user->update($request->all());
        $response = [
            'status' => true,
            'data'    => $user,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function del_user($id){
        $user=User::where('id',$id)->first();

        if ($user){
            $user->delete();
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
}
