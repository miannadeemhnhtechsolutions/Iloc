<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InitiativeTwoForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InitiativeTwoController extends Controller
{
    public function store(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            // Organization fields
            'organization_name' => 'required|string|max:255',
            'organization_city' => 'required|string|max:255',
            'organization_state' => 'required|string|max:255',
            'organization_email' => 'required|string|email|max:255',
            'organization_website' => 'required|string|url|max:255',
            'organization_established_year' => 'required|integer|min:1900|max:' . date('Y'),
            'organization_is_active' => 'required|boolean',
            'organization_year' => 'required|integer|min:1900|max:' . date('Y'),
            'organization_age' => 'required|string',
            'organization_presentation_type' => 'required|in:debutantes,beaus,both,males,females,other',
            'organization_presentation_frequency' => 'required|in:annually,biannually',
            'organization_participation_method' => 'required|in:invite,referral,open',

            // Former Debutante fields
            'debutante_name_at_presentation' => 'required|string|max:255',
            'debutante_escort_name' => 'required|string|max:255',
            'debutante_year_presented' => 'required|integer|min:1900|max:' . date('Y'),
            'debutante_sponsoring_organization' => 'required|string|max:255',
            'debutante_city' => 'required|string|max:255',
            'debutante_state' => 'required|string|max:255',

            // Former Beau fields
            'beau_name_at_presentation' => 'required|string|max:255',
            'beau_escort_name' => 'required|string|max:255',
            'beau_year_presented' => 'required|integer|min:1900|max:' . date('Y'),
            'beau_sponsoring_organization' => 'required|string|max:255',
            'beau_city' => 'required|string|max:255',
            'beau_state' => 'required|string|max:255',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }

        $data = $validated_data->validated();
        $data['user_id'] = $request->user()->id;
        $presentationData = InitiativeTwoForm::create($data);
        $response = [
            'status' => true,
            'data'=>$presentationData,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {
//        dd($request->all());
        $validated_data = Validator::make($request->all(), [
            // Organization fields
            'organization_name' => 'required|string|max:255',
            'organization_city' => 'required|string|max:255',
            'organization_state' => 'required|string|max:255',
            'organization_email' => 'required|string|email|max:255',
            'organization_website' => 'required|string|url|max:255',
            'organization_established_year' => 'required|integer|min:1900|max:' . date('Y'),
            'organization_year' => 'required|integer|min:1900|max:' . date('Y'),
            'organization_age' => 'required|string',
            'organization_is_active' => 'required|boolean',
            'organization_presentation_type' => 'required|in:debutantes,beaus,both,males,females,other',
            'organization_presentation_frequency' => 'required|in:annually,biannually',
            'organization_participation_method' => 'required|in:invite,referral,open',

            // Former Debutante fields
            'debutante_name_at_presentation' => 'required|string|max:255',
            'debutante_escort_name' => 'required|string|max:255',
            'debutante_year_presented' => 'required|integer|min:1900|max:' . date('Y'),
            'debutante_sponsoring_organization' => 'required|string|max:255',
            'debutante_city' => 'required|string|max:255',
            'debutante_state' => 'required|string|max:255',

            // Former Beau fields
            'beau_name_at_presentation' => 'required|string|max:255',
            'beau_escort_name' => 'required|string|max:255',
            'beau_year_presented' => 'required|integer|min:1900|max:' . date('Y'),
            'beau_sponsoring_organization' => 'required|string|max:255',
            'beau_city' => 'required|string|max:255',
            'beau_state' => 'required|string|max:255',
            "id"=>'numeric|required|exists:initiative_two_forms,id',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $data = $validated_data->validated();
//dd($request->user()->id);
$record=InitiativeTwoForm::where(['id'=>$request->id,'user_id'=>$request->user()->id])->first();
//dd($record);
        $record->update($data);

        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function destroy($id){
        $data=InitiativeTwoForm::where(['id'=>$id,'user_id'=>request()->user()->id])->first();


        if ($data){
            $data->delete();
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
    public function index(){
        $user=InitiativeTwoForm::where('User_id',request()->user()->id)->first();
        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }

}
