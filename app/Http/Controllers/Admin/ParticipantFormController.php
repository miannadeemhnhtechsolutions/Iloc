<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtifactDonation;
use App\Models\ParticipantBusinessForm;
use App\Models\ParticipantFemaleForm;
use App\Models\ParticipantMaleForm;
use App\Models\ParticipantOrganizationForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParticipantFormController extends Controller
{
    public function get_organization_form(){

        $Data = ParticipantOrganizationForm::with('payment')->get();
        $response = [
            'status' => true,
            'data'=>$Data,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function get_female(){

        $Data = ParticipantFemaleForm::all();
        $response = [
            'status' => true,
            'data'=>$Data,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function get_male(){

        $Data = ParticipantMaleForm::all();
        $response = [
            'status' => true,
            'data'=>$Data,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function get_business(){

        $Data = ParticipantBusinessForm::with('payment')->get();
        $response = [
            'status' => true,
            'data'=>$Data,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function del_org(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            "id"=>'numeric|required|exists:participant_organization_forms,id',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $rc=ParticipantOrganizationForm::where('id',$request->id)->delete();
        $response = [
            'status' => true,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function del_male(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            "id"=>'numeric|required|exists:participant_male_forms,id',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $rc=ParticipantMaleForm::where('id',$request->id)->delete();
        $response = [
            'status' => true,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function del_female(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            "id"=>'numeric|required|exists:participant_female_forms,id',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $rc=ParticipantFemaleForm::where('id',$request->id)->delete();
        $response = [
            'status' => true,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function del_business(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            "id"=>'numeric|required|exists:participant_business_forms,id',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $rc=ParticipantBusinessForm::where('id',$request->id)->delete();
        $response = [
            'status' => true,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }

    public function change_status(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            "id"=>'numeric|required|exists:participant_organization_forms,id',
            'status' => 'numeric|required|in:0,1',
        ]);
        if($validated_data->fails()){

            $response = [
                'status' => false,
                'errors'    => $validated_data->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $rc=ParticipantOrganizationForm::where('id',$request->id)->first();
        $rc->organization_is_active=$request->status;
        $rc->save();
        $response = [
            'status' => true,
            'data'=>$rc,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
}
