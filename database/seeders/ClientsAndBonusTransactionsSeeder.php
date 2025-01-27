<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsAndBonusTransactionsSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            ['name' => 'Шурик', 'email' => 'shurik@example.com', 'balance' => 100.00],
            ['name' => 'Леня', 'email' => 'lenya@example.com', 'balance' => 200.00],
            ['name' => 'Нина', 'email' => 'nina@example.com', 'balance' => 150.00],
            ['name' => 'Гриша', 'email' => 'grisha@example.com', 'balance' => 300.00],
            ['name' => 'Света', 'email' => 'sveta@example.com', 'balance' => 250.00],
        ];

        // Вставляем клиентов
        foreach ($clients as $client) {
            DB::table('clients')->insert($client);
        }

        // Получаем ID всех клиентов
        $clientIds = DB::table('clients')->pluck('id');

        // Создаем бонусные транзакции
        foreach ($clientIds as $clientId) {
            DB::table('bonus_transactions')->insert([
                ['client_id' => $clientId, 'type' => 'credit', 'amount' => 50.00],
                ['client_id' => $clientId, 'type' => 'debit', 'amount' => 20.00],
            ]);
        }
    }
}
