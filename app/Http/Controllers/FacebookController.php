<?php


namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FacebookController extends Controller
{

    public function invokeDialog(){
        return redirect()->to('https://www.facebook.com/v12.0/dialog/oauth?client_id=385867956570857&redirect_uri=http://localhost:8000/api/callback/ajax/token&response_type=token');
    }

    public function callback(Request $request, $query){
        $token = $request->get('access_token');

        Storage::disk('local')->put("token/token.json", json_encode([
            'token' => $token
        ]));

        return response()->json([
            'message' => 'Successfully login.'
        ]);
    }
}
