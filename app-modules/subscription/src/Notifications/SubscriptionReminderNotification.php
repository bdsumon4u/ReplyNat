<?php

namespace Hotash\Subscription\Notifications;

use Hotash\Subscription\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $daysRemaining,
        public ?Invoice $invoice = null
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $plan = $this->invoice?->subscription?->plan;
        $planName = $plan['name'] ?? 'Pro Plan';
        $amount = $this->invoice ? ($this->invoice->currency.' '.number_format($this->invoice->amount, 2)) : '';
        $dueDate = $this->invoice?->due_date ? $this->invoice->due_date->format('M d, Y') : '';

        $message = (new MailMessage)
            ->greeting('Hello '.($notifiable->name ?? 'Valued Customer').',');

        if ($this->daysRemaining === 7) {
            $message->subject('Subscription Renewal Invoice '.($this->invoice?->invoice_number ? '#'.$this->invoice->invoice_number : ''))
                ->line('Your subscription for **'.$planName.'** is due for renewal in 7 days (on '.$dueDate.').')
                ->line('Invoice Amount: **'.$amount.'**');
        } elseif ($this->daysRemaining === 3) {
            $message->subject('Reminder: Subscription Expires in 3 Days')
                ->line('This is a friendly reminder that your subscription for **'.$planName.'** will expire in 3 days (on '.$dueDate.').')
                ->line('To ensure uninterrupted service, please complete your payment of **'.$amount.'**.');
        } else {
            $message->subject('Urgent: Subscription Expires Tomorrow!')
                ->line('Your subscription for **'.$planName.'** expires tomorrow ('.$dueDate.').')
                ->line('Please complete your renewal payment of **'.$amount.'** to avoid service disruption.');
        }

        return $message
            ->action('Pay & Renew Subscription', url('/app/billing'))
            ->line('Thank you for being a valued customer!');
    }
}
