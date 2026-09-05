<?php

namespace App\Observers;

use App\Models\Client;
use App\Events\ClientCreated;

class ClientObserver
{
    public function created(Client $client): void
    {
        $client->agency?->increment('clients_count');
        // event(new ClientCreated($client));
    }

    public function deleted(Client $client): void
    {
        $client->agency?->decrement('clients_count');
    }
}
