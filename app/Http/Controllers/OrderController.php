<?php

namespace App\Http\Controllers;

use App\DTOs\OrderDTO;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        public OrderService $service
    ) {
        //
    }

    public function index(Request $request) {
        return new OrderResource(
            Order::query()
                ->where('user_id', $this->service->user->id) // чтобы нельзя было смотреть чужие заказы
                ->orderBy($request->input('orderBy') ?? 'id', $request->input('orderDir') ?? 'asc')
                ->paginate(
                    $request->input('perPage') ?? 10,
                    ['*'],
                    'page',
                    $request->input('page') ?? 1,
                )
        );
    }

    public function show($order_id) {
        if (!$order_id) {
            return response()->json(['error' => 'Отсутствует обязательный параметр order_id'], 400);
        }

        $order = Order::query()
            ->where('user_id', $this->service->user->id) // идентично тому, что выше
            ->where('id', $order_id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Заказ с данным идентификатором не найден'], 404);
        }

        // тут могут быть любые преобразования для фронта, просто не заморачивался
        return response()->json(['order' => $order->toArray()]);
    }

    public function create(CreateOrderRequest $request): JsonResponse
    {
        try {
            $dto = new OrderDTO($request->validated());
            $this->service->setContext($dto);
            $result = $this->service->createOrder();
            return response()->json(
                [
                    'order' => $result['order']->toArray(),
                    'message' => $result['message']
                ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => 'Продукт не найден'], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }


}
