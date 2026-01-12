<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public int $remaining,
        public int $threshold
    ) {
    }

    public function build(): self
    {
        return $this->subject("Low stock: {$this->product->name}")
            ->view('emails.low-stock')
            ->with([
                'product' => $this->product,
                'remaining' => $this->remaining,
                'threshold' => $this->threshold,
            ]);
    }
}
