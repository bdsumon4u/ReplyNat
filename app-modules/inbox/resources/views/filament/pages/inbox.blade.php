<x-filament-panels::page class="p-0">
    <div
        wire:poll.8s
        x-data="{
            cannedOpen: @entangle('showCannedPopup'),
            scrollToBottom() {
                const el = this.$refs.chatContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            }
        }"
        x-init="
            $nextTick(() => scrollToBottom());
            $wire.on('conversation-selected', () => $nextTick(() => scrollToBottom()));
            $wire.on('message-sent', () => $nextTick(() => scrollToBottom()));
        "
        class="flex h-[calc(100vh-10rem)] min-h-[600px] w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
    >
        <!-- ========================================== -->
        <!-- PANE 1: FILTERS & CHANNELS SIDEBAR        -->
        <!-- ========================================== -->
        <div class="flex w-64 flex-shrink-0 flex-col border-r border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
            <!-- Header -->
            <div class="mb-4 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Views & Filters</span>
            </div>

            <!-- Status & Assignment Views -->
            <div class="space-y-1">
                <button
                    type="button"
                    wire:click="setStatusFilter('open')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition {{ $statusFilter === 'open' ? 'bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400 font-semibold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <span>Open Inboxes</span>
                    </div>
                </button>

                <button
                    type="button"
                    wire:click="setAssigneeFilter('mine')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition {{ $assigneeFilter === 'mine' ? 'bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400 font-semibold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Assigned to Me</span>
                    </div>
                </button>

                <button
                    type="button"
                    wire:click="setAssigneeFilter('unassigned')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition {{ $assigneeFilter === 'unassigned' ? 'bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400 font-semibold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Unassigned</span>
                    </div>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('snoozed')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition {{ $statusFilter === 'snoozed' ? 'bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400 font-semibold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Snoozed</span>
                    </div>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('closed')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition {{ $statusFilter === 'closed' ? 'bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400 font-semibold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Resolved / Closed</span>
                    </div>
                </button>
            </div>

            <hr class="my-4 border-gray-200 dark:border-gray-800">

            <!-- Connected Channels / Assets -->
            <div class="mb-2 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Channels</span>
                <a
                    href="{{ route('comments.auth.redirect') }}"
                    class="flex items-center gap-1 text-[11px] font-semibold text-primary-600 hover:text-primary-700 hover:underline dark:text-primary-400"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Connect</span>
                </a>
            </div>

            <div class="flex-1 space-y-1 overflow-y-auto pr-1">
                <button
                    type="button"
                    wire:click="setAssetFilter(null); setChannelFilter('all')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-xs font-medium transition {{ ! $assetFilter && $channelFilter === 'all' ? 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/60' }}"
                >
                    <span>All Channels</span>
                </button>

                <button
                    type="button"
                    wire:click="setAssetFilter(null); setChannelFilter('facebook_page')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-xs font-medium transition {{ ! $assetFilter && $channelFilter === 'facebook_page' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 font-semibold' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/60' }}"
                >
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-blue-600"></span>
                        <span>Facebook Messenger</span>
                    </div>
                </button>

                <button
                    type="button"
                    wire:click="setAssetFilter(null); setChannelFilter('instagram')"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-xs font-medium transition {{ ! $assetFilter && $channelFilter === 'instagram' ? 'bg-pink-100 text-pink-800 dark:bg-pink-950/60 dark:text-pink-300 font-semibold' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/60' }}"
                >
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500"></span>
                        <span>Instagram DM</span>
                    </div>
                </button>

                @if($this->connectedAssets->isNotEmpty())
                    <div class="pt-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">Pages & Accounts</div>
                    @foreach($this->connectedAssets as $asset)
                        <button
                            type="button"
                            wire:click="setAssetFilter({{ $asset->id }})"
                            class="flex w-full items-center gap-2 truncate rounded-lg px-3 py-1.5 text-left text-xs transition {{ $assetFilter === $asset->id ? 'bg-primary-100 text-primary-900 font-semibold dark:bg-primary-950 dark:text-primary-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/60' }}"
                        >
                            <img src="{{ $asset->avatar_url ?: ($asset->isInstagram() ? 'https://cdn-icons-png.flaticon.com/512/174/174855.png' : 'https://cdn-icons-png.flaticon.com/512/124/124010.png') }}" class="h-4 w-4 rounded-full object-cover">
                            <span class="truncate">{{ $asset->asset_name }}</span>
                        </button>
                    @endforeach
                @else
                    <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-3 text-center dark:border-gray-700">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">No channels connected yet.</p>
                        <a
                            href="{{ route('comments.auth.redirect') }}"
                            class="mt-2 inline-flex items-center gap-1 rounded bg-primary-600 px-2 py-1 text-[11px] font-medium text-white shadow-xs hover:bg-primary-700"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Connect Pages</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PANE 2: CONVERSATION LIST STREAM          -->
        <!-- ========================================== -->
        <div class="flex w-80 flex-shrink-0 flex-col border-r border-gray-200 dark:border-gray-800">
            <!-- Search Bar -->
            <div class="border-b border-gray-200 p-3 dark:border-gray-800">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchQuery"
                        placeholder="Search conversations..."
                        class="w-full rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 pl-8 pr-3 text-xs focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                    <svg class="absolute left-2.5 top-2 h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/50">
                @forelse($this->conversations as $conv)
                    <div
                        wire:key="conv-{{ $conv->id }}"
                        wire:click="selectConversation({{ $conv->id }})"
                        class="cursor-pointer p-3 transition hover:bg-gray-50 dark:hover:bg-gray-800/40 {{ $selectedConversationId === $conv->id ? 'bg-primary-50/70 border-l-4 border-primary-600 dark:bg-primary-950/40 dark:border-primary-500' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Contact Avatar with Channel Badge -->
                            <div class="relative flex-shrink-0">
                                <img
                                    src="{{ $conv->contact->getAvatar() }}"
                                    alt="{{ $conv->contact->name }}"
                                    class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700"
                                >
                                @if($conv->channel === 'facebook_page' || $conv->channel === 'facebook')
                                    <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-blue-600 text-[9px] font-bold text-white shadow">
                                        f
                                    </span>
                                @elseif($conv->channel === 'instagram' || $conv->channel === 'instagram_account')
                                    <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-[8px] font-bold text-white shadow">
                                        ig
                                    </span>
                                @endif
                            </div>

                            <!-- Conversation Info -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="truncate text-xs font-semibold text-gray-900 dark:text-white {{ $conv->unread_count > 0 ? 'font-bold' : '' }}">
                                        {{ $conv->contact->name }}
                                    </h4>
                                    <span class="text-[10px] text-gray-400 whitespace-nowrap">
                                        {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true, true) : '' }}
                                    </span>
                                </div>

                                <p class="mt-0.5 truncate text-xs {{ $conv->unread_count > 0 ? 'font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $conv->last_message_preview ?: 'No messages yet' }}
                                </p>

                                <div class="mt-1.5 flex items-center justify-between">
                                    <span class="truncate text-[10px] text-gray-400">
                                        {{ $conv->connectedAsset?->asset_name ?? 'Omnichannel' }}
                                    </span>

                                    @if($conv->unread_count > 0)
                                        <span class="inline-flex items-center justify-center rounded-full bg-primary-600 px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">
                                            {{ $conv->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center p-8 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-xs font-semibold text-gray-700 dark:text-gray-300">No conversations</p>
                        <p class="mt-1 text-[11px] text-gray-400">Incoming messages from Facebook and Instagram will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PANE 3: CHAT STREAM & RICH COMPOSER       -->
        <!-- ========================================== -->
        <div class="flex flex-1 flex-col min-w-0 bg-white dark:bg-gray-900">
            @if($this->selectedConversation)
                @php $conv = $this->selectedConversation; @endphp

                <!-- Chat Header -->
                <div class="flex h-14 flex-shrink-0 items-center justify-between border-b border-gray-200 px-4 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <img src="{{ $conv->contact->getAvatar() }}" class="h-9 w-9 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $conv->contact->name }}</span>
                                @if($conv->channel === 'facebook_page' || $conv->channel === 'facebook')
                                    <span class="inline-flex items-center rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">Facebook Page</span>
                                @elseif($conv->channel === 'instagram' || $conv->channel === 'instagram_account')
                                    <span class="inline-flex items-center rounded bg-pink-50 px-1.5 py-0.5 text-[10px] font-medium text-pink-700 dark:bg-pink-950 dark:text-pink-300">Instagram</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-gray-400">Via: {{ $conv->connectedAsset?->asset_name ?? 'Unknown Asset' }}</p>
                        </div>
                    </div>

                    <!-- Header Actions (Status, Assignee) -->
                    <div class="flex items-center gap-2">
                        <!-- Status Toggle -->
                        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5 dark:border-gray-700 dark:bg-gray-800">
                            <button
                                type="button"
                                wire:click="updateConversationStatus('open')"
                                class="rounded-md px-2.5 py-1 text-xs font-medium transition {{ $conv->status === 'open' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400' }}"
                            >
                                Open
                            </button>
                            <button
                                type="button"
                                wire:click="updateConversationStatus('snoozed')"
                                class="rounded-md px-2.5 py-1 text-xs font-medium transition {{ $conv->status === 'snoozed' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400' }}"
                            >
                                Snooze
                            </button>
                            <button
                                type="button"
                                wire:click="updateConversationStatus('closed')"
                                class="rounded-md px-2.5 py-1 text-xs font-medium transition {{ $conv->status === 'closed' ? 'bg-white text-emerald-600 shadow-sm dark:bg-gray-700 dark:text-emerald-400' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400' }}"
                            >
                                Resolve
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 24-Hour Messaging Policy Alert (Meta standard) -->
                @if(! $conv->isWithinMeta24HourWindow())
                    <div class="flex items-center gap-2 border-b border-amber-200 bg-amber-50 px-4 py-2 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
                        <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>24-Hour Messaging Window Expired:</strong> Standard replies may fail unless the customer sends another message.</span>
                    </div>
                @endif

                <!-- Message Stream -->
                <div
                    x-ref="chatContainer"
                    class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/40 dark:bg-gray-950/30"
                >
                    @forelse($conv->messages as $msg)
                        @if($msg->is_private_note)
                            <!-- Internal Team Private Note -->
                            <div class="mx-auto max-w-lg rounded-xl border border-amber-200 bg-amber-50/90 p-3 shadow-xs dark:border-amber-900/40 dark:bg-amber-950/30">
                                <div class="flex items-center justify-between text-[11px] font-semibold text-amber-900 dark:text-amber-300">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span>Private Team Note ({{ $msg->agent?->name ?? 'Agent' }})</span>
                                    </div>
                                    <span class="text-amber-700/80 dark:text-amber-400/80">{{ $msg->created_at->format('h:i A') }}</span>
                                </div>
                                <div class="mt-1 text-xs text-amber-950 dark:text-amber-100 whitespace-pre-wrap">{{ $msg->content }}</div>
                            </div>
                        @elseif($msg->isIncoming())
                            <!-- Customer Incoming Message -->
                            <div class="flex items-end gap-2">
                                <img src="{{ $conv->contact->getAvatar() }}" class="h-6 w-6 rounded-full object-cover">
                                <div class="max-w-md rounded-2xl rounded-bl-xs bg-white p-3 text-xs text-gray-900 shadow-xs border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                    @if($msg->content)
                                        <p class="whitespace-pre-wrap">{{ $msg->content }}</p>
                                    @endif

                                    @if(!empty($msg->attachments))
                                        <div class="mt-2 space-y-2">
                                            @foreach($msg->attachments as $att)
                                                @if(($att['type'] ?? '') === 'image')
                                                    <img src="{{ $att['url'] }}" class="max-h-60 rounded-lg object-cover">
                                                @else
                                                    <a href="{{ $att['url'] }}" target="_blank" class="flex items-center gap-2 rounded bg-gray-100 p-2 text-[11px] text-primary-600 hover:underline dark:bg-gray-700 dark:text-primary-400">
                                                        <span>Download Attachment</span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mt-1 flex items-center justify-end text-[9px] text-gray-400">
                                        {{ $msg->created_at->format('h:i A') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Outgoing Agent Message -->
                            <div class="flex items-end justify-end gap-2">
                                <div class="max-w-md rounded-2xl rounded-br-xs bg-primary-600 p-3 text-xs text-white shadow-xs dark:bg-primary-600">
                                    @if($msg->content)
                                        <p class="whitespace-pre-wrap">{{ $msg->content }}</p>
                                    @endif

                                    @if(!empty($msg->attachments))
                                        <div class="mt-2 space-y-2">
                                            @foreach($msg->attachments as $att)
                                                @if(($att['type'] ?? '') === 'image')
                                                    <img src="{{ $att['url'] }}" class="max-h-60 rounded-lg object-cover">
                                                @else
                                                    <a href="{{ $att['url'] }}" target="_blank" class="flex items-center gap-2 rounded bg-primary-700/50 p-2 text-[11px] text-white underline">
                                                        <span>Attachment</span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mt-1 flex items-center justify-end gap-1 text-[9px] text-primary-100">
                                        <span>{{ $msg->created_at->format('h:i A') }}</span>
                                        @if($msg->status === 'delivered')
                                            <span>✓✓</span>
                                        @elseif($msg->status === 'read')
                                            <span class="text-white font-bold">✓✓</span>
                                        @elseif($msg->status === 'failed')
                                            <span class="text-red-200 font-bold">! Failed</span>
                                        @else
                                            <span>✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex h-full items-center justify-center text-center text-xs text-gray-400">
                            No messages in this conversation yet. Send a reply below!
                        </div>
                    @endforelse
                </div>

                <!-- Rich Message Composer -->
                <div class="border-t border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                    <!-- Mode Switcher (Reply vs Private Note) & Shortcuts -->
                    <div class="mb-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="$set('isPrivateNote', false)"
                                class="rounded px-2.5 py-1 text-xs font-medium transition {{ ! $isPrivateNote ? 'bg-primary-50 text-primary-700 font-semibold dark:bg-primary-950 dark:text-primary-300' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400' }}"
                            >
                                Reply
                            </button>

                            <button
                                type="button"
                                wire:click="$set('isPrivateNote', true)"
                                class="rounded px-2.5 py-1 text-xs font-medium transition {{ $isPrivateNote ? 'bg-amber-100 text-amber-900 font-semibold dark:bg-amber-950 dark:text-amber-300' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400' }}"
                            >
                                🔒 Private Note
                            </button>
                        </div>

                        <!-- Canned Responses Trigger -->
                        <div class="relative">
                            <button
                                type="button"
                                @click="cannedOpen = !cannedOpen"
                                class="flex items-center gap-1 rounded border border-gray-200 px-2 py-0.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
                            >
                                <span class="font-mono text-primary-600">/</span>
                                <span>Canned Responses</span>
                            </button>

                            <!-- Canned Responses Modal / Dropdown -->
                            <div
                                x-show="cannedOpen"
                                @click.away="cannedOpen = false"
                                x-cloak
                                class="absolute bottom-8 right-0 z-30 w-72 rounded-xl border border-gray-200 bg-white p-2 shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <input
                                    type="text"
                                    wire:model.live.debounce.200ms="cannedSearch"
                                    placeholder="Search shortcuts..."
                                    class="mb-2 w-full rounded-md border border-gray-200 px-2 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                >
                                <div class="max-h-48 overflow-y-auto space-y-1">
                                    @forelse($this->cannedResponses as $canned)
                                        <button
                                            type="button"
                                            wire:click="insertCannedResponse(@js($canned->content))"
                                            class="w-full text-left rounded-md p-1.5 text-xs hover:bg-primary-50 dark:hover:bg-gray-700"
                                        >
                                            <div class="font-semibold text-gray-900 dark:text-white">/{{ $canned->shortcut }} - {{ $canned->title }}</div>
                                            <div class="truncate text-[11px] text-gray-400">{{ $canned->content }}</div>
                                        </button>
                                    @empty
                                        <div class="p-2 text-center text-xs text-gray-400">No canned responses found</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachment Previews -->
                    @if(!empty($uploadedFiles))
                        <div class="mb-2 flex flex-wrap gap-2">
                            @foreach($uploadedFiles as $index => $file)
                                <div class="relative rounded-lg border border-gray-200 bg-gray-50 p-1.5 text-xs flex items-center gap-2 dark:border-gray-700 dark:bg-gray-800">
                                    <span class="truncate max-w-[150px]">{{ $file->getClientOriginalName() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Message Input Form -->
                    <form wire:submit.prevent="sendMessage" class="relative">
                        <textarea
                            wire:model="messageContent"
                            rows="3"
                            placeholder="{{ $isPrivateNote ? 'Write internal note for your team (not visible to customer)...' : 'Type your message (or use / for quick response)...' }}"
                            class="w-full rounded-lg border p-2.5 text-xs focus:outline-none focus:ring-1 {{ $isPrivateNote ? 'border-amber-300 bg-amber-50/30 focus:border-amber-500 focus:ring-amber-500 dark:border-amber-900 dark:bg-amber-950/20' : 'border-gray-200 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white' }}"
                            @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage() }"
                        ></textarea>

                        <div class="mt-2 flex items-center justify-between">
                            <!-- File Upload Button -->
                            <label class="cursor-pointer rounded-md p-1 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <input type="file" wire:model="uploadedFiles" multiple class="hidden">
                            </label>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition {{ $isPrivateNote ? 'bg-amber-600 hover:bg-amber-700' : 'bg-primary-600 hover:bg-primary-700' }}"
                            >
                                <span>{{ $isPrivateNote ? 'Add Note' : 'Send' }}</span>
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- No Conversation Selected State -->
                <div class="flex flex-1 flex-col items-center justify-center p-8 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">Select a conversation</h3>
                    <p class="mt-1 text-xs text-gray-400">Choose a thread from the list on the left to start messaging.</p>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- PANE 4: CONTACT & CHANNEL CONTEXT SIDEBAR -->
        <!-- ========================================== -->
        @if($this->selectedConversation)
            @php $conv = $this->selectedConversation; @endphp
            <div class="flex w-64 flex-shrink-0 flex-col border-l border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ $conv->contact->getAvatar() }}" class="h-16 w-16 rounded-full object-cover border-2 border-primary-500 shadow-sm">
                    <h3 class="mt-2 text-sm font-bold text-gray-900 dark:text-white">{{ $conv->contact->name }}</h3>
                    <span class="mt-0.5 rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        {{ ucfirst($conv->contact->platform) }} Contact
                    </span>
                </div>

                <div class="mt-6 space-y-4 text-xs">
                    <div>
                        <span class="font-semibold text-gray-500 dark:text-gray-400">Platform ID</span>
                        <p class="font-mono text-gray-900 dark:text-white truncate">{{ $conv->contact->platform_sender_id }}</p>
                    </div>

                    <div>
                        <span class="font-semibold text-gray-500 dark:text-gray-400">Connected Channel</span>
                        <p class="text-gray-900 dark:text-white">{{ $conv->connectedAsset?->asset_name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="font-semibold text-gray-500 dark:text-gray-400">First Contact</span>
                        <p class="text-gray-900 dark:text-white">{{ $conv->created_at->format('M d, Y') }}</p>
                    </div>

                    <div>
                        <span class="font-semibold text-gray-500 dark:text-gray-400">Assigned Agent</span>
                        <p class="text-gray-900 dark:text-white">{{ $conv->assignee?->name ?? 'Unassigned' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
