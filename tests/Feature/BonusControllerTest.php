<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Client;
use App\Models\BonusTransaction;
use App\Services\BonusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonusControllerTest extends TestCase
{
    use RefreshDatabase;

    protected BonusService $bonusService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bonusService = $this->createMock(BonusService::class);
    }

    public function testCreditEndpointWorks()
    {
        $client = Client::factory()->create(['balance' => 100]);

        $this->bonusService
            ->method('credit')
            ->willReturn(new BonusTransaction());

        $response = $this->postJson('/api/bonus/credit', [
            'client_id' => $client->id,
            'amount' => 50.0,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['client_id', 'amount', 'type']);
    }

    public function testDebitEndpointWorks()
    {
        $client = Client::factory()->create(['balance' => 100]);

        $this->bonusService
            ->method('debit')
            ->willReturn(new BonusTransaction());

        $response = $this->postJson('/api/bonus/debit', [
            'client_id' => $client->id,
            'amount' => 50.0,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['client_id', 'amount', 'type']);
    }

    public function testDebitEndpointFailsWhenInsufficientFunds()
    {
        $client = Client::factory()->create(['balance' => 50]);

        $this->bonusService
            ->method('debit')
            ->willThrowException(new InsufficientBalanceException());

        $response = $this->postJson('/api/bonus/debit', [
            'client_id' => $client->id,
            'amount' => 100.0,
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Insufficient balance!']);
    }

    public function testCheckBalanceEndpointWorks()
    {
        $client = Client::factory()->create(['id' => 1, 'balance' => 100]);

        $this->bonusService
            ->method('get_balance')
            ->willReturn(100.0);

        $response = $this->getJson("/api/bonus/balance/{$client->id}");

        $response->assertStatus(200)
            ->assertJson(['client_id' => $client->id, 'balance' => 100.0]);
    }

    public function testCheckBalanceEndpointValidationFailsClientNotExist()
    {
        $response = $this->getJson('/api/bonus/balance/999'); // Не существующий клиент

        $response->assertStatus(404);
    }

    public function testCheckBalanceEndpointValidationFailsNoClientId()
    {
        $response = $this->getJson('/api/bonus/balance/'); // Не существующий клиент

        $response->assertStatus(404);
    }

    public function testCreditEndpointValidationFails()
    {
        $response = $this->postJson('/api/bonus/credit', [
            'client_id' => 999, // Не существующий клиент
            'amount' => 50.0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_id']);
    }

    public function testDebitEndpointValidationFails()
    {
        $response = $this->postJson('/api/bonus/debit', [
            'client_id' => 999, // Не существующий клиент
            'amount' => 50.0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_id']);
    }

}
