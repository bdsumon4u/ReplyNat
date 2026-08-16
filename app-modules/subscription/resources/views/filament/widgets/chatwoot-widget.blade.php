<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-5 text-white shadow-xl ring-1 ring-white/10 dark:from-gray-900 dark:via-indigo-950 dark:to-gray-900">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="relative z-10 flex flex-col justify-between h-full min-h-[140px] gap-4">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-white text-base tracking-tight">Chatwoot Console</h3>
                        @if($chatwootAccount && $chatwootAccount->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30">Active</span>
                        @elseif($chatwootAccount && $chatwootAccount->status === 'suspended')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30">Suspended</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-slate-300 ring-1 ring-white/20">Not Synced</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">Access your customer live chat dashboard and support inbox workspace.</p>
                </div>
                <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>

            @if($chatwootAccount && $chatwootAccount->status === 'active')
                <div>
                    <a href="{{ route('chatwoot.sso') }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-xs bg-indigo-500 text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1"/></svg>
                        Open Chatwoot Workspace
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
