<?php

namespace Hotash\Subscription\Filament\Pages;

use Filament\Pages\Page;
use Hotash\Subscription\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class ViewInvoice extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = '';

    protected static ?string $slug = 'billing/invoices/{record}';

    protected string $view = 'subscription::filament.invoices.view';

    public Invoice $invoice;

    public function mount(int|string $record): void
    {
        $user = Auth::user();

        $this->invoice = Invoice::where('user_id', $user?->id)
            ->with(['user', 'subscription'])
            ->findOrFail($record);
    }
}
