<?php

namespace App\Filament\Resources;

use App\Enums\MaterialStatusEnum;
use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $pluralModelLabel = 'materiais';

    protected static ?string $modelLabel = 'material';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label('Preço'),
                Forms\Components\TextInput::make('reference')
                    ->label('Referência')
                    ->maxLength(255),
                Forms\Components\Select::make('measurement_unit')
                    ->label('Unidade de medida')
                    ->options(config('units')),
                Forms\Components\DatePicker::make('expiration_date')
                    ->label('Data de vencimento')
                    ->displayFormat('d/m/Y')
                    ->minDate(now()),
                Forms\Components\TextInput::make('shelf')
                    ->label('Prateleira')
                    ->maxLength(255),
                Forms\Components\Select::make('categories')
                    ->label('Categorias')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                    ])
                    ->createOptionModalHeading('Nova Categoria'),
                Forms\Components\Select::make('suppliers')
                    ->label('Fornecedores')
                    ->multiple()
                    ->relationship('suppliers', 'name')
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                    ])
                    ->createOptionModalHeading('Novo Fornecedor'),
                Forms\Components\TextInput::make('stock')
                    ->label('Quantidade')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        MaterialStatusEnum::Active->value => 'Ativo',
                        MaterialStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->hiddenOn('create'),
                Forms\Components\Textarea::make('notes')
                    ->label('Anotações')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    // ->prefix(fn () => config('app.locale') === 'pt_BR' ? 'R$' : '$')
                    ->label('Preço')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-cash'),
                Tables\Columns\TextColumn::make('stock.quantity')
                    ->formatStateUsing(function (string $state, $record) {
                        $quantity = $state . ' ' . config('units.' . $record->measurement_unit);
               
                        return $quantity .= $state > 1 ? 'S' : '';
                    })
                    ->label('Estoque')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Data de vencimento')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('shelf')
                    ->label('Prateleira')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-table'),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        MaterialStatusEnum::Active->value => 'Ativo',
                        MaterialStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->colors([
                        'success' => MaterialStatusEnum::Active->value,
                        'danger' => MaterialStatusEnum::Inactive->value,
                    ])
                    ->label('Status')
                    ->sortable()
                    ->toggleable()
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
                            ->label('Atualizar Status')
                            ->mountUsing(fn (Forms\ComponentContainer $form, Material $record) => $form->fill([
                                'status' => $record->status,
                            ]))
                            ->action(function (Material $record, array $data): void {
                                $record->update([
                                    'status' => data_get($data, 'status')
                                ]);
                            })
                            ->form([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        MaterialStatusEnum::Active->value => 'Ativo',
                                        MaterialStatusEnum::Inactive->value => 'Inativo'
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        MaterialStatusEnum::Active->value => 'Ativo',
                        MaterialStatusEnum::Inactive->value => 'Inativo'
                    ])
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }    
}