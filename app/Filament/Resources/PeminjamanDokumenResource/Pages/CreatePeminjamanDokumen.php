<?php

namespace App\Filament\Resources\PeminjamanDokumenResource\Pages;

use App\Filament\Resources\PeminjamanDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePeminjamanDokumen extends CreateRecord
{
    protected static string $resource = PeminjamanDokumenResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
