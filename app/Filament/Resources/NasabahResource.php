<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NasabahResource\Pages;
use App\Models\Nasabah;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;

class NasabahResource extends Resource
{
    protected static ?string $model = Nasabah::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Nasabah';

    protected static ?string $navigationGroup = 'Manajemen Nasabah';

    protected static ?string $slug = 'nasabah';
    public static function getPluralLabel(): string
    {
        return 'Nasabah';
    }

    public static function getModelLabel(): string
    {
        return 'Nasabah';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('noPin')
                    ->label('No. Pin Nasabah')
                    // Rule unique masih bisa dipakai sebagai back-up validasi,
                    // tapi validasi realtime ini yang memberikan notifikasi.
                    ->unique(table: Nasabah::class, ignoreRecord: true)
                    ->required()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state && Nasabah::where('noPin', $state)->exists()) {
                            Notification::make()
                                ->title('Pin sudah ada!')
                                ->body('Silahkan gunakan pin yang berbeda.')
                                ->danger()
                                ->send();

                            // Opsional: Mengosongkan field agar user harus memasukkan nilai baru.
                            $set('noPin', '');
                        }
                    }),

                TextInput::make('nama')
                    ->label('Nama Nasabah')
                    ->required(),

                TextInput::make('branch')
                    ->label('Branch')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Lunas' => 'Lunas',
                    ])
                    ->default('Aktif')
                    ->required(),

                TextInput::make('gudang')
                    ->label('Gudang'),

                TextInput::make('rak_aplikasi')
                    ->label('Rak Aplikasi'),

                FileUpload::make('file')
                    ->label('Dokumen')
                    ->directory('nasabah-files')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(10240),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('noPin')
                    ->label('No. Pin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Nasabah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gudang')
                    ->label('Gudang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rak_aplikasi')
                    ->label('Rak Aplikasi')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk dokumen
                Tables\Columns\TextColumn::make('file')
                    ->label('Dokumen')
                    ->formatStateUsing(function ($state, $record) {
                        return $state
                            ? '<a href="' . Storage::url($record->file) . '" target="_blank" class="inline-flex items-center px-2 py-1 text-xs font-medium leading-4 text-white bg-blue-600 rounded-md hover:bg-blue-500">Lihat Dokumen</a>'
                            : '-';
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Nasabah')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Lunas' => 'Lunas',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNasabahs::route('/'),
            'create' => Pages\CreateNasabah::route('/create'),
            'edit' => Pages\EditNasabah::route('/{record}/edit'),
        ];
    }
}
