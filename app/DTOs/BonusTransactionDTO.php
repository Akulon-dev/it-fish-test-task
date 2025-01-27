<?php

namespace App\DTOs;

use App\Models\BonusTransaction;

class BonusTransactionDTO
{
    public int $client_id;
    public string $type;
    public float $amount;

    public function __construct(BonusTransaction $transaction)
    {
        $this->client_id = $transaction->client_id;
        $this->type = $transaction->type;
        $this->amount = $transaction->amount;
    }

    public function toArray(): array
    {
        return [
            'client_id' => $this->client_id,
            'type' => $this->type,
            'amount' => $this->amount,
        ];
    }
}
