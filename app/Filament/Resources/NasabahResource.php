<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NasabahResource\Pages;
use App\Models\Nasabah;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

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
                    ->unique(table: Nasabah::class, ignoreRecord: true)
                    ->required()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state && Nasabah::where('noPin', $state)->exists()) {
                            Notification::make()
                                ->title('Pin sudah ada!')
                                ->body('Silahkan gunakan pin yang berbeda.')
                                ->danger()
                                ->send();

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
            ->headerActions([
                Action::make('download-template')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(asset('template_nasabah.csv'))
                    ->openUrlInNewTab(),

                Action::make('import-data')
                    ->label('Import Data')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('import_file')
                            ->label('File CSV')
                            ->acceptedFileTypes(['text/csv'])
                            ->directory('temp-uploads')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Pastikan Anda telah menginstall league/csv:
                        // composer require league/csv
                        $filePath = $data['import_file'];
                        $absolutePath = Storage::disk('public')->path($filePath);

                        $csv = Reader::createFromPath($absolutePath, 'r');
                        $csv->setDelimiter(';');
                        $csv->setHeaderOffset(0);
                        $records = $csv->getRecords();

                        foreach ($records as $record) {
                            // Asumsikan CSV memiliki header: noPin, nama, branch, status, gudang, rak_aplikasi
                            Nasabah::updateOrCreate(
                                ['noPin' => $record['noPin']],
                                [
                                    'nama'          => $record['nama'],
                                    'branch'        => $record['branch'],
                                    'status'        => $record['status'],
                                    'gudang'        => $record['gudang'] ?? null,
                                    'rak_aplikasi'  => $record['rak_aplikasi'] ?? null,
                                ]
                            );
                        }

                        Notification::make()
                            ->title('Sukses')
                            ->body('Data nasabah berhasil diimport.')
                            ->success()
                            ->send();
                    }),
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
            'index'  => Pages\ListNasabahs::route('/'),
            'create' => Pages\CreateNasabah::route('/create'),
            'edit'   => Pages\EditNasabah::route('/{record}/edit'),
        ];
    }
}
