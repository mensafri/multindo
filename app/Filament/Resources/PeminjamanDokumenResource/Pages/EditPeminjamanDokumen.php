<?php

namespace App\Filament\Resources\PeminjamanDokumenResource\Pages;

use App\Filament\Resources\PeminjamanDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeminjamanDokumen extends EditRecord
{
    protected static string $resource = PeminjamanDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
