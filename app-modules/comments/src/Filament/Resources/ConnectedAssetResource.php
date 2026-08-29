<?php

namespace Hotash\Comments\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid as SchemaGrid;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Hotash\Comments\Filament\Resources\ConnectedAssetResource\Pages\ManageConnectedAssets;
use Hotash\Comments\Models\ConnectedAsset;
use Hotash\Comments\Services\MetaGraphService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ConnectedAssetResource extends Resource
{
    protected static ?string $model = ConnectedAsset::class;

    protected static ?string $slug = 'comment-automation';

    protected static ?string $navigationLabel = 'Comment Automation';

    protected static ?string $modelLabel = 'Connected Page / Account';

    protected static ?string $pluralModelLabel = 'Comment Automation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaSection::make('Asset Configuration')
                    ->schema([
                        SchemaGrid::make(2)->schema([
                            TextInput::make('asset_name')
                                ->label('Asset Name')
                                ->disabled(),

                            TextInput::make('platform')
                                ->label('Platform')
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'facebook_page' => 'Facebook Page',
                                    'instagram_account' => 'Instagram Account',
                                    default => (string) $state,
                                })
                                ->disabled(),
                        ]),
                    ]),

                SchemaSection::make('Automation Settings')
                    ->schema([
                        SchemaGrid::make(3)->schema([
                            Toggle::make('is_active')
                                ->label('Enable Automation')
                                ->helperText('Process comments for this asset.')
                                ->default(true),

                            Toggle::make('auto_reply_enabled')
                                ->label('Public Comment Reply')
                                ->helperText('Post an automated public reply under the comment.')
                                ->default(true),

                            Toggle::make('private_reply_enabled')
                                ->label('Private DM Reply')
                                ->helperText('Send a private inbox message directly to the commenter.')
                                ->default(false),
                        ]),

                        Textarea::make('settings.prompt_instructions')
                            ->label('Custom AI Instructions')
                            ->placeholder('e.g. Always include our support email info@example.com or promote our 20% summer sale.')
                            ->helperText('Custom guidance injected into your n8n AI workflow when replying to comments on this asset.')
                            ->rows(3),

                        TextInput::make('settings.banned_keywords')
                            ->label('Ignore Keywords (Comma-separated)')
                            ->placeholder('e.g. spam, scam, promo')
                            ->helperText('Comments containing these words will be skipped.'),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (ConnectedAsset $record): string => $record->isInstagram()
                        ? 'https://cdn-icons-png.flaticon.com/512/174/174855.png'
                        : 'https://cdn-icons-png.flaticon.com/512/124/124010.png'),

                TextColumn::make('asset_name')
                    ->label('Page / Account')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ConnectedAsset $record): string => $record->isInstagram()
                        ? '@'.($record->username ?? $record->asset_name)
                        : "ID: {$record->asset_id}"),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'facebook_page' => 'Facebook Page',
                        'instagram_account' => 'Instagram',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'facebook_page' => 'info',
                        'instagram_account' => 'warning',
                        default => 'gray',
                    }),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                ToggleColumn::make('auto_reply_enabled')
                    ->label('Auto-Reply'),

                ToggleColumn::make('private_reply_enabled')
                    ->label('Private DM'),

                TextColumn::make('last_synced_at')
                    ->label('Last Synced')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Action::make('resyncWebhook')
                    ->label('Re-sync Webhook')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('gray')
                    ->action(function (ConnectedAsset $record, MetaGraphService $meta): void {
                        $targetPageId = $record->isInstagram() ? ($record->parent_asset_id ?: $record->asset_id) : $record->asset_id;
                        $success = $meta->subscribePageToWebhook($targetPageId, $record->access_token);

                        if ($success) {
                            $record->update(['last_synced_at' => now()]);
                            Notification::make()
                                ->title('Webhook Re-subscribed')
                                ->body("Successfully refreshed webhook subscription for '{$record->asset_name}'.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Sync Failed')
                                ->body('Failed to subscribe webhook. You may need to reconnect your Meta account.')
                                ->danger()
                                ->send();
                        }
                    }),

                EditAction::make()
                    ->label('Configure')
                    ->modalHeading(fn (ConnectedAsset $record): string => "Configure: {$record->asset_name}")
                    ->modalWidth(Width::Large),

                DeleteAction::make()
                    ->label('Unlink'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Auth::id(), fn (Builder $query, int $userId) => $query->where('user_id', $userId));
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageConnectedAssets::route('/'),
        ];
    }
}
