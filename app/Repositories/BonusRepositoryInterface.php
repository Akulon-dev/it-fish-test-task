<?php

namespace app\Repositories;

namespace App\Repositories;

use App\Models\Client;
use App\Models\BonusTransaction;

interface BonusRepositoryInterface
{
    public function getClient(int $id): ?Client;
    public function addTransaction(int $clientId, string $type, float $amount): BonusTransaction;
}
