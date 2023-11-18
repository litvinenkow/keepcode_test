<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CheckRentOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-rent-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Order::query()
            ->where('type', 'rent')
            // здесь можно обработку ограничить частотой времени вызова скрипта, чтобы не сканить всю таблицу
            //->where('created_at', now()->addMinutes(-10))
            ->whereNotNull('valid_till')
            ->where('valid_till', '<', now())
            ->chunk(50, function($orders) {
                foreach ($orders as $order) {
                    // просто очищаем это поле, возможны другие варианты, это смотрится как самый простой
                    $order->valid_till = null;
                    $order->save();
                }
            });
    }
}
