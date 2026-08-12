<x-filament-panels::page>
    <div class="space-y-6 sm:space-y-10">
        <!-- Header Banner: Active Plan Overview -->
        <div class="relative overflow-hidden rounded-md bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-5 sm:p-8 text-white shadow-2xl ring-1 ring-white/10">
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 sm:h-80 w-64 sm:w-80 rounded-md bg-indigo-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-64 sm:h-80 w-64 sm:w-80 rounded-md bg-blue-500/20 blur-3xl"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 sm:gap-8">
                <div class="space-y-2.5 sm:space-y-3">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span class="px-2.5 sm:px-3.5 py-0.5 sm:py-1 rounded-md text-[11px] sm:text-xs font-bold uppercase tracking-wider bg-indigo-500/30 text-indigo-300 ring-1 ring-indigo-400/40">
                            Subscription
                        </span>
                        <div class="flex items-center gap-2">
                        @if($isTrial)
                            <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[11px] sm:text-xs font-bold bg-amber-500/20 text-amber-300 ring-1 ring-amber-400/40">
                                <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Free Trial
                            </span>
                        @elseif($isCanceled)
                            <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[11px] sm:text-xs font-bold bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/40">
                                <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                Canceled
                            </span>
                        @elseif($isPending)
                            <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[11px] sm:text-xs font-bold bg-amber-500/20 text-amber-300 ring-1 ring-amber-400/40">
                                <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pending
                            </span>
                        @elseif($isExpired)
                            <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[11px] sm:text-xs font-bold bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/40">
                                <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Expired
                            </span>
                        @elseif($isActive)
                            <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[11px] sm:text-xs font-bold bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/40">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Active
                            </span>
                        @endif
                        </div>
                    </div>

                    <div class="flex sm:flex-col sm:flex-row items-center sm:items-baseline gap-2 sm:gap-4">
                        <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                            {{ $currentPlan['name'] ?? 'Pro' }}
                        </h1>
                        <span class="text-base sm:text-2xl font-semibold text-slate-300">
                            {{ isset($currentPlan['price']) ? ($currentPlan['currency'] . ' ' . number_format($currentPlan['price'], 0) . ' / ' . $currentPlan['invoice_period'] . ' ' . $currentPlan['invoice_interval']) : 'Free' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-base text-slate-300 max-w-2xl">
                        {{ $currentPlan['description'] ?? 'You are currently on our Pro subscription plan.' }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 bg-white/5 backdrop-blur-md p-4 sm:p-5 rounded-md border border-white/10">
                    <div>
                        <div class="text-[11px] sm:text-xs font-medium text-slate-400 uppercase tracking-wider">
                            {{ $isTrial ? 'Trial Ends' : ($isExpired ? 'Expired On' : 'Next Billing Date') }}
                        </div>
                        <div class="text-sm sm:text-lg font-bold text-white mt-0.5">
                            {{ $effectiveEndsAt ? $effectiveEndsAt->format('F d, Y') : 'Continuous' }}
                            @if($isPending)
                                <span class="block sm:inline text-xs font-semibold text-amber-300 font-sans">(Pending)</span>
                            @elseif($isExpired)
                                <span class="block sm:inline text-xs font-semibold text-rose-400 font-sans">(Expired)</span>
                            @elseif($daysRemaining !== null)
                                <span class="block sm:inline text-xs font-normal text-indigo-300 font-sans">({{ $daysRemaining }} days remaining)</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        @if($unpaidInvoice)
                            <button 
                                type="button"
                                wire:click="payInvoice({{ $unpaidInvoice->id }})"
                                class="w-full sm:w-auto text-center px-4 py-2.5 rounded-md text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md transition-all duration-150">
                                Pay #{{ $unpaidInvoice->invoice_number }}
                            </button>
                        @endif
                        @if($currentSubscription && ! $currentSubscription->canceled() && ! $isExpired)
                            <button 
                                type="button"
                                wire:click="cancelSubscription"
                                wire:confirm="Are you sure you want to cancel your subscription?"
                                class="w-full sm:w-auto text-center px-4 py-2.5 rounded-md text-xs sm:text-sm font-semibold text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 transition-all duration-150">
                                Cancel Plan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Plans Section -->
        <div class="space-y-4 sm:space-y-6">
            <div class="text-center max-w-3xl mx-auto space-y-1.5 sm:space-y-2 px-2">
                <h2 class="text-xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Choose Your Subscription Package
                </h2>
                <p class="text-xs sm:text-base text-gray-600 dark:text-gray-400">
                    Flexible plans tailored to your growth. Change or upgrade your plan at any time.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 pt-2 sm:pt-4">
                @foreach($plans as $key => $plan)
                    @php
                        $isCurrent = $currentSubscription && $currentSubscription->plan_key === $key && $currentSubscription->active();
                    @endphp
                    <div class="relative flex flex-col justify-between rounded-md p-5 sm:p-6 bg-white dark:bg-gray-900 border transition-all duration-200 hover:shadow-2xl {{ $plan['featured'] ? 'border-indigo-500 dark:border-indigo-500 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-800' }}">
                        @if($plan['featured'])
                            <div class="text-center absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-md text-[10px] sm:text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-500 to-blue-600 text-white shadow-md">
                                Most Popular
                            </div>
                        @endif

                        <div class="space-y-3 sm:space-y-4">
                            <div class="space-y-1">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $plan['name'] }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $plan['description'] }}
                                </p>
                            </div>

                            <div class="py-2 border-y border-gray-100 dark:border-gray-800">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-lg sm:text-xl font-black text-gray-900 dark:text-white">
                                        {{ $plan['currency'] }} {{ number_format($plan['price'], 0) }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        / {{ $plan['invoice_period'] }} {{ $plan['invoice_interval'] }}
                                    </span>
                                </div>
                                <div class="mt-1 inline-flex items-center text-xs font-medium text-indigo-600 dark:text-indigo-400">
                                    ✨ 21 days free trial included
                                </div>
                            </div>

                            <!-- Features List -->
                            <div class="space-y-2 text-sm">
                                <div class="text-[11px] sm:text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">INCLUDED FEATURES</div>
                                <ul class="space-y-1.5 sm:space-y-2">
                                    @foreach($features as $feature)
                                        <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 pt-3 sm:pt-4">
                            @if($isCurrent)
                                <button disabled class="w-full py-2.5 sm:py-3 px-4 rounded-md text-xs font-bold text-gray-500 bg-gray-100 dark:bg-gray-800 cursor-not-allowed text-center">
                                    Current Active Plan
                                </button>
                            @else
                                <button 
                                    type="button"
                                    wire:click="selectPlan('{{ $key }}')"
                                    class="w-full py-2.5 sm:py-3 px-4 rounded-md text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-500/25 transition-all duration-150 text-center">
                                    Subscribe / Upgrade
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Billing History Table -->
        <div class="space-y-3 sm:space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Billing History</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        {{ $subscriptions->total() }} records
                    </span>
                </h3>
            </div>

            @if($subscriptions->isNotEmpty())
                <div class="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                    <table class="w-full text-left text-xs sm:text-sm text-gray-600 dark:text-gray-300 min-w-[750px]">
                        <thead class="bg-gray-50/80 dark:bg-gray-800/60 text-[11px] uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-2 py-2 sm:px-3 sm:py-2">Subscription & Package</th>
                                <th class="px-2 py-2 sm:px-3 sm:py-2">Amount/Price</th>
                                <th class="px-2 py-2 sm:px-3 sm:py-2">Billing Period</th>
                                <th class="px-2 py-2 sm:px-3 sm:py-2">Status & Payment</th>
                                <th class="px-2 py-2 sm:px-3 sm:py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                            @foreach($subscriptions as $sub)
                                @php
                                    $inv = $sub->invoice;
                                @endphp
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="px-2 py-2 sm:px-3 sm:py-2">
                                        <div class="font-bold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                            <span>{{ $sub->plan['name'] ?? 'Pro Plan' }}</span>
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60">
                                                {{ $sub->plan['invoice_period'] ?? '1' }} {{ $sub->plan['invoice_interval'] ?? 'month' }}
                                            </span>
                                        </div>
                                        <div class="text-[9px] md:text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-1 flex items-center gap-2">
                                            <span>#SUB-{{ sprintf('%04d', $sub->id) }}</span>
                                            @if($inv)
                                                <span>•</span>
                                                <span class="font-mono text-gray-600 dark:text-gray-300">{{ $inv->invoice_number }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 dark:text-white text-xs md:text-sm">
                                            {{ $sub->plan['currency'] ?? 'BDT' }} {{ number_format($sub->price, 2) }}
                                        </div>
                                        @if($inv)
                                            <div class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                Inv: {{ $inv->currency }} {{ number_format($inv->amount, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap">
                                        <div class="text-xs font-medium text-gray-900 dark:text-gray-200">
                                            Start Date: {{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : ($sub->created_at ? $sub->created_at->format('M d, Y') : '-') }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                            Next Billing: {{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'Continuous' }}
                                        </div>
                                        @if($sub->trial_ends_at)
                                            <div class="text-[10px] text-indigo-600 dark:text-indigo-400 mt-0.5">
                                                Trial Ends: {{ $sub->trial_ends_at->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            @if($sub->canceled())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                                    <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Canceled
                                                </span>
                                            @elseif($sub->isPendingPayment())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Pending
                                                </span>
                                            @elseif($sub->active())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    Expired
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($inv && $inv->isPaid())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Paid {{ $inv->paid_at ? 'on '.$inv->paid_at->format('M d, Y') : '' }}
                                                </span>
                                            @elseif($inv && $inv->isOverdue())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60">
                                                    <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Overdue
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Unpaid
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 sm:px-3 sm:py-2 text-right whitespace-nowrap">
                                        @if($inv)
                                            <div class="flex flex-col items-end gap-1.5 sm:gap-2">
                                                <a 
                                                    href="{{ \Hotash\Subscription\Filament\Pages\ViewInvoice::getUrl(['record' => $inv->id]) }}"
                                                    class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition-all border border-gray-200 dark:border-gray-700">
                                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View Invoice
                                                </a>
                                                @if(! $inv->isPaid())
                                                    <button 
                                                        type="button"
                                                        wire:click="payInvoice({{ $inv->id }})"
                                                        class="inline-flex items-center gap-1 px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-sm shadow-indigo-500/30 transition-all active:scale-95">
                                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                        Pay Now
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($subscriptions->hasPages())
                    <div class="mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            @else
                <div class="rounded-md border border-dashed border-gray-300 dark:border-gray-800 p-8 text-center bg-white/50 dark:bg-gray-900/50">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        No subscription records found.
                    </p>
                </div>
            @endif
        </div>

        <!-- Checkout / Payment Modal -->
        @if($showCheckoutModal && $selectedPlan)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
                <div class="relative w-full max-w-lg rounded-md bg-white dark:bg-gray-900 p-5 sm:p-6 shadow-2xl border border-gray-200 dark:border-gray-800 space-y-4 sm:space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 sm:pb-4">
                        <h3 class="text-base sm:text-xl font-bold text-gray-900 dark:text-white">
                            Checkout: {{ $selectedPlan['name'] }} ({{ $selectedPlan['invoice_period'] }} {{ $selectedPlan['invoice_interval'] }})
                        </h3>
                        <button wire:click="closeCheckoutModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="space-y-2">
                        <label class="block text-[11px] sm:text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Payment Method
                        </label>
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <button 
                                type="button" 
                                wire:click="$set('paymentMethod', 'card')"
                                class="p-2.5 sm:p-3 rounded-md border text-center text-[11px] sm:text-xs font-semibold transition-all {{ $paymentMethod === 'card' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400' }}">
                                💳 Card
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('paymentMethod', 'bkash')"
                                class="p-2.5 sm:p-3 rounded-md border text-center text-xs font-semibold transition-all {{ $paymentMethod === 'bkash' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400' }}">
                                📱 Mobile
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('paymentMethod', 'bank')"
                                class="p-2.5 sm:p-3 rounded-md border text-center text-xs font-semibold transition-all {{ $paymentMethod === 'bank' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400' }}">
                                🏦 Bank
                            </button>
                        </div>
                    </div>

                    <!-- Pricing Summary -->
                    <div class="rounded-md bg-gray-50 dark:bg-gray-800/50 p-3.5 sm:p-4 space-y-2 border border-gray-100 dark:border-gray-800 text-xs sm:text-sm">
                        <div class="flex justify-between font-bold text-sm sm:text-base text-gray-900 dark:text-white">
                            <span>Total Due</span>
                            <span>{{ $selectedPlan['currency'] }} {{ number_format($selectedPlan['price'], 2) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 sm:gap-3 pt-2">
                        <button 
                            type="button" 
                            wire:click="closeCheckoutModal" 
                            class="px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-md text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            wire:click="confirmSubscription" 
                            class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-md text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all">
                            Pay & Activate
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
