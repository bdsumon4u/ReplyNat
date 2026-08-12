<?php

namespace Hotash\Subscription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'due_date',
        'paid_at',
        'reminded_7_days_at',
        'reminded_3_days_at',
        'reminded_1_day_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'reminded_7_days_at' => 'datetime',
        'reminded_3_days_at' => 'datetime',
        'reminded_1_day_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateNextInvoiceNumber(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        return 'INV-'.date('Ym').'-'.sprintf('%04d', $nextId);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'unpaid' && $this->due_date && $this->due_date->isPast();
    }

    public function markAsPaid(): self
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $subscription = $this->subscription;
        if ($subscription) {
            $plan = $subscription->plan;
            $period = (int) ($plan['invoice_period'] ?? 1);
            $interval = $plan['invoice_interval'] ?? 'month';

            if ($subscription->status === 'pending') {
                $startsAt = now();
                $endsAt = (clone $startsAt);
                if ($interval === 'year') {
                    $endsAt->addYears($period);
                } else {
                    $endsAt->addMonths($period);
                }
            } else {
                $baseDate = ($subscription->ends_at && $subscription->ends_at->isFuture())
                    ? $subscription->ends_at
                    : now();

                $startsAt = $subscription->starts_at ?? now();
                $endsAt = (clone $baseDate);
                if ($interval === 'year') {
                    $endsAt->addYears($period);
                } else {
                    $endsAt->addMonths($period);
                }
            }

            $subscription->update([
                'status' => 'active',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'canceled_at' => null,
            ]);
        }

        return $this;
    }
}
