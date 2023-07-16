<?php

namespace App\Filament\Resources;

use App\Enums\HouseStatusEnum;
use App\Filament\Resources\PeriodicResource\Pages;
use App\Models\Periodic;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Forms;

class PeriodicResource extends Resource
{
    protected static ?string $model = Periodic::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Serviços';

    protected static ?string $navigationLabel = 'Periódicos';

    protected static ?string $modelLabel = 'periódico';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('house.owner.name')
                    ->label('Proprietário')
                    ->icon('heroicon-o-user'),
                Tables\Columns\IconColumn::make('house')
                    ->label('Casa')
                    ->options([
                        'heroicon-o-home',
                    ])
                    ->colors([
                        'warning',
                    ])
                    ->action(
                        Action::make('showHouse')
                            ->mountUsing(fn (Forms\ComponentContainer $form, Periodic $record) => $form->fill([
                                'number' => $record->house->number,
                                'postal_code' => $record->house->postal_code,
                                'street' => $record->house->street,
                                'district' => $record->house->district,
                                'city' => $record->house->city,
                                'state' => $record->house->state,
                                'country' => $record->house->country,
                                'status' => $record->house->status,
                            ]))
                            ->action(fn (Periodic $record) => to_route('filament.resources.houses.edit', ['record' => $record->house]))
                            ->form([
                                Forms\Components\TextInput::make('postal_code')
                                    ->label('Código Postal')
                                    ->disabled(),
                                Forms\Components\TextInput::make('number')
                                    ->label('Número')
                                    ->disabled(),
                                Forms\Components\TextInput::make('street')
                                    ->label('Rua')
                                    ->disabled(),
                                Forms\Components\TextInput::make('district')
                                    ->label('Bairro')
                                    ->disabled(),
                                Forms\Components\TextInput::make('city')
                                    ->label('Cidade')
                                    ->disabled(),
                                Forms\Components\Select::make('country')
                                    ->label('País')
                                    ->options(config('countries'))
                                    ->default(array_key_first(config('countries')))
                                    ->disabled(),
                                Forms\Components\Select::make('state')
                                    ->label('Estado')
                                    ->options(fn (callable $get) => config("states.{$get('country')}"))
                                    ->disabled(),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        HouseStatusEnum::Active->value => 'Ativo',
                                        HouseStatusEnum::Inactive->value => 'Inativo'
                                    ])
                                    ->disabled(),
                            ])
                            ->modalWidth('xl')
                            ->modalButton('Ir para a casa')
                            ->modalHeading(fn (Periodic $record) => "Casa de " . $record->house->owner->name)
                    )
                    ->alignCenter()
                    ->tooltip('Ver casa'),
                Tables\Columns\TextColumn::make('periodicity')
                    ->label('Periodicidade')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'daily' => 'Diário',
                            'bimonthly' => 'Quinzenal',
                            'monthly' => 'Mensal'
                        };
                    }),
                Tables\Columns\IconColumn::make('can_alert')
                    ->label('Receber alerta?')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('Próximo Serviço')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListPeriodics::route('/'),
            'create' => Pages\CreatePeriodic::route('/create'),
            // 'edit' => Pages\EditPeriodic::route('/{record}/edit'),
        ];
    }
}
