<?php

namespace Hotash\Comments\Filament\Resources\ConnectedAssetResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Hotash\Comments\Filament\Resources\ConnectedAssetResource;

class ManageConnectedAssets extends ManageRecords
{
    protected static string $resource = ConnectedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('connectMeta')
                ->label('Connect Facebook & Instagram')
                ->icon(Heroicon::OutlinedPlusCircle)
                ->color('primary')
                ->url(route('comments.auth.redirect')),
        ];
    }
}
