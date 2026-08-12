<?php

namespace Hotash\Subscription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_key',
        'status',
        'custom_price',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
    ];

    protected $casts = [
        'custom_price' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('canceled_at')
            ->where(function (Builder $q): void {
                $q->where('status', '!=', 'canceled')
                    ->orWhereNull('status');
            })
            ->where(function (Builder $q): void {
                $q->where('status', '!=', 'pending')
                    ->orWhereNull('status');
            })
            ->where(function (Builder $q): void {
                $q->where(function (Builder $trialQ): void {
                    $trialQ->whereNotNull('trial_ends_at')
                        ->where('trial_ends_at', '>', now());
                })->orWhere(function (Builder $activeQ): void {
                    $activeQ->where(function (Builder $statusQ): void {
                        $statusQ->where('status', 'active')
                            ->orWhereNull('status');
                    })->where(function (Builder $dateQ): void {
                        $dateQ->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    });
                });
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function canceled(): bool
    {
        return $this->canceled_at !== null || $this->status === 'canceled';
    }

    public function isPendingPayment(): bool
    {
        return $this->status === 'pending';
    }

    public function ended(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function active(): bool
    {
        if ($this->canceled() || $this->isPendingPayment()) {
            return false;
        }

        return $this->onTrial() || (! $this->ended() && ($this->status === 'active' || empty($this->status)));
    }

    public function getPlanAttribute(): ?array
    {
        $plan = config("subscription.plans.{$this->plan_key}");

        if ($plan && $this->custom_price !== null) {
            $plan['price'] = (float) $this->custom_price;
            $plan['is_custom_price'] = true;
        }

        return $plan;
    }

    public function getPriceAttribute(): float
    {
        return $this->custom_price !== null
            ? (float) $this->custom_price
            : (float) ($this->plan['price'] ?? 0.0);
    }

    public function cancel(): self
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        if ($this->invoice && $this->invoice->status === 'unpaid') {
            $this->invoice->update([
                'status' => 'canceled',
            ]);
        }

        return $this;
    }
}
