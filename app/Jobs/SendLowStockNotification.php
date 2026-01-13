<?php

namespace App\Jobs;

use App\Mail\LowStockAlert;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const DEFAULT_THRESHOLD = 5;

    public const ADMIN_EMAIL = 'admin@example.com';

    public function __construct(
        public Product $product,
        public int $remaining,
        public int $threshold
    ) {}

    public function handle(): void
    {
        Mail::to(self::ADMIN_EMAIL)->send(
            new LowStockAlert($this->product, $this->remaining, $this->threshold)
        );
    }
}
