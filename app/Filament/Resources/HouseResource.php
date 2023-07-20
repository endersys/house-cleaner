<?php

namespace App\Filament\Resources;

use App\Enums\HouseStatusEnum;
use App\Filament\Resources\HouseResource\Pages;
use App\Filament\Resources\HouseResource\RelationManagers\ServicesRelationManager;
use App\Models\House;
use App\Models\Owner;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;

class HouseResource extends Resource
{
    protected static ?string $model = House::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $pluralModelLabel = 'casas';

    protected static ?string $modelLabel = 'casa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_id')
                    ->reactive()
                    ->label('Proprietário')
                    ->relationship('owner', 'name')
                    ->options(Owner::whereStatus('active')->pluck('name', 'id'))
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_client')
                            ->label('É Cliente?')
                            ->required(),
                    ])
                    ->createOptionModalHeading('Novo Proprietário')
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Código Postal')
                    ->maxLength(255),
                Forms\Components\TextInput::make('number')
                    ->label('Número')
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
                    ->default(array_key_first(config('countries')))
                    ->required(),
                Forms\Components\Select::make('state')
                    ->label('Estado')
                    ->options(fn (callable $get) => config("states.{$get('country')}"))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        HouseStatusEnum::Active->value => 'Ativo',
                        HouseStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->required()
                    ->hiddenOn('create'),
                Forms\Components\Textarea::make('notes')
                    ->label('Anotações')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Fieldset::make('Periódicos')
                    ->relationship('periodicity', 'house_id')
                    ->schema([
                        Forms\Components\Grid::make('label')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content(
                                        new HtmlString(
                                            "<small 
                                                class='custom-color-danger'
                                            >
                                                *A data do próximo serviço será gerada automaticamente com base na data de finalização do último serviço.
                                            </small>"
                                        )
                                    ),
                            ]),
                        Forms\Components\Select::make('periodicity')
                            ->label('Periodicidade')
                            ->options([
                                'daily' => 'Diário',
                                'bimonthly' => 'Quinzenal',
                                'monthly' => 'Mensal'
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('next_service_date')
                            ->label('Data do Próximo Serviço')
                            ->displayFormat('d/m/Y')
                            ->minDate(now()),
                        Forms\Components\Toggle::make('can_alert')
                            ->label('Gerar alerta do próximo serviço?')
                            ->required(),
                    ])
                    ->columns(2)
                    ->hidden(function (callable $get) {
                        if ($get('owner_id')) {
                            $owner = Owner::findOrFail($get('owner_id'));

                            return $owner->is_client === 0;
                        }

                        return true;
                    })
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Proprietário')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->sortable('number')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('street')
                    ->label('Rua')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label('CEP')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('district')
                    ->label('Bairro')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($record, string $state) => config("states.{$record->country}.{$state}")),
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => config("countries.{$state}")),
                Tables\Columns\BadgeColumn::make('status')
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
                    ->toggleable()
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
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
                                Forms\Components\Select::make('status')
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        HouseStatusEnum::Active->value => 'Ativo',
                        HouseStatusEnum::Inactive->value => 'Inativo'
                    ])
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('changeOwner')
                        ->label('Mudar Proprietário')
                        ->mountUsing(fn (Forms\ComponentContainer $form, House $record) => $form->fill([
                            'owner_id' => $record->owner_id,
                        ]))
                        ->action(function (House $record, array $data): void {
                            $record->update([
                                'owner_id' => data_get($data, 'owner')
                            ]);
                        })
                        ->form([
                            Forms\Components\Select::make('owner')
                                ->label('Proprietário Atual')
                                ->relationship('owner', 'name')
                                ->options(Owner::whereStatus('active')->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->modalWidth('md')
                        ->modalHeading('Mudar Proprietário')
                        ->modalButton('Salvar')
                        ->icon('heroicon-o-refresh')
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            ServicesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHouses::route('/'),
            'create' => Pages\CreateHouse::route('/create'),
            'edit' => Pages\EditHouse::route('/{record}/edit'),
        ];
    }    
}
