<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtifactDonation;
use App\Models\ParticipantOrganizationForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtifactDonationController extends Controller
{
   public function index(){

       $Data = ArtifactDonation::all();
       $response = [
           'status' => true,
           'data'=>$Data,
           'message' => "success",
       ];
       return response()->json($response, 200);
   }
   public function del_art(Request $request)
   {
       $validated_data = Validator::make($request->all(), [


           "id"=>'numeric|required|exists:artifact_donations,id',
       ]);
       if($validated_data->fails()){

           $response = [
               'status' => false,
               'errors'    => $validated_data->errors(),
               'message' => "Validation Fails",
           ];


           return response()->json($response, 404);

       }
       $rc=ArtifactDonation::where('id',$request->id)->delete();
       $response = [
           'status' => true,
           'message' => "success",
       ];
       return response()->json($response, 200);

   }

}
