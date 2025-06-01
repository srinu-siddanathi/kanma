<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Thank you for subscribing!')
            ->view('emails.newsletter-confirmation');
    }
} 