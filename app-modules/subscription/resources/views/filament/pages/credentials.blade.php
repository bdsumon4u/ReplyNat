<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-10">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}
                <div class="flex items-center gap-3">
                    <x-filament::button type="submit" size="lg" color="primary">
                        Save Credentials
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Sidebar / Status Column -->
        <div class="space-y-6">
            <!-- Workflow Status Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-5 text-white shadow-xl ring-1 ring-white/10 dark:from-gray-900 dark:via-indigo-950 dark:to-gray-900">
                <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl"></div>
                <div class="relative z-10 flex flex-col justify-between h-full gap-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Workflow Status</span>
                            @php $status = $this->getWorkflowStatus(); @endphp
                            @if($status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30">Active</span>
                            @elseif($status === 'inactive')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30">Inactive</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-slate-300 ring-1 ring-white/20">Not Published</span>
                            @endif
                        </div>

                        <h3 class="font-bold text-white text-lg tracking-tight">n8n Automation Engine</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Publishing will read your credentials, substitute them into our workflow template, register the workflow with n8n, and activate background processing.
                        </p>
                    </div>

                    <div class="pt-2">
                        <x-filament::button
                            wire:click="publishWorkflow"
                            size="lg"
                            color="success"
                            class="w-full font-semibold"
                            icon="heroicon-o-arrow-path"
                        >
                            {{ $status === 'not_created' ? 'Publish Workflow' : 'Re-publish / Sync Workflow' }}
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
