<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewSubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewSubscriptionManageController extends Controller
{
public function update_status(Request $request){
    $validator = Validator::make($request->all(), [

        'status' => 'required|string|in:paused,canceled,active',
        'subscription_id'=>"required|numeric|exists:new_subscription_plans,id",
    ]);

    if($validator->fails()) {

        $response = [
            'status' => false,
            'errors' => $validator->errors(),
            'message' => "Validation Fails",
        ];


        return response()->json($response, 404);
    }
        $find=NewSubscriptionPlan::where('id',$request->subscription_id)->first();
        $find->update(['status'=>$request->status]);
        $response = [
            'status' => true,
            'data'=>$find,
            'message' => "Subscription plan updated successfully",
        ];
        return response()->json($response, 200);



}
public function index(){
    $data=NewSubscriptionPlan::with('user','plan')->get();
    $response = [
        'status' => true,
        'data'=>$data,
        'message' => "Success",
    ];
    return response()->json($response, 200);


}
}
