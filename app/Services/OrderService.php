<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderService {

    public ?User $user;
    private ?OrderDTO $dto;
    // максимально число часов на которые можно продлевать аренду
    private const MAX_HOURS = 24;

    public function __construct() {
        $this->user = Auth::user();
    }

    public function setContext(OrderDTO $dto) {
        $this->dto = $dto;
    }

    /**
     * @throws \Throwable
     */
    public function createOrder(): array
    {
        $product = Product::query()->findOrFail($this->dto->product_id);

        // берём существующий заказ на покупку или аренду, ну или его может не быть и мы просто его создадим
        $result = [
            'order' => $this->getProductOrder($product->id),
            'message' => ''
        ];

        // сразу проверяем если продукт куплен, ничего с ним мы уже сделать не можем
        if ($result['order']?->type === 'buy') {
            throw new \RuntimeException('Вы уже купили этот продукт навсегда!');
        }

        switch ($this->dto->type) {
            case 'buy':
                // тут возможны варианты исполнения, взял первый, пришедший в голову
                if ($result['order']) {
                    throw new \RuntimeException('Вы уже арендовали этот продукт до '
                        .$result['order']->valid_till->format('d.m.Y H:i:s').'!');
                }

                $result['order'] = Order::query()->create(
                    array_merge($this->dto->toArray(),
                        [
                            'user_id' => $this->user->id,
                            'price' => $product->price_buy
                        ]
                    ));
                $result['message'] = 'Продукт успешно приобретён!';
                break;
            case 'rent':
                // здесь в любом случае мы попадаем в заказ, который не истёк, он на аренду
                // и мы его будем продлевать, если время аренды не превышает 24 часа
                if ($result['order']) {
                    if (
                        $result['order']->valid_till
                            ->addHours($this->dto->hours)
                            ->gt(now()->addHours(self::MAX_HOURS))
                    ) {
                        throw new \RuntimeException('К сожалению продление аренды на указанный срок невозможно'
                            .', попробуйте выбрать меньший срок!');
                    } else {
                        $result['order']->valid_till = $result['order']->valid_till->addHours($this->dto->hours);
                        $result['order']->price += ($product->price_rent * $this->dto->hours);
                        $result['order']->save();

                        $result['message'] = 'Продукт успешно продлён на '
                            .$this->dto->hours
                            .' часов до ' // здесь надо конечно сделать plural по часам, но не будем заморачиваться
                            .$result['order']->valid_till->format('d.m.Y H:i:s').'!';

                        return $result;
                    }
                }

                $result['order'] = Order::query()->create(
                    array_merge($this->dto->toArray(),
                        [
                            'user_id' => $this->user->id,
                            'price' =>($product->price_rent * $this->dto->hours),
                            'valid_till' => now()->addHours($this->dto->hours)
                        ]
                    ));
                $result['message'] = 'Продукт успешно арендован на '
                    .$this->dto->hours
                    .' часов до ' // здесь та же история
                    .$result['order']->valid_till->format('d.m.Y H:i:s').'!';
                break;
        }
        return $result;
    }

    /**
     * Берем первый активный заказ на покупку или аренду по выбранному продукту
     * @param int $product_id
     * @return Model|null
     */
    private function getProductOrder(int $product_id): ?Model
    {
        return Order::query()
            ->where('product_id', $product_id)
            ->where('user_id', $this->user->id)
            ->where(function (Builder $builder) {
                $builder->where('type', '=', 'buy');
                $builder->orWhere(function (Builder $builder) {
                    $builder->where('type', '=', 'rent');
                    $builder->whereNotNull('valid_till');
                    $builder->where('valid_till', '>', now());
                });
            })
            ->first();
    }
}
