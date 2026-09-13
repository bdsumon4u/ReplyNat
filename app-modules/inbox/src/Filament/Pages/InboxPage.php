<?php

namespace Hotash\Inbox\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Inbox\Models\CannedResponse;
use Hotash\Inbox\Models\Conversation;
use Hotash\Inbox\Models\Message;
use Hotash\Inbox\Services\ChannelManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class InboxPage extends Page
{
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $navigationLabel = 'Inbox';

    protected static ?string $title = 'Omnichannel Inbox';

    protected static ?string $slug = 'inbox';

    protected static ?int $navigationSort = 2;

    protected string $view = 'inbox::filament.pages.inbox';

    // Filters and selection state
    public ?int $selectedConversationId = null;

    public string $statusFilter = 'open'; // 'open', 'snoozed', 'closed', 'all'

    public string $channelFilter = 'all'; // 'all', 'facebook_page', 'instagram'

    public ?int $assetFilter = null; // ConnectedAsset id

    public string $assigneeFilter = 'all'; // 'all', 'mine', 'unassigned'

    public string $searchQuery = '';

    // Message composer state
    public string $messageContent = '';

    public bool $isPrivateNote = false;

    /** @var array<int, mixed> */
    public array $uploadedFiles = [];

    // Canned responses popup state
    public bool $showCannedPopup = false;

    public string $cannedSearch = '';

    public static function getNavigationBadge(): ?string
    {
        $userId = Auth::id();
        if (! $userId) {
            return null;
        }

        $count = Conversation::where('user_id', $userId)
            ->where('status', 'open')
            ->sum('unread_count');

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('connectMeta')
                ->label('Connect Facebook / Instagram')
                ->icon(Heroicon::OutlinedPlusCircle)
                ->color('primary')
                ->url(route('comments.auth.redirect')),
        ];
    }

    public function mount(): void
    {
        $first = $this->getConversationsQuery()->first();
        if ($first) {
            $this->selectConversation($first->id);
        }
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;

        $conversation = Conversation::where('user_id', Auth::id())->find($id);
        if ($conversation) {
            $conversation->markAsRead();
        }

        $this->messageContent = '';
        $this->uploadedFiles = [];
        $this->isPrivateNote = false;
        $this->showCannedPopup = false;

        $this->dispatch('conversation-selected');
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->refreshFirstConversation();
    }

    public function setChannelFilter(string $channel): void
    {
        $this->channelFilter = $channel;
        $this->refreshFirstConversation();
    }

    public function setAssetFilter(?int $assetId): void
    {
        $this->assetFilter = $assetId;
        $this->refreshFirstConversation();
    }

    public function setAssigneeFilter(string $assignee): void
    {
        $this->assigneeFilter = $assignee;
        $this->refreshFirstConversation();
    }

    protected function refreshFirstConversation(): void
    {
        $first = $this->getConversationsQuery()->first();
        if ($first) {
            $this->selectConversation($first->id);
        } else {
            $this->selectedConversationId = null;
        }
    }

    public function getConversationsProperty()
    {
        return $this->getConversationsQuery()->get();
    }

    protected function getConversationsQuery()
    {
        return Conversation::query()
            ->with(['contact', 'connectedAsset', 'assignee'])
            ->where('user_id', Auth::id())
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->channelFilter !== 'all', fn ($q) => $q->where('channel', $this->channelFilter))
            ->when($this->assetFilter, fn ($q) => $q->where('connected_asset_id', $this->assetFilter))
            ->when($this->assigneeFilter === 'mine', fn ($q) => $q->where('assigned_to', Auth::id()))
            ->when($this->assigneeFilter === 'unassigned', fn ($q) => $q->whereNull('assigned_to'))
            ->when(trim($this->searchQuery) !== '', function ($q) {
                $search = '%'.trim($this->searchQuery).'%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('last_message_preview', 'like', $search)
                        ->orWhereHas('contact', fn ($cq) => $cq->where('name', 'like', $search)->orWhere('platform_sender_id', 'like', $search));
                });
            })
            ->orderByRaw('CASE WHEN unread_count > 0 THEN 0 ELSE 1 END')
            ->orderBy('last_message_at', 'desc');
    }

    public function getSelectedConversationProperty(): ?Conversation
    {
        if (! $this->selectedConversationId) {
            return null;
        }

        return Conversation::query()
            ->with(['contact', 'connectedAsset', 'assignee', 'messages.agent'])
            ->where('user_id', Auth::id())
            ->find($this->selectedConversationId);
    }

    public function getConnectedAssetsProperty(): Collection
    {
        return ConnectedAsset::query()
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();
    }

    public function getCannedResponsesProperty(): Collection
    {
        return CannedResponse::query()
            ->where('user_id', Auth::id())
            ->when(trim($this->cannedSearch) !== '', fn ($q) => $q->where('shortcut', 'like', '%'.trim($this->cannedSearch).'%')->orWhere('title', 'like', '%'.trim($this->cannedSearch).'%'))
            ->get();
    }

    public function sendMessage(ChannelManager $channelManager): void
    {
        $conversation = $this->selectedConversation;
        if (! $conversation) {
            return;
        }

        $text = trim($this->messageContent);
        if ($text === '' && empty($this->uploadedFiles)) {
            return;
        }

        // Process attachments
        $attachments = [];
        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('inbox-attachments', 'public');
            $mime = $file->getMimeType();
            $type = str_starts_with($mime, 'image/') ? 'image' : (str_starts_with($mime, 'video/') ? 'video' : 'file');
            $attachments[] = [
                'type' => $type,
                'url' => Storage::disk('public')->url($path),
                'name' => $file->getClientOriginalName(),
            ];
        }

        if ($this->isPrivateNote) {
            // Create internal note
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'agent',
                'sender_id' => Auth::id(),
                'message_type' => 'private_note',
                'content' => $text,
                'attachments' => count($attachments) ? $attachments : null,
                'status' => 'sent',
                'is_private_note' => true,
            ]);

            Notification::make()->title('Private note added')->success()->send();
        } else {
            // Create outgoing message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'agent',
                'sender_id' => Auth::id(),
                'message_type' => count($attachments) && empty($text) ? $attachments[0]['type'] : 'text',
                'content' => $text ?: '['.ucfirst($attachments[0]['type'] ?? 'Attachment').']',
                'attachments' => count($attachments) ? $attachments : null,
                'status' => 'pending',
                'is_private_note' => false,
            ]);

            // Dispatch to Meta
            $result = $channelManager->sendMessage($conversation, $text, $attachments);

            if ($result['success'] ?? false) {
                $message->update([
                    'status' => 'sent',
                    'platform_message_id' => $result['message_id'] ?? null,
                ]);
            } else {
                $message->update(['status' => 'failed']);
                Notification::make()
                    ->title('Failed to send message via Meta')
                    ->body($result['error'] ?? 'Check your Page Access Token permissions.')
                    ->danger()
                    ->send();
            }

            $conversation->update([
                'last_message_preview' => $text ?: '['.ucfirst($attachments[0]['type'] ?? 'Attachment').']',
                'last_message_at' => now(),
            ]);
        }

        $this->messageContent = '';
        $this->uploadedFiles = [];
        $this->dispatch('message-sent');
    }

    public function updateConversationStatus(string $status): void
    {
        $conversation = $this->selectedConversation;
        if (! $conversation) {
            return;
        }

        $conversation->update(['status' => $status]);
        Notification::make()
            ->title("Conversation marked as {$status}")
            ->success()
            ->send();
    }

    public function assignConversation(?int $userId): void
    {
        $conversation = $this->selectedConversation;
        if (! $conversation) {
            return;
        }

        $conversation->update(['assigned_to' => $userId]);
        Notification::make()
            ->title($userId ? 'Conversation assigned' : 'Conversation unassigned')
            ->success()
            ->send();
    }

    public function insertCannedResponse(string $content): void
    {
        $this->messageContent = $content;
        $this->showCannedPopup = false;
        $this->cannedSearch = '';
    }
}
