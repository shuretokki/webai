<?php

namespace App\Filament\Resources\UserUsageResource\Pages;

use App\Filament\Resources\UserUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserUsage extends EditRecord
{
    protected static string $resource = UserUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
