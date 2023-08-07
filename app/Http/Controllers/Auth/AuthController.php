<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function username()
    {
        return 'csm_c_id';
    }

    public function login(Request $request)
    {
        $dataUser = $_SERVER['HTTP_AUTHORIZATION'];
        $dataUser = substr($dataUser, 6);
        $dataUser = base64_decode($dataUser);
        $loginData = explode(':', $dataUser);

        $authKey = $request->header('x-auth-key', null);

        $client = Client::where('csm_c_id', $loginData[0])->first();

        try {
            // Cek Expired Token
            $accessToken = PersonalAccessToken::where(['name' => $loginData[0]])
                ->where('expires_at', '>=', Carbon::now('Asia/Jakarta'))
                ->get()
            ;

            if (0 != count($accessToken)) { // NOTE:: Kondisi ketika masih ada token yang aktif (belum expire)
                return response()
                    ->json([
                        'access_token' => $client->csm_c_bearer,
                        'token_type' => 'Bearer',
                        'result_code' => 200,
                        'result_message' => 'Token Viewed',
                    ], 200, [
                        'Accept' => 'application/json',
                    ])
                ;
            }   // NOTE:: Kondisi ketika token tidak aktif (expired)

            if (Auth::guard('client')
            ->attempt(
                [
                    'csm_c_id' => $loginData[0],
                    'password' => $loginData[1], 'csm_c_key' => $authKey
                ],
                true
            )
            ) {
                $token = Auth::guard('client')->user()
                ->createToken(
                    $loginData[0],
                    ['*'],
                    ('superadminAPI' == $loginData[0] || 'autorecon' == $loginData[0])
                    ? Carbon::now()->addYears(10)
                    : Carbon::now()->addSeconds(300)
                );
                Client::where('csm_c_id', $loginData[0])->update(['csm_c_bearer' => $token->plainTextToken]);

                return response()->json(
                    [
                    'access_token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_in' => 300,
                    'result_code' => 200,
                    'result_message' => 'Token Generated',
                    ],
                    200,
                    [
                    'Accept', 'application/json',
                    'Authorization', $token->plainTextToken,
                    'x-auth-key', $client->csm_c_key,
                    ]
                );
            }

            return response()->json(
                [
                    'access_token' => null,
                    'token_type' => 'Bearer',
                    'expires_in' => 0,
                    'result_code' => 401,
                    'result_message' => 'Client Unauthorized',
                ],
                401,
                [
                    'Accept', 'application/json',
                ]
            );
        } catch (\Throwable $th) {
            return response(
                new ResponseResource(500, 'Get Token Failed'),
                500,
                ['Accept' => 'application/json']
            );
        }
    }
}
