<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanDokumenResource\Pages;
use App\Models\PeminjamanDokumen;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PeminjamanDokumenResource extends Resource
{
    protected static ?string $model = PeminjamanDokumen::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Peminjaman Dokumen';
    protected static ?string $navigationGroup = 'Manajemen Peminjaman';
    protected static ?string $slug = 'peminjaman-dokumen';
    public static function getPluralLabel(): string
    {
        return 'Peminjaman Dokumen';
    }

    public static function getModelLabel(): string
    {
        return 'Peminjaman Dokumen';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Select::make('nasabah_id')
                    ->relationship('nasabah', 'nama')
                    ->searchable()
                    ->label('Peminjam (Nasabah)')
                    ->required(),

                TextInput::make('nama_dokumen')
                    ->label('Nama Dokumen')
                    ->required(),

                DatePicker::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam'),

                DatePicker::make('tanggal_selesai_pinjam')
                    ->label('Tanggal Selesai Pinjam'),

                // Perbarui daftar opsi status agar mencakup semua state
                Select::make('status')
                    ->options([
                        'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                        'Disetujui'           => 'Disetujui',
                        'Dibatalkan'          => 'Dibatalkan',
                        'Dikembalikan'        => 'Dikembalikan',
                    ])
                    ->default('Menunggu Verifikasi') // default ketika create
                    ->required()
                    ->label('Status'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nasabah.noPin')
                    ->label('No. Pin Nasabah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_dokumen')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date(),

                Tables\Columns\TextColumn::make('tanggal_selesai_pinjam')
                    ->label('Tgl Selesai Pinjam')
                    ->date(),

                // Gunakan TextColumn + badge()
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        // Mapping status
                        return match ($state) {
                            'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                            'Disetujui'           => 'Disetujui',
                            'Dibatalkan'          => 'Dibatalkan',
                            'Dikembalikan'        => 'Dikembalikan',
                            default               => $state,
                        };
                    })
                    ->colors([
                        'warning'  => 'Menunggu Verifikasi',
                        'success'  => 'Disetujui',
                        'danger'   => 'Dibatalkan',
                        'secondary' => 'Dikembalikan',
                    ])
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                        'Disetujui'           => 'Disetujui',
                        'Dibatalkan'          => 'Dibatalkan',
                        'Dikembalikan'        => 'Dikembalikan',
                    ]),
            ])
            ->actions([
                // Aksi Edit & Delete bawaan
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                // Aksi Kustom Approve
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    // Hanya muncul bila status = 'Menunggu Verifikasi'
                    ->visible(fn($record) => $record->status === 'Menunggu Verifikasi')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'Disetujui';
                        $record->save();
                    }),

                // Aksi Kustom Cancel
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->button()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    // Muncul jika status masih 'Menunggu Verifikasi' atau 'Disetujui'
                    ->visible(fn($record) => in_array($record->status, ['Menunggu Verifikasi', 'Disetujui']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'Dibatalkan';
                        $record->save();
                    }),

                // Aksi Kustom Dikembalikan
                Tables\Actions\Action::make('returned')
                    ->label('Dikembalikan')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    // Muncul jika status telah 'Disetujui'
                    ->visible(fn($record) => $record->status === 'Disetujui')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'Dikembalikan';
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeminjamanDokumens::route('/'),
            'create' => Pages\CreatePeminjamanDokumen::route('/create'),
            'edit'   => Pages\EditPeminjamanDokumen::route('/{record}/edit'),
        ];
    }
}
