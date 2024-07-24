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
            'organization_email' => 'nullable|email',
            'organization_website' => 'nullable|max:255',
            'organization_established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'organization_is_active' => 'nullable|boolean',
            'organization_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'organization_age' => 'nullable|string',
            'organization_presentation_type' => 'nullable|in:debutantes,beaus,both,males,females,other',
            'organization_presentation_frequency' => 'nullable|in:annually,biannually',
            'organization_participation_method' => 'nullable|in:invite,referral,open',


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
            'debutante_escort_name' => 'nullable|max:255',
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
            'beau_escort_name' => 'nullable|max:255',
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
            'name_individual_business' => 'nullable|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'email' => 'nullable|max:100|email',
            'website' => 'nullable|max:255',
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
