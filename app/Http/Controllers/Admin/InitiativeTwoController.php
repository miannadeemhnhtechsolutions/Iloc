<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InitiativeTwoForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InitiativeTwoController extends Controller
{
    public function index()
    {
        $data=InitiativeTwoForm::get();

        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function get_form_with_user($id)
    {
        $data=InitiativeTwoForm::where('id',$id)->with('users')->first();
        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function update_form(Request $request)
    {
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

            'type_of_service' => 'required|string|max:100',
            'name_individual_business' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'email' => 'required|string|max:100|email',
            'website' => 'required|string|max:255|url',






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

        $record=InitiativeTwoForm::where(['id'=>$request->id])->first();
        $record->update($data);

        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function destroy($id){
        $data=InitiativeTwoForm::where(['id'=>$id])->first();


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
}
