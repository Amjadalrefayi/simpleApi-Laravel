<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function sendResponse($data,$message)
    {

        $response=[
            'success' => true,
            'data' => $data,
            'message' =>$message,
        ];
        return response()->json($response ,200);
    }

    public function sendError($error , $erorrMessage=[])
    {
        $response=[
            'success' => false,
            'data' => $error,
            'message' =>$erorrMessage,
        ];
        return response()->json($response ,404);
    }

}
