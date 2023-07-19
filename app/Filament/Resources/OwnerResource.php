<?php

namespace App\Filament\Resources;

use App\Enums\ClientStatusEnum;
use App\Filament\Resources\OwnerResource\Pages;
use App\Filament\Resources\OwnerResource\RelationManagers\HousesRelationManager;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

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
                    ->mask(fn (Mask $mask) => $mask->pattern('(000)0 0000-00-00'))
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        ClientStatusEnum::Active->value => 'Ativo',
                        ClientStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->hiddenOn('create'),
                Forms\Components\Toggle::make('is_client')
                    ->label('É Cliente?')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-mail'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->icon('heroicon-o-phone')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\ToggleColumn::make('is_client')
                    ->label('É cliente?')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('houses_count')
                    ->label('Número de casas')
                    ->counts('houses')
                    ->icon('heroicon-o-home')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
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
                    ->toggleable()
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
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
                                Forms\Components\Select::make('status')
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
                    ->alignCenter()
                    ->tooltip('Clique para editar o status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        ClientStatusEnum::Active->value => 'Ativo',
                        ClientStatusEnum::Inactive->value => 'Inativo'
                    ]),
                Tables\Filters\TernaryFilter::make('is_client')
                    ->label('É Cliente'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('massStatusChange')
                    ->label('Alterar status')
                    ->action(function (Collection $records, array $data): void {
                        $records->each(fn ($record) => $record->update(['status' => data_get($data, 'status')]));
                    })
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                ClientStatusEnum::Active->value => 'Ativo',
                                ClientStatusEnum::Inactive->value => 'Inativo'
                            ])
                            ->required(),
                    ])
                    ->modalWidth('sm')
                    ->modalHeading('Atualizar Status')
                    ->modalButton('Salvar')
                    ->icon('heroicon-o-refresh'),
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
