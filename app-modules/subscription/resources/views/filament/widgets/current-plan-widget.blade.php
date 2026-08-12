<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-5 sm:p-6 text-white shadow-xl ring-1 ring-white/10 dark:from-gray-900 dark:via-indigo-950 dark:to-gray-900">
        <!-- Background Ambient Glow -->
        <div class="pointer-events-none absolute -right-12 -top-12 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5 sm:gap-6">
            <div class="space-y-2.5 sm:space-y-3">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-semibold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 ring-1 ring-indigo-500/30">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Current Plan
                    </span>

                    @if($isTrial)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30">
                            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Trialing
                        </span>
                    @elseif($isCanceled)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-medium bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30">
                            <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Canceling Soon
                        </span>
                    @elseif($isPending)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30">
                            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pending
                        </span>
                    @elseif($isExpired)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-medium bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30">
                            <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Expired
                        </span>
                    @elseif($isActive)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-medium bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Active
                        </span>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-3">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        {{ $plan['name'] ?? 'Pro Plan' }}
                    </h2>
                    <span class="text-base sm:text-lg font-medium text-slate-300">
                        {{ isset($plan['price']) ? ($plan['currency'] . ' ' . number_format($plan['price'], 2) . ' / ' . $plan['invoice_period'] . ' ' . $plan['invoice_interval']) : 'No Charge' }}
                    </span>
                </div>

                <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                    {{ $plan['description'] ?? 'You are currently on our subscription plan.' }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @if($endsAt)
                    <div class="bg-white/5 backdrop-blur-md px-4 py-3 rounded-lg border border-white/10 text-center sm:text-left">
                        <div class="text-xs text-slate-400 font-medium">
                            {{ $isTrial ? 'Trial Ends On' : ($isExpired ? 'Expired On' : ($isCanceled ? 'Access Ends On' : 'Next Renewal')) }}
                        </div>
                        <div class="text-sm font-semibold text-white mt-0.5">
                            {{ $endsAt->format('M d, Y') }}
                            @if($isPending)
                                <span class="block sm:inline text-xs font-semibold text-amber-300">(Pending)</span>
                            @elseif($isExpired)
                                <span class="block sm:inline text-xs font-semibold text-rose-400">(Expired)</span>
                            @elseif($daysRemaining !== null)
                                <span class="block sm:inline text-xs font-normal text-indigo-300">({{ $daysRemaining }} days remaining)</span>
                            @endif
                        </div>
                    </div>
                @endif

                <a href="/app/billing" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg font-semibold text-xs sm:text-sm bg-gradient-to-r from-indigo-500 to-blue-600 text-white shadow-lg shadow-indigo-500/25 hover:from-indigo-600 hover:to-blue-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Manage Billing & Plans
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
