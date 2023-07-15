<?php

namespace App\Filament\Resources;

use App\Enums\MaterialStatusEnum;
use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $pluralModelLabel = 'materiais';

    protected static ?string $modelLabel = 'material';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->label('Preço'),
                TextInput::make('reference')
                    ->label('Referência')
                    ->maxLength(255),
                Select::make('measurement_unit')
                    ->label('Unidade de medida')
                    ->options(config('units')),
                DatePicker::make('expiration_date')
                    ->label('Data de vencimento')
                    ->displayFormat('d/m/Y')
                    ->minDate(now()),
                TextInput::make('shelf')
                    ->label('Prateleira')
                    ->maxLength(255),
                Select::make('categories')
                    ->label('Categorias')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                    ])
                    ->createOptionModalHeading('Nova Categoria'),
                Select::make('suppliers')
                    ->label('Fornecedores')
                    ->multiple()
                    ->relationship('suppliers', 'name')
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                    ])
                    ->createOptionModalHeading('Novo Fornecedor'),
                TextInput::make('stock')
                    ->label('Quantidade')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        MaterialStatusEnum::Active->value => 'Ativo',
                        MaterialStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->hiddenOn('create'),
                Textarea::make('notes')
                    ->label('Anotações')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    // ->prefix(fn () => config('app.locale') === 'pt_BR' ? 'R$' : '$')
                    ->label('Preço')
                    ->sortable()
                    ->icon('heroicon-o-cash'),
                TextColumn::make('stock.quantity')
                    ->formatStateUsing(function (string $state, $record) {
                        $quantity = $state . ' ' . config('units.' . $record->measurement_unit);
               
                        return $quantity .= $state > 1 ? 'S' : '';
                    })
                    ->label('Quantidade')
                    ->sortable(),
                TextColumn::make('reference')
                    ->label('Referencia')
                    ->sortable(),
                TextColumn::make('expiration_date')
                    ->label('Data de vencimento')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                TextColumn::make('shelf')
                    ->label('Prateleira')
                    ->sortable()
                    ->icon('heroicon-o-table'),
                BadgeColumn::make('status')
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
                    ->action(
                        Action::make('updateStatus')
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
                                Select::make('status')
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
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            
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