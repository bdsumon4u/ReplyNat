<?php

namespace Hotash\Subscription\Filament\Admin\Resources;

use App\Models\User;
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
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Hotash\Subscription\Filament\Admin\Resources\UserResource\Pages\ManageUsers;
use Hotash\Subscription\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $title = 'Users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Leave empty to keep current password.'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->email),

                TextColumn::make('plan')
                    ->label('Plan')
                    ->state(function (User $record): string {
                        $sub = $record->subscriptions()->latest('id')->first();
                        if (! $sub) {
                            return '-';
                        }
                        $p = $sub->plan;

                        return ($p['name'] ?? 'Pro').' ('.($p['invoice_period'] ?? '1').' '.($p['invoice_interval'] ?? 'month').')';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';

                        return $query->select('users.*')
                            ->leftJoin('subscriptions', function ($join) {
                                $join->on('users.id', '=', 'subscriptions.user_id')
                                    ->whereNull('subscriptions.canceled_at');
                            })
                            ->orderBy('subscriptions.plan_key', $dir);
                    }),

                TextColumn::make('package_price')
                    ->label('Renewal Price')
                    ->state(function (User $record): string {
                        $sub = $record->subscriptions()->latest('id')->first();
                        if (! $sub) {
                            return '-';
                        }
                        $priceStr = 'BDT '.number_format((float) $sub->price, 2);

                        return $sub->custom_price !== null ? $priceStr.' (Custom)' : $priceStr;
                    })
                    ->badge()
                    ->color(function (User $record): string {
                        $sub = $record->subscriptions()->latest('id')->first();

                        return ($sub && $sub->custom_price !== null) ? 'warning' : 'gray';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';
                        $plans = config('subscription.plans', []);
                        $cases = [];
                        foreach ($plans as $k => $p) {
                            $price = (float) ($p['price'] ?? 0);
                            $cases[] = "WHEN subscriptions.plan_key = '{$k}' THEN {$price}";
                        }
                        $defaultPriceSql = count($cases) > 0 ? 'CASE '.implode(' ', $cases).' ELSE 0 END' : '0';

                        return $query->select('users.*')
                            ->leftJoin('subscriptions', function ($join) {
                                $join->on('users.id', '=', 'subscriptions.user_id')
                                    ->whereNull('subscriptions.canceled_at');
                            })
                            ->orderByRaw("COALESCE(subscriptions.custom_price, {$defaultPriceSql}) {$dir}");
                    }),

                TextColumn::make('starts_at')
                    ->label('Starts At')
                    ->state(function (User $record): ?string {
                        $sub = $record->subscriptions()->latest('id')->first();

                        return $sub?->starts_at ? $sub->starts_at->format('M d, Y') : null;
                    })
                    ->placeholder('-')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';

                        return $query->select('users.*')
                            ->leftJoin('subscriptions', function ($join) {
                                $join->on('users.id', '=', 'subscriptions.user_id')
                                    ->whereNull('subscriptions.canceled_at');
                            })
                            ->orderBy('subscriptions.starts_at', $dir);
                    })
                    ->toggleable(),

                TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->state(function (User $record): ?string {
                        $sub = $record->subscriptions()->latest('id')->first();

                        return $sub?->trial_ends_at ? $sub->trial_ends_at->format('M d, Y h:i A') : null;
                    })
                    ->placeholder('-')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';

                        return $query->select('users.*')
                            ->leftJoin('subscriptions', function ($join) {
                                $join->on('users.id', '=', 'subscriptions.user_id')
                                    ->whereNull('subscriptions.canceled_at');
                            })
                            ->orderBy('subscriptions.trial_ends_at', $dir);
                    })
                    ->toggleable(),

                TextColumn::make('next_billing_date')
                    ->label('Next Billing')
                    ->state(function (User $record): ?string {
                        $sub = $record->subscriptions()->latest('id')->first();
                        $isTrial = $sub?->onTrial();
                        $date = $isTrial ? $sub?->trial_ends_at : $sub?->ends_at;

                        return $date ? $date->format('M d, Y h:i A') : null;
                    })
                    ->placeholder('Continuous')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';

                        return $query->select('users.*')
                            ->leftJoin('subscriptions', function ($join) {
                                $join->on('users.id', '=', 'subscriptions.user_id')
                                    ->whereNull('subscriptions.canceled_at');
                            })
                            ->orderByRaw("COALESCE(CASE WHEN subscriptions.trial_ends_at > NOW() THEN subscriptions.trial_ends_at ELSE subscriptions.ends_at END, '9999-12-31') {$dir}");
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(function (User $record): string {
                        $sub = $record->subscriptions()->latest('id')->first();
                        if (! $sub) {
                            return 'No Sub';
                        }
                        if ($sub->canceled()) {
                            return 'Canceled';
                        }
                        if ($sub->isPendingPayment()) {
                            return 'Pending';
                        }
                        if ($sub->onTrial()) {
                            return 'Trialing';
                        }
                        if ($sub->active()) {
                            return 'Active';
                        }

                        return 'Expired';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Trialing' => 'info',
                        'Pending' => 'warning',
                        'Canceled' => 'danger',
                        'Expired' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'Active' => Heroicon::OutlinedCheckCircle,
                        'Trialing' => Heroicon::OutlinedClock,
                        'Pending' => Heroicon::OutlinedClock,
                        'Canceled' => Heroicon::OutlinedXCircle,
                        'Expired' => Heroicon::OutlinedExclamationTriangle,
                        default => Heroicon::OutlinedMinusCircle,
                    }),

                TextColumn::make('invoices_count')
                    ->label('Invoices')
                    ->counts('invoices')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total_paid')
                    ->label('Lifetime Paid')
                    ->state(function (User $record): string {
                        $total = Invoice::where('user_id', $record->id)->where('status', 'paid')->sum('amount');

                        return 'BDT '.number_format($total, 2);
                    })
                    ->badge()
                    ->color('success')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        /** @var 'asc'|'desc' $dir */
                        $dir = strtolower($direction) === 'desc' ? 'desc' : 'asc';

                        return $query->withSum(['invoices' => fn ($q) => $q->where('status', 'paid')], 'amount')
                            ->orderBy('invoices_sum_amount', $dir);
                    })
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->label('Subscription Status')
                    ->options([
                        'active' => 'Active',
                        'trialing' => 'Trialing',
                        'pending' => 'Pending',
                        'canceled' => 'Canceled',
                        'expired' => 'Expired',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }
                        $status = $data['value'];

                        return $query->whereHas('subscriptions', function ($q) use ($status) {
                            if ($status === 'canceled') {
                                $q->whereNotNull('canceled_at');
                            } elseif ($status === 'pending') {
                                $q->where('status', 'pending');
                            } elseif ($status === 'trialing') {
                                $q->whereNull('canceled_at')->where('trial_ends_at', '>', now());
                            } elseif ($status === 'active') {
                                $q->whereNull('canceled_at')->where('status', 'active');
                            } elseif ($status === 'expired') {
                                $q->whereNull('canceled_at')->where('ends_at', '<=', now());
                            }
                        });
                    }),
                SelectFilter::make('plan_key')
                    ->label('Plan Package')
                    ->options(function (): array {
                        return collect(config('subscription.plans', []))
                            ->mapWithKeys(fn ($p, $k) => [$k => $p['name'].' ('.$p['invoice_period'].' '.$p['invoice_interval'].')'])
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('subscriptions', fn ($q) => $q->where('plan_key', $data['value']));
                    }),

                Filter::make('active_only')
                    ->label('Active Subscriptions Only')
                    ->query(fn (Builder $query) => $query->whereHas('subscriptions', fn ($q) => $q->whereNull('canceled_at'))),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalWidth(Width::Medium),
            ])
            ->recordActions([
                EditAction::make()
                    ->fillForm(function (User $record): array {
                        $sub = $record->subscriptions()->latest('id')->first();

                        return [
                            'name' => $record->name,
                            'email' => $record->email,
                            'plan_key' => $sub?->plan_key,
                            'custom_price' => $sub?->custom_price,
                            'status' => $sub?->status ?? 'active',
                            'starts_at' => $sub?->starts_at,
                            'trial_ends_at' => $sub?->trial_ends_at,
                            'ends_at' => $sub?->ends_at,
                            'canceled_at' => $sub?->canceled_at,
                        ];
                    })
                    ->schema([
                        Section::make('Account')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('password')
                                        ->password()
                                        ->nullable()
                                        ->helperText('Leave empty to keep current password.'),
                                ]),
                            ]),

                        Section::make('Plan & Price')
                            ->schema([
                                Grid::make(3)->schema([
                                    Select::make('plan_key')
                                        ->label('Package')
                                        ->options(function (): array {
                                            return collect(config('subscription.plans', []))
                                                ->mapWithKeys(fn ($p, $k) => [$k => $p['name'].' ('.$p['invoice_period'].' '.$p['invoice_interval'].' - BDT '.number_format($p['price']).')'])
                                                ->toArray();
                                        })
                                        ->nullable()
                                        ->searchable()
                                        ->columnSpan(1),

                                    TextInput::make('custom_price')
                                        ->label('Custom Renewal Price (BDT)')
                                        ->numeric()
                                        ->prefix('BDT')
                                        ->nullable()
                                        ->helperText('Leave empty to use standard plan price.')
                                        ->columnSpan(1),
                                    Select::make('status')
                                        ->options([
                                            'active' => 'Active',
                                            'pending' => 'Pending',
                                            'canceled' => 'Canceled',
                                            'expired' => 'Expired',
                                        ])
                                        ->nullable()
                                        ->searchable()
                                        ->columnSpan(1),
                                ]),
                            ]),

                        Section::make('Dates & Lifecycle')
                            ->schema([
                                Grid::make(4)->schema([
                                    DateTimePicker::make('starts_at')
                                        ->label('Starts At')
                                        ->nullable()
                                        ->columnSpan(1),

                                    DateTimePicker::make('trial_ends_at')
                                        ->label('Free Trial Ends At')
                                        ->nullable()
                                        ->columnSpan(1),

                                    DateTimePicker::make('ends_at')
                                        ->label('Next Billing Date')
                                        ->nullable()
                                        ->columnSpan(1),

                                    DateTimePicker::make('canceled_at')
                                        ->label('Canceled At')
                                        ->nullable()
                                        ->columnSpan(1),
                                ]),
                            ]),
                    ])
                    ->action(function (array $data, User $record): void {
                        $userData = [
                            'name' => $data['name'],
                            'email' => $data['email'],
                        ];
                        if (filled($data['password'] ?? null)) {
                            $userData['password'] = bcrypt($data['password']);
                        }
                        $record->update($userData);

                        if (! empty($data['plan_key'])) {
                            $subData = [
                                'plan_key' => $data['plan_key'],
                                'custom_price' => $data['custom_price'] ?? null,
                                'status' => $data['status'] ?? 'active',
                                'starts_at' => $data['starts_at'] ?? now(),
                                'trial_ends_at' => $data['trial_ends_at'] ?? null,
                                'ends_at' => $data['ends_at'] ?? null,
                                'canceled_at' => $data['canceled_at'] ?? null,
                            ];

                            $sub = $record->subscriptions()->latest('id')->first();
                            if ($sub) {
                                $sub->update($subData);
                            } else {
                                $record->subscriptions()->create($subData);
                            }
                        }

                        Notification::make()
                            ->title('User & Subscription Saved')
                            ->body("Updated account and subscription for {$record->name}.")
                            ->success()
                            ->send();
                    }),

                Action::make('extend30Days')
                    ->label('+30 Days')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $sub = $record->subscriptions()->latest('id')->first();
                        if ($sub) {
                            $baseDate = $sub->ends_at && $sub->ends_at->isFuture() ? $sub->ends_at : now();
                            $sub->update([
                                'ends_at' => (clone $baseDate)->addDays(30),
                                'status' => 'active',
                            ]);

                            Notification::make()
                                ->title('Subscription Extended')
                                ->body("Added 30 days to expiration date for {$record->name}.")
                                ->success()
                                ->send();
                        }
                    }),

                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
