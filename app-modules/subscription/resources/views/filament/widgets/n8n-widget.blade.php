<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-5 text-white shadow-xl ring-1 ring-white/10 dark:from-gray-900 dark:via-indigo-950 dark:to-gray-900">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="relative z-10 flex flex-col justify-between h-full min-h-[140px] gap-4">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-white text-base tracking-tight">n8n Automation</h3>
                        @if($n8nWorkflow && $n8nWorkflow->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30">Active</span>
                        @elseif($n8nWorkflow && $n8nWorkflow->status === 'inactive')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30">Inactive</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-slate-300 ring-1 ring-white/20">Not Synced</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">Automate operations using custom background workflow logic triggers.</p>
                </div>
                <div class="p-2 rounded-lg bg-blue-500/10 text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>

            <div>
                <a href="/app/credentials" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-xs bg-blue-600 text-white shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Configure Credentials
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
