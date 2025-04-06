<?php

namespace App\Filament\Resources\PeminjamanDokumenResource\Pages;

use App\Filament\Resources\PeminjamanDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeminjamanDokumens extends ListRecords
{
    protected static string $resource = PeminjamanDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
