<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class TransactionReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function build()
    {
        $pdf = PDF::loadView('receipts.transaction-advanced', [
            'transaction' => $this->transaction
        ]);

        return $this->subject('Transaction Receipt')
                    ->view('emails.transaction-receipt')
                    ->attachData($pdf->output(), "transaction-{$this->transaction->reference_number}.pdf");
    }
} 