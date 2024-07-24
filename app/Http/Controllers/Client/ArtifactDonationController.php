<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ArtifactDonation;
use App\Models\ParticipantBusinessForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtifactDonationController extends Controller
{
    public function store(Request $request)
    {
        $validated_data = Validator::make($request->all(), [


            'first_name' => 'required|string|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:100|email',
            'phone' => 'required|string|max:100',
            'artifact' => 'required|string|max:100',
            'organization' => 'required|max:255|string',
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

        $Data = ArtifactDonation::create($data);
        $response = [
            'status' => true,
            'data'=>$Data,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
}
