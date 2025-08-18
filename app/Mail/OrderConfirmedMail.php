<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $recipientType; // 'customer' or 'branch_manager'
    public $recipientName;

    public function __construct(Order $order, string $recipientType, string $recipientName)
    {
        $this->order = $order;
        $this->recipientType = $recipientType;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        $subject = $this->recipientType === 'customer' 
            ? 'Order #' . $this->order->id . ' Confirmed - KANMA'
            : 'Order #' . $this->order->id . ' Confirmed - KANMA';

        return $this->subject($subject)
            ->view('emails.order-confirmed');
    }
} 