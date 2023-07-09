<?php

namespace App\Filament\Resources;

use App\Enums\MaterialStatusEnum;
use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
                    ->preload(),
                Select::make('suppliers')
                    ->label('Fornecedores')
                    ->multiple()
                    ->relationship('suppliers', 'name')
                    ->preload(),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->prefix(fn () => config('app.locale') === 'pt_BR' ? 'R$' : '$')
                    ->label('Preço')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock.quantity')
                    ->formatStateUsing(function (string $state, $record) {
                        $quantity = $state . ' ' . config('units.' . $record->measurement_unit);
               
                        return $quantity .= $state > 1 ? 'S' : '';
                    })
                    ->label('Quantidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Referencia')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Data de vencimento')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shelf')
                    ->label('Prateleira')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->enum([
                        MaterialStatusEnum::Active->value => 'Ativo',
                        MaterialStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->label('Status')
                    ->sortable(),
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