<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailySalesReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $sales,
        public string $reportDate,
        public float $totalRevenue
    ) {
    }

    public function build(): self
    {
        return $this->subject("Daily sales report: {$this->reportDate}")
            ->view('emails.daily-sales-report')
            ->with([
                'sales' => $this->sales,
                'reportDate' => $this->reportDate,
                'totalRevenue' => $this->totalRevenue,
            ]);
    }
}
