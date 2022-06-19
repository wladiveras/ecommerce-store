<?php

namespace App\Notifications\Tickets;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class NewTicket extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toSlack($notifiable)
    {
        $ticket = $this->ticket;
        $url = env('DASHBOARD_ROUTE',"") . "/tickets/".$ticket['id'];
        return (new SlackMessage)
            ->warning()
            ->content("Novo ticket aberto #{$ticket['id']}")
            ->attachment(function ($attachment) use ($url,$ticket) {
                $attachment->title($ticket['subject'], $url)
                           ->content(substr($ticket['content'],0,120));
            });
    }
}
