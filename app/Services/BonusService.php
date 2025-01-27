<?php

namespace app\Services;

use app\Enums\ClientCacheKeys;
use App\Exceptions\InsufficientBalanceException;
use App\Models\BonusTransaction;
use App\Repositories\BonusRepository;
use App\Repositories\BonusRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BonusService
{
    protected BonusRepository $bonusRepository;

    public function __construct(BonusRepositoryInterface $bonusRepository)
    {
        $this->bonusRepository = $bonusRepository;
    }

    /** Пополнение баланса клиента
     * @param int $clientId
     * @param float $amount
     * @return BonusTransaction
     */
    public function credit(int $clientId, float $amount): BonusTransaction
    {
        $client = $this->bonusRepository->getClient($clientId);

        $client->increment('balance', $amount);

        // Удаляем кэш баланса
        Cache::forget(ClientCacheKeys::GET_BALANCE_CACHE_KEY->value . $clientId);
        Log::channel('bonus')->info("Credit: Client {$clientId} credited with {$amount}");

        return $this->bonusRepository->addTransaction($clientId, 'credit', $amount);
    }

    /** Списать с баланса клиента
     * @param int $clientId
     * @param float $amount
     * @return BonusTransaction
     * @throws InsufficientBalanceException
     */
    public function debit(int $clientId, float $amount): BonusTransaction
    {
        $client = $this->bonusRepository->getClient($clientId);
        if ($client->balance < $amount) {
            throw new InsufficientBalanceException();
        }
        $client->decrement('balance', $amount);

        Log::channel('bonus')->info("Debit: Client {$clientId} debited with {$amount}");
        // Удаляем кэш баланса
        Cache::forget(ClientCacheKeys::GET_BALANCE_CACHE_KEY->value . $clientId);

        return $this->bonusRepository->addTransaction($clientId, 'debit', $amount);
    }

    /** Возвращает баланс клиента
     * @param int $clientId
     * @return float
     */
    public function get_balance(int $clientId): float
    {
        return $this->bonusRepository->getClient($clientId)->balance;
    }
}
