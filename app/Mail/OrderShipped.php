<?php

namespace App\Mail;

use App\Models\FoodOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\FoodOrder
     */
    protected $order;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\FoodOrder  $order
     * @return void
     */
    public function __construct(FoodOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('welcome@tapigo.ru', 'Новый заказ №' . $this->order->id)
            ->markdown('emails.orders.shipped')
            ->subject('Новый заказ')
            ->with([
                'order' => $this->order,
            ]);
    }
}
