<?php

namespace App\Filament\Resources\OwnerResource\RelationManagers;

use App\Enums\HouseStatusEnum;
use App\Models\House;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\ActionGroup;

class HousesRelationManager extends RelationManager
{
    protected static string $relationship = 'houses';

    protected static ?string $pluralModelLabel = 'casas';

    protected static ?string $modelLabel = 'casa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->maxLength(255),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Código Postal')
                    ->maxLength(255),
                Forms\Components\TextInput::make('street')
                    ->label('Rua')
                    ->maxLength(255),
                Forms\Components\TextInput::make('district')
                    ->label('Bairro')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Cidade')
                    ->maxLength(255),
                Forms\Components\Select::make('country')
                    ->label('País')
                    ->reactive()
                    ->options(config('countries'))
                    ->required()
                    ->default(array_key_first(config('countries'))),
                Forms\Components\Select::make('state')
                    ->label('Estado')
                    ->options(fn (callable $get) => config("states.{$get('country')}"))
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        HouseStatusEnum::Active->value => 'Ativo',
                        HouseStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Número'),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Código Postal'),
                Tables\Columns\TextColumn::make('street')
                    ->label('Rua'),
                Tables\Columns\TextColumn::make('district')
                    ->label('Bairro'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade'),
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->formatStateUsing(fn ($record, $state) => config("states.{$record->country}.{$state}")),
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->formatStateUsing(fn (string $state): string => config("countries.{$state}")),
                BadgeColumn::make('status')
                    ->enum([
                        HouseStatusEnum::Active->value => 'Ativo',
                        HouseStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->colors([
                        'success' => HouseStatusEnum::Active->value,
                        'danger' => HouseStatusEnum::Inactive->value,
                    ])
                    ->label('Status')
                    ->sortable()
                    ->action(
                        Action::make('updateStatus')
                            ->label('Atualizar Status')
                            ->mountUsing(fn (Forms\ComponentContainer $form, House $record) => $form->fill([
                                'status' => $record->status,
                            ]))
                            ->action(function (House $record, array $data): void {
                                $record->update([
                                    'status' => data_get($data, 'status')
                                ]);
                            })
                            ->form([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        HouseStatusEnum::Active->value => 'Ativo',
                                        HouseStatusEnum::Inactive->value => 'Inativo'
                                    ])
                                    ->required(),
                            ])
                            ->modalWidth('md')
                            ->modalHeading('Atualizar Status')
                            ->modalButton('Salvar')
                            ->icon('heroicon-o-refresh')
                    )
                    ->tooltip('Clique para editar o status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('showHouse')
                        ->label('Ver')
                        ->icon('heroicon-o-eye')
                        ->url(fn (House $record): string => route('filament.resources.houses.edit', ['record' => $record])),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
