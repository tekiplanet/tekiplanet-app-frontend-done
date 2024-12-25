<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class TransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Transaction $transaction)
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.transaction', [
                'user' => $notifiable,
                'transaction' => $this->transaction
            ]);
    }
} 