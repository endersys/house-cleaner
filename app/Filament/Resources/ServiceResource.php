<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $pluralModelLabel = 'serviços';

    protected static ?string $modelLabel = 'serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'deep' => 'Deep',
                        'simple' => 'Simples'
                    ]),
                TextInput::make('price')
                    ->label('Preço')
                    ->numeric()
                    ->maxLength(255),
                DatePicker::make('service_date')
                    ->label('Data do serviço')
                    ->displayFormat('d/m/Y')
                    ->minDate(now()),
                TimePicker::make('started_at')
                    ->label('Hora do início')
                    ->withoutSeconds()
                    ->default(0),
                TimePicker::make('finished_at')
                    ->label('Hora do término')
                    ->withoutSeconds()
                    ->default(0),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }    
}
