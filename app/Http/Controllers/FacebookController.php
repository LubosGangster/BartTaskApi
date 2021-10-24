<?php


namespace App\Http\Controllers;



use Illuminate\Http\Request;

class FacebookController extends Controller
{

    public function invokeDialog(){
        return redirect()->to('https://www.facebook.com/v12.0/dialog/oauth?client_id=385867956570857&redirect_uri=http://localhost:8000/api/callback/ajax&response_type=token');
    }

    public function callback(Request $request){
        if ($request->get('access_token') == null && $request->get('error') == null) {
            return view('view');
        } else if ($request->get('access_token') != null){
            return response()->json([
                "token" => $request->get('access_token')
            ], 200);
        } else {
            return response()->json([
                "error" => [
                    "message" => $request->get('error')
                ]
            ], 404);
        }
    }
}
