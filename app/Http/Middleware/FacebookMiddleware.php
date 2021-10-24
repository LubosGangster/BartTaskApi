<?php


namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FacebookMiddleware
{
    private static $client;

    public function __construct()
    {
        self::$client = new Client([ 'base_uri' => 'https://graph.facebook.com' ]);
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        try {
            $response = self::$client->request('GET', '/me', [
                'query' => [
                    'access_token' => $token
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body);
            $request->attributes->add(['idOfUser' => $data->id]);
        } catch (GuzzleException $e) {
            return response()->json([
                "error" => [
                    "message" => "You are not authenticated. Authenticate yourself on http://localhost:8000/api/facebook"
                ]
            ], 401);
        }

        return $next($request);
    }
}
