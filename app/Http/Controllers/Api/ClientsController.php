<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\ClientRepository;
use App\Exceptions\ApplicationException;
use App\Http\Resources\ClientCollection;
use App\Http\Requests\ClientRegisterRequest;

class ClientsController extends Controller
{
    private $clients;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clients = $clientRepository;
    }

    public function index()
    {
        $collection = $this->clients
            ->orderBy(request('orderBy', 'client_name'), request('orderType', 'ASC'))
            ->with('users')
            ->paginate(10);


        return new ClientCollection($collection);
    }

    public function store(ClientRegisterRequest $request, UserRepository $userRepository)
    {
        $data = $request->transform();


        $clientData = $data['client'];
        $userData = $data['user'];

        /* using transaction to avoid having orphan client data. */
        DB::beginTransaction();
        try {
            $userData['client_id'] = $this->clients->saveClient($clientData)->id;

            $userRepository->create($userData);

            DB::commit();

            return apiSuccess([], 'Client Registered');
        } catch (\Exception $e) {
            DB::rollBack();
            /* we again throw another exception here os the exception handler can send required response */
            throw new ApplicationException("Something went wrong. " . $e->getMessage());
        }
    }
}
