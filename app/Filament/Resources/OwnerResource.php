<?php

namespace App\Filament\Resources;

use App\Enums\ClientStatusEnum;
use App\Filament\Resources\OwnerResource\Pages;
use App\Filament\Resources\OwnerResource\RelationManagers\HousesRelationManager;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $pluralModelLabel = 'proprietarios';

    protected static ?string $modelLabel = 'proprietario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->mask(fn (Mask $mask) => $mask->pattern('(000)00000-00-00'))
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        ClientStatusEnum::Active->value => 'Ativo',
                        ClientStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->hiddenOn('create'),
                Forms\Components\Toggle::make('is_client')
                    ->label('É Cliente?')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone'),
                Tables\Columns\ToggleColumn::make('is_client')
                    ->label('É cliente?'),
                Tables\Columns\TextColumn::make('houses_count')
                    ->label('Número de casas')
                    ->counts('houses'),
                BadgeColumn::make('status')
                    ->enum([
                        ClientStatusEnum::Active->value => 'Ativo',
                        ClientStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->colors([
                        'success' => ClientStatusEnum::Active->value,
                        'danger' => ClientStatusEnum::Inactive->value,
                    ])
                    ->label('Status')
                    ->sortable()
                    ->action(
                        Action::make('updateStatus')
                            ->label('Atualizar Status')
                            ->mountUsing(fn (Forms\ComponentContainer $form, Owner $record) => $form->fill([
                                'status' => $record->status,
                            ]))
                            ->action(function (Owner $record, array $data): void {
                                $record->update([
                                    'status' => data_get($data, 'status')
                                ]);
                            })
                            ->form([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        ClientStatusEnum::Active->value => 'Ativo',
                                        ClientStatusEnum::Inactive->value => 'Inativo'
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            HousesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOwners::route('/'),
            'create' => Pages\CreateOwner::route('/create'),
            'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }    
}
