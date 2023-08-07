<?php

namespace App\Http\Middleware;

use App\Http\Resources\ResponseResource;
use App\Models\ApiScope;
use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param mixed $api_name
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, $api_name)
    {
        $auth_key = $request->header('x-auth-key');
        $auth_bearer = $request->header('Authorization');
        $bearer_type = substr($auth_bearer, 0, 5);
        $bearer_value = substr($auth_bearer, 7);

        // NOTE:: Cek Validasi auth-key user

        try {
            if (!empty($auth_key)) {
                $user = Client::where(['csm_c_key' => $auth_key])->first();

                if (null != $user) {
                    if ('Basic' != $bearer_type) { // NOTE:: Kondisi ketika bukan Authorization basic
                        // NOTE:: Validasi Token Bearer user
                        $bearer = Client::where([
                            'csm_c_bearer' => $bearer_value,
                            'csm_c_key' => $auth_key,
                        ])->first();

                        if (null != $bearer) {
                            $clientId = DB::connection('recon_auth')
                            ->table('CSCMOD_CLIENT')
                            ->where('csm_c_bearer', $bearer_value)
                            ->pluck('csm_c_id');

                            $scope = ApiScope::where(
                                [
                                'CSM_CAS_API_NAME' => $api_name,
                                'CSM_CAS_CLIENT' => $clientId
                                ]
                            )
                                ->first();

                            if ('superadminAPI' == $clientId[0]
                            || 'autorecon' == $clientId[0]
                            || null != $scope) {
                                $response = $next($request);
                            } else {
                                $response = response()->json(
                                    new ResponseResource(404, 'API Not Found'),
                                    404,
                                    ['Accept' => 'Application/json']
                                );
                            }
                        } else {
                            $response = response()->json(
                                new ResponseResource(401, 'Client Unauthorized'),
                                401,
                                ['Accept' => 'Application/json']
                            );
                        }
                    } else {
                        $response = $next($request);
                    }
                } else {
                    $response = response()->json(
                        new ResponseResource(401, 'Client Unauthorized'),
                        401,
                        ['Accept' => 'Application/json']
                    );
                }
            } else {
                $response = response()->json(
                    new ResponseResource(401, 'Client Unauthorized'),
                    401,
                    ['Accept' => 'Application/json']
                );
            }
        } catch (\Throwable $th) {
            $response = response(
                new ResponseResource(
                    500,
                    'Internal Server Error',
                    500,
                    ['Accept' => 'Application/json']
                )
            );
        }

        return $response;
    }
}
