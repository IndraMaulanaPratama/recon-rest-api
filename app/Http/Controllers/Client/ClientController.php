<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ResponseResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('clientRegister');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::get();

        return response()->json(new DataResponseResource(200, 'Lis All Data Client', $clients), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_name' => ['required'],
                'client_id' => ['required'],
                'client_secret' => ['required'],
            ]);

            $clients = Client::create([
                'csm_c_id' => $request->client_id,
                'csm_c_name' => $request->client_name,
                'csm_c_secret' => bcrypt($request->client_secret),
                'csm_c_key' => Uuid::uuid4(),
            ]);

            $clients['csm_c_secret'] = $request->client_secret;

            if ($clients) {
                return response(
                    new DataResponseResource(
                        200,
                        'User has been created',
                        [
                        'client_id' => $clients['csm_c_id'],
                        'client_name' => $clients['csm_c_name'],
                        'client_secret' => $clients['csm_c_secret'],
                        'client_key' => $clients['csm_c_key'],
                        ]
                    ),
                    200,
                    ['Accept' => 'application/json']
                );
            }

            return response()->json(new ResponseResource(400, 'Error Inserting data'), 500);
        } catch (ValidationException $th) {
            return response()->json(
                new DataResponseResource(400, 'Invalid Credentials', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $client = Client::where('csm_c_id', $request->client_id)->get([
            'csm_c_id AS client_id',
            'csm_c_name AS client_name',
            'csm_c_secret AS client_secret',
            'csm_c_key AS client_key',
            'csm_c_status AS client_status',
        ]);

        if (count($client) > 0) {
            if (0 == $client[0]->csm_c_status) {
                $client[0]->client_status = 'Aktif';
            } else {
                $client[0]->client_status = 'InAktif';
            }

            return response()->json(
                new DataResponseResource(200, 'Data Client', $client),
                200,
                ['Accept' => 'application/json']
            );
        }

        return response()->json(
            new ResponseResource(404, 'Data Not Found'),
            400,
            ['Accept' => 'application/json']
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'client_id' => ['required', 'max:50'],
                'new_secret' => ['required', 'max:20'],
            ]);

            $client = Client::where('csm_c_id', $request->client_id)->get();

            if (count($client) < 1) {
                return response()->json(
                    new ResponseResource(404, 'Data Not Found'),
                    404,
                    ['Accept' => 'application/json']
                );
            }
            $updateClient = Client::where('csm_c_id', $request->client_id)->update([
                'csm_c_secret' => bcrypt($request->new_secret),
            ]);

            return response()->json(
                new ResponseResource(200, 'Client Secret has been updated'),
                200,
                ['Accept' => 'application/json']
            );
        } catch (ValidationException $th) {
            return response()->json(
                new DataResponseResource(400, 'Invalid Credentials', $th->validator->errors()),
                400,
                ['Accept' => 'application/json']
            );
        }
    }
}
