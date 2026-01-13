<?php

namespace App\Jobs;

use App\Mail\DailySalesReport;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const ADMIN_EMAIL = 'admin@example.com';

    public function handle(): void
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        $sales = OrderItem::query()
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('order_items.created_at', [$start, $end])
            ->groupBy('order_items.product_id', 'products.name')
            ->orderByDesc(DB::raw('SUM(order_items.quantity * order_items.unit_price)'))
            ->get([
                'order_items.product_id',
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue'),
            ]);

        $totalRevenue = (float) $sales->sum('total_revenue');

        Mail::to(self::ADMIN_EMAIL)->send(
            new DailySalesReport($sales, $start->toDateString(), $totalRevenue)
        );
    }
}
