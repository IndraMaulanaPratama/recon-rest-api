<?php

namespace App\Http\Middleware;

use App\Http\Resources\ResponseResource;
use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

class ClientRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $auth_key = $request->header('x-auth-key');

            if (!empty($auth_key)) {
                $userLogin = Client::where([
                    'csm_c_key' => $auth_key,
                    'csm_c_id' => 'superadminAPI',
                ])->get();

                if (count($userLogin) > 0) {
                    return $next($request);
                }

                return response()->json([
                    'result_code' => 401,
                    'result_message' => 'Unauthorized',
                    // $userLogin
                ], 401, ['Accept' => 'Application/json']);
            }

            return response()->json([
                'result_code' => 401,
                'result_message' => 'Unauthorized',
            ], 401, ['Accept' => 'Application/json']);
        } catch (\Throwable $th) {
            return response(new ResponseResource(500, 'Internal Server Error', 500, ['Accept' => 'Application/json']));
        }
    }
}
