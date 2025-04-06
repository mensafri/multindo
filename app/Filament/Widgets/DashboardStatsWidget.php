<?php

namespace App\Filament\Widgets;

use App\Models\Nasabah;
use App\Models\PeminjamanDokumen;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Nasabah', Nasabah::count())
                ->description('Jumlah semua nasabah')
                ->icon('heroicon-o-user-group'),

            Stat::make('Total Nasabah Aktif', Nasabah::where('status', 'Aktif')->count())
                ->description('Nasabah dengan status Aktif')
                ->icon('heroicon-o-user'),

            Stat::make('Total Nasabah Lunas', Nasabah::where('status', 'Lunas')->count())
                ->description('Nasabah dengan status Lunas')
                ->icon('heroicon-o-user-circle'),

            Stat::make('Total Dokumen Dipinjam', PeminjamanDokumen::where('status', 'Belum Dikembalikan')->count())
                ->description('Dokumen yang sedang dipinjam')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
