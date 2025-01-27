<?php

namespace app\Repositories;

namespace App\Repositories;

use app\Enums\ClientCacheKeys;
use App\Models\Client;
use App\Models\BonusTransaction;
use Illuminate\Support\Facades\Cache;

class BonusRepository implements BonusRepositoryInterface
{
    public function getClient(int $id): ?Client
    {
        return Cache::remember(ClientCacheKeys::GET_BALANCE_CACHE_KEY->value.$id, now()->addMinutes(5), function () use ($id) {
            return Client::findOrFail($id);
        });
    }

    public function addTransaction(int $clientId, string $type, float $amount): BonusTransaction
    {
        return BonusTransaction::create([
            'client_id' => $clientId,
            'type' => $type,
            'amount' => $amount,
        ]);
    }
}
