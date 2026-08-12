<?php

namespace Hotash\Subscription\Filament\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Hotash\Subscription\Filament\Admin\Resources\InvoiceResource\Pages\ManageInvoices;
use Hotash\Subscription\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Invoices';

    protected static ?string $title = 'Invoices';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Overview')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('invoice_number')
                                ->required()
                                ->default(fn (): string => Invoice::generateNextInvoiceNumber())
                                ->maxLength(255)
                                ->columnSpan(1),

                            TextInput::make('amount')
                                ->numeric()
                                ->prefix('BDT')
                                ->required()
                                ->columnSpan(1),

                            Select::make('status')
                                ->options([
                                    'unpaid' => 'Unpaid',
                                    'paid' => 'Paid',
                                ])
                                ->required()
                                ->default('unpaid')
                                ->columnSpan(1),
                        ]),
                    ]),

                Section::make('Dates')
                    ->schema([
                        Grid::make(2)->schema([
                            DateTimePicker::make('due_date')
                                ->label('Due Date')
                                ->nullable()
                                ->columnSpan(1),

                            DateTimePicker::make('paid_at')
                                ->label('Paid At')
                                ->nullable()
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Invoice $record): string => $record->user?->email ?? ''),

                TextColumn::make('package')
                    ->label('Package Plan')
                    ->state(function (Invoice $record): string {
                        $p = $record->subscription?->plan;
                        if (! $p) {
                            return 'Pro Plan';
                        }

                        return ($p['name'] ?? 'Pro Plan').' ('.($p['invoice_period'] ?? '1').' '.($p['invoice_interval'] ?? 'month').')';
                    })
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, Invoice $record) => $record->currency.' '.number_format((float) $state, 2))
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Issue Date')
                    ->dateTime('M d, Y')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime('M d, Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state, Invoice $record): string => match (true) {
                        $record->isPaid() => 'Paid',
                        $record->isOverdue() => 'Overdue',
                        default => 'Unpaid',
                    })
                    ->color(fn (string $state, Invoice $record): string => match (true) {
                        $record->isPaid() => 'success',
                        $record->isOverdue() => 'danger',
                        default => 'warning',
                    })
                    ->icon(fn (string $state, Invoice $record): Heroicon => match (true) {
                        $record->isPaid() => Heroicon::OutlinedCheckCircle,
                        $record->isOverdue() => Heroicon::OutlinedExclamationTriangle,
                        default => Heroicon::OutlinedClock,
                    })
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Paid On')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'overdue' => 'Overdue',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }
                        $val = $data['value'];
                        if ($val === 'paid') {
                            return $query->where('status', 'paid');
                        }
                        if ($val === 'overdue') {
                            return $query->where('status', 'unpaid')->where('due_date', '<', now());
                        }
                        if ($val === 'unpaid') {
                            return $query->where('status', 'unpaid');
                        }

                        return $query;
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('markAsPaid')
                    ->label('Mark Paid')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Invoice $record): bool => ! $record->isPaid())
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        $record->markAsPaid();

                        Notification::make()
                            ->title('Invoice Marked as Paid')
                            ->body('Invoice #'.$record->invoice_number.' status updated to paid.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvoices::route('/'),
        ];
    }
}
