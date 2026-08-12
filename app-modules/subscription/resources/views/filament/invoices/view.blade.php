<x-filament-panels::page>
    @php
        $user = $invoice->user;
        $subscription = $invoice->subscription;
        $plan = $subscription?->plan;
    @endphp

    <div class="mb-2 flex items-center justify-between">
        <a href="{{ \Hotash\Subscription\Filament\Pages\Billing::getUrl() }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            ← Back to Billing & Subscription
        </a>
    </div>

    <div class="p-6 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 max-w-3xl mx-auto rounded-xl space-y-8 font-sans border border-gray-200 dark:border-gray-800 shadow-sm">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-6">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    INVOICE
                </h2>
                <p class="text-sm font-mono text-indigo-600 dark:text-indigo-400 font-semibold mt-1">
                    #{{ $invoice->invoice_number }}
                </p>
            </div>
            <div>
                @if($invoice->isPaid())
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full border border-emerald-300 dark:border-emerald-800">
                        Paid
                    </span>
                @elseif($invoice->isOverdue())
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded-full border border-rose-300 dark:border-rose-800">
                        Overdue
                    </span>
                @else
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 rounded-full border border-amber-300 dark:border-amber-800">
                        Unpaid
                    </span>
                @endif
            </div>
        </div>

        <!-- Client & Invoice Info Grid -->
        <div class="grid grid-cols-2 gap-6 text-sm">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Billed To</span>
                <p class="font-semibold text-gray-900 dark:text-white text-base">{{ $user?->name ?? 'N/A' }}</p>
                <p class="text-gray-500 dark:text-gray-400">{{ $user?->email }}</p>
            </div>
            <div class="space-y-1.5 text-right">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Dates & Info</span>
                <p class="text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Issue Date:</strong> {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : '-' }}</p>
                <p class="text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '-' }}</p>
                @if($invoice->paid_at)
                    <p class="text-emerald-600 dark:text-emerald-400"><strong class="text-gray-900 dark:text-white">Paid On:</strong> {{ $invoice->paid_at->format('M d, Y h:i A') }}</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <tr>
                        <td class="px-4 py-4">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $plan['name'] ?? 'Subscription Plan' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Subscription renewal for period: {{ $subscription?->starts_at?->format('M d, Y') ?? 'Current' }} – {{ $subscription?->ends_at?->format('M d, Y') ?? 'Renewal' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right font-semibold text-gray-900 dark:text-white">
                            {{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Summary -->
        <div class="flex justify-end pt-2">
            <div class="w-full md:w-64 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span>{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 dark:text-white text-base border-t border-gray-200 dark:border-gray-800 pt-2">
                    <span>Total Due</span>
                    <span class="text-indigo-600 dark:text-indigo-400">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
