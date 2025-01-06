<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\NewsData;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $data=NewsData::all();
        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "Success",
        ];
        return response()->json($response, 200);
    }
}
