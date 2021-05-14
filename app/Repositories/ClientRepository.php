<?php
namespace App\Repositories;

use App\Models\Client;
use App\Events\ClientSaved;
use App\Core\Repository\BaseRepository;

class ClientRepository extends BaseRepository {

    public function model(){
        return Client::class;
    }


    public function saveClient($data){
        $client = $this->create($data);
        //firing saved event here.
        event(new ClientSaved($client, false));

        return $client;
    }
}
