<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InitiativeTwoForm;
use App\Models\ParticipantBusinessForm;
use App\Models\ParticipantFemaleForm;
use App\Models\ParticipantMaleForm;
use App\Models\ParticipantOrganizationForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParticipantFormController extends Controller
{
    public function store_organization_form(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            // Organization fields
            'organization_name' => 'required|string|max:255',
            'organization_city' => 'required|string|max:255',
            'organization_state' => 'required|string|max:255',
            'organization_email' => 'required|string|email|max:255|unique:participant_organization_forms,organization_email',
            'organization_website' => 'required|string|url|max:255',
            'organization_established_year' => 'required|integer|min:1900|max:' . date('Y'),
            'organization_is_active' => 'required|boolean',
            'organization_year' => 'required|integer|min:1900|max:' . date('Y'),
            'organization_age' => 'required|string',
            'organization_presentation_type' => 'required|in:debutantes,beaus,both,males,females,other',
            'organization_presentation_frequency' => 'required|in:annually,biannually',
            'organization_participation_method' => 'required|in:invite,referral,open',


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

        $presentationData = ParticipantOrganizationForm::create($data);
        $response = [
            'status' => true,
            'data'=>$presentationData,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function store_female_form(Request $request)
    {
        $validated_data = Validator::make($request->all(), [

            // Former Debutante fields
            'debutante_name_at_presentation' => 'required|string|max:255',
            'debutante_escort_name' => 'required|string|max:255',
            'debutante_year_presented' => 'required|integer|min:1900|max:' . date('Y'),
            'debutante_sponsoring_organization' => 'required|string|max:255',
            'debutante_city' => 'required|string|max:255',
            'debutante_state' => 'required|string|max:255',

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

        $presentationData = ParticipantFemaleForm::create($data);
        $response = [
            'status' => true,
            'data'=>$presentationData,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function store_male_form(Request $request)
    {
        $validated_data = Validator::make($request->all(), [



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

        $presentationData = ParticipantMaleForm::create($data);
        $response = [
            'status' => true,
            'data'=>$presentationData,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function store_business_form(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            'type_of_service' => 'required|string|max:255',
            'name_individual_business' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'email' => 'required|string|max:100|email',
            'website' => 'required|string|max:255|url',
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

        $presentationData = ParticipantBusinessForm::create($data);
        $response = [
            'status' => true,
            'data'=>$presentationData,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
}
