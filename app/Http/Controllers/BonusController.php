<?php

namespace App\Http\Controllers;

use App\DTOs\BonusTransactionDTO;
use App\Exceptions\InsufficientBalanceException;
use App\Services\BonusService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
/**
 * @OA\Info(title="Bonus System API", version="1.0")
 */
class BonusController extends Controller
{
    protected $bonusService;

    public function __construct(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;
    }

    /**
     * @OA\Post(
     *     path="/api/bonus/credit",
     *     summary="Начисление бонуса клиенту",
     *     tags={"Bonus"},
     *     @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Response(response=200, description="Бонус успешно зачислен"),
     *     @OA\Response(response=404, description="Неизвесный клиент"),
     *     @OA\Response(response=500, description="Непредвиденная ошибка")
     * )
     */
    public function credit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|numeric|exists:clients,id',
            'amount' => 'required|numeric|max:99999999|min:0.01',// По поводу максимального значения, конечно, в postgres максимум 10^8, это можно предусмотреть добавив отдельное поле для множителя, но ситуация крайне маловероятная, так что в этом случае будет неизвестная ошибка.
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $transaction = $this->bonusService->credit($validator->getValue('client_id'), $validator->getValue('amount'));
            return response()->json((new BonusTransactionDTO($transaction))->toArray());
        } catch (Exception $e) {
            Log::channel('bonus')->error("Error in debit: " . $e->getMessage());
            return response()->json(['error' => 'Unknown error'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/bonus/debit",
     *     summary="Списание бонуса от клиента",
     *     tags={"Bonus"},
     *     @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Response(response=200, description="Бонус успешно списан"),
     *     @OA\Response(response=400, description="Недостаточный баланс или недействительный запрос"),
     *     @OA\Response(response=404, description="Неизвесный клиент"),
     *     @OA\Response(response=500, description="Непредвиденная ошибка")
     * )
     */
    public function debit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|numeric|exists:clients,id',
            'amount' => 'required|numeric|max:99999999|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $transaction = $this->bonusService->debit($validator->getValue('client_id'), $validator->getValue('amount'));
            return response()->json((new BonusTransactionDTO($transaction))->toArray());
        } catch (InsufficientBalanceException $e) {
            return response()->json(['error' => $e->getMessage()], 400);

        } catch (Exception $e) {
            Log::channel('bonus')->error("Error in debit: " . $e->getMessage());
            return response()->json(['error' => 'Unknown error'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/bonus/balance/{client_id}",
     *     summary="Проверить баланс клиента",
     *     tags={"Bonus"},
     *     @OA\Parameter(
     *         name="client_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Баланс клиента успешно возвращён"),
     *     @OA\Response(response=404, description="Неизвесный клиент")
     * )
     * @param int $clientId Сразу проверяет только числовые
     * @return JsonResponse
     */
    public function checkBalance(int $clientId): JsonResponse
    {
        $balance = $this->bonusService->get_balance($clientId);
        return response()->json(['client_id' => $clientId, 'balance' => $balance]);
    }
}

