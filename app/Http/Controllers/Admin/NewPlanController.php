<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewPlan;
use App\Models\NewSubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NewPlanController extends Controller
{
    public function index(){

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required|string',
            'price' => 'required|numeric|digits_between:1,9999999',
            'interval' => 'required|string|in:month,year',
            'description' => 'required|string',
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);


        }
        $newPlan= NewPlan::create(['name'=>$request->name,'slug'=>$request->slug,'price'=>$request->price,'interval'=>$request->interval,'description'=>$request->description,
            'user_id'=>$request->user()->id]);

        $response = [
            'status' => true,
            'data'=>$newPlan,
            'message' => "Subscription Plan created successfully",
        ];
        return response()->json($response, 200);
    }
    public function all_plans(){
        $newPlan= NewPlan::all();

        $response = [
            'status' => true,
            'data'=>$newPlan,
            'message' => "All available plans",
        ];
        return response()->json($response, 200);
    }
    public function view_single($id){
        $newPlan= NewPlan::where('id',$id)->first();

        $response = [
            'status' => true,
            'data'=>$newPlan,
            'message' => "Available Plan",
        ];
        return response()->json($response, 200);

    }
    public function update_plan(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required|string',
            'price' => 'required|numeric|digits_between:1,9999999',
            'interval' => 'required|string|in:month,year',
            'description' => 'required|string',
            'id'=>"required|exists:new_plans,id|numeric",
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);


        }
        $find_plan=NewPlan::where('id',$request->id)->first();
        $find_plan->update(['name'=>$request->name,'slug'=>$request->slug,'price'=>$request->price,'interval'=>$request->interval,'description'=>$request->description,
            'user_id'=>$request->user()->id]);

        $response = [
            'status' => true,
            'data'=>$find_plan,
            'message' => "Subscription Plan updated successfully",
        ];
        return response()->json($response, 200);

    }
    public function del_plan($id){
        $find_plan=NewPlan::where('id',$id)->first();
        if ($find_plan){
            $find_subscription=NewSubscriptionPlan::where('new_plan_id',$find_plan->id)->first();
            if ($find_subscription){
                $response = [
                    'status' => false,
                    'message' => "Plan cannot be deleted as its having subscription and payments",
                ];
                return response()->json($response, 404);
            }else{
                $find_plan->delete();
                $response = [
                    'status' => true,
                    'message' => "Success",
                ];
                return response()->json($response, 200);
            }
        }else{
            $response = [
                'status' => false,
                'message' => "Invalid Plan ID",
            ];
            return response()->json($response, 200);
        }

    }


}
