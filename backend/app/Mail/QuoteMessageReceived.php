<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\QuoteMessage;

class QuoteMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public function __construct(QuoteMessage $message)
    {
        $this->message = $message;
        $this->onQueue('emails');
        $this->afterCommit();
    }

    public function build()
    {
        return $this->view('emails.quotes.new-message')
            ->subject('New Message on Your Quote')
            ->with([
                'greeting' => 'Hello ' . $this->message->quote->user->first_name . ',',
                'closing' => 'Best regards,<br>TekiPlanet Team'
            ]);
    }
} 