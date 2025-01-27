<?php

namespace Tests\Unit\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Models\BonusTransaction;
use App\Models\Client;
use App\Repositories\BonusRepository;
use App\Services\BonusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BonusService $bonusService;
    protected $bonusRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем мок для репозитория
        $this->bonusRepositoryMock = $this->createMock(BonusRepository::class);
        $this->bonusService = new BonusService($this->bonusRepositoryMock);
    }

    public function testCreditIncreasesClientBalance()
    {
        $clientId = 1;
        $amount = 100.0;

        // Создаем мок клиента
        $client = new Client(['balance' => 150.0]);
        $this->bonusRepositoryMock->method('getClient')->willReturn($client);
        $this->bonusRepositoryMock->method('addTransaction')->willReturn(new BonusTransaction());

        // Выполняем кредитование
        $transaction = $this->bonusService->credit($clientId, $amount);

        // Проверяем, что баланс увеличился
        $this->assertEquals(150.0, $client->balance);
        $this->assertInstanceOf(BonusTransaction::class, $transaction);
    }

    public function testDebitDecreasesClientBalance()
    {
        $clientId = 1;
        $amount = 50.0;

        // Создаем мок клиента
        $client = new Client(['balance' => 50.0]);
        $this->bonusRepositoryMock->method('getClient')->willReturn($client);
        $this->bonusRepositoryMock->method('addTransaction')->willReturn(new BonusTransaction());

        // Выполняем дебетование
        $transaction = $this->bonusService->debit($clientId, $amount);

        // Проверяем, что баланс уменьшился
        $this->assertEquals(50.0, $client->balance);
        $this->assertInstanceOf(BonusTransaction::class, $transaction);
    }

    public function testDebitThrowsInsufficientBalanceException()
    {
        $this->expectException(InsufficientBalanceException::class);

        $clientId = 1;
        $amount = 100.0;

        // Создаем мок клиента с недостаточным балансом
        $client = new Client(['balance' => 50.0]);
        $this->bonusRepositoryMock->method('getClient')->willReturn($client);

        // Пытаемся выполнить дебетование
        $this->bonusService->debit($clientId, $amount);
    }

    public function testGetBalanceReturnsClientBalance()
    {
        $clientId = 1;
        $expectedBalance = 100.0;

        // Создаем мок клиента
        $client = new Client(['balance' => $expectedBalance]);
        $this->bonusRepositoryMock->method('getClient')->willReturn($client);

        // Получаем баланс
        $balance = $this->bonusService->get_balance($clientId);

        // Проверяем, что возвращается правильный баланс
        $this->assertEquals($expectedBalance, $balance);
    }
}
