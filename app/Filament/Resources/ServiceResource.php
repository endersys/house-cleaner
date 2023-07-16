<?php

namespace App\Filament\Resources;

use App\Enums\EmployeeStatusEnum;
use App\Enums\HouseStatusEnum;
use App\Enums\ServiceStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Material;
use App\Models\Service;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Serviços';

    protected static ?string $navigationLabel = 'Gerais';

    protected static ?string $pluralModelLabel = 'serviços';

    protected static ?string $modelLabel = 'serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('house_id')
                    ->label('Casa')
                    ->relationship('house', 'number')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "$record->number - $record->street - $record->district - $record->city"),
                Forms\Components\DatePicker::make('service_date')
                    ->label('Data do serviço')
                    ->required()
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        ServiceStatusEnum::Pending->value => 'Pendente',
                        ServiceStatusEnum::InProgress->value => 'Em andamento',
                        ServiceStatusEnum::Done->value => 'Concluído',
                        ServiceStatusEnum::DoneWithPendency->value => 'Concluído com pendência',
                        ServiceStatusEnum::Rescheduled->value => 'Reagendado',
                        ServiceStatusEnum::Canceled->value => 'Cancelado',
                        ServiceStatusEnum::Expired->value => 'Expirado',
                    ])
                    ->default(ServiceStatusEnum::Pending),
                Forms\Components\TextInput::make('price')
                    ->label('Preço'),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->required()
                    ->options([
                        ServiceTypeEnum::Simple->value => 'Simples',
                        ServiceTypeEnum::Deep->value => 'Deep',
                    ]),
                Forms\Components\TimePicker::make('started_at')
                    ->label('Hora do início')
                    ->withoutSeconds()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                        if ($get('finished_at') < $state) {
                            return $set('finished_at', $state);
                        }
                    })
                    ->default(0),
                Forms\Components\TimePicker::make('finished_at')
                    ->label('Hora do término')
                    ->withoutSeconds()
                    ->default(0),
                Forms\Components\Select::make('employees')
                    ->label('Colaboradores')
                    ->multiple()
                    ->relationship('employees', 'name')
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask->pattern('(000)0 0000-00-00'))
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                EmployeeStatusEnum::Active->value => 'Ativo',
                                EmployeeStatusEnum::Inactive->value => 'Inativo'
                            ])
                            ->required()
                            ->default(EmployeeStatusEnum::Active->value)
                    ])
                    ->createOptionModalHeading('Novo Colaborador'),
                Forms\Components\Textarea::make('notes')
                    ->label('Anotações')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Section::make('Materiais')
                    ->description('Materiais usados no serviço')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Repeater::make('materials')
                            ->label('')
                            ->relationship('materials')
                            ->schema([
                                Forms\Components\Select::make('material_id')
                                    ->label('Material')
                                    ->options(Material::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantidade')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                            ])
                            ->createItemButtonLabel('Adicionar Material')
                    ])
            ]);
    }

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
                            ->mountUsing(fn (Forms\ComponentContainer $form, Service $record) => $form->fill([
                                'number' => $record->house->number,
                                'postal_code' => $record->house->postal_code,
                                'street' => $record->house->street,
                                'district' => $record->house->district,
                                'city' => $record->house->city,
                                'state' => $record->house->state,
                                'country' => $record->house->country,
                                'status' => $record->house->status,
                            ]))
                            ->action(fn (Service $record) => to_route('filament.resources.houses.edit', ['record' => $record->house]))
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
                            ->modalHeading(fn (Service $record) => "Casa de " . $record->house->owner->name)
                    )
                    ->alignCenter()
                    ->tooltip('Ver casa'),
                Tables\Columns\TextColumn::make('service_date')
                    ->label('Data do serviço')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->icon('heroicon-o-cash'),
                Tables\Columns\BadgeColumn::make('type')
                    ->enum([
                        ServiceTypeEnum::Simple->value => 'Simples',
                        ServiceTypeEnum::Deep->value => 'Deep',
                    ])
                    ->colors([
                        'primary' => ServiceTypeEnum::Simple->value,
                        'secondary' => ServiceTypeEnum::Deep->value,
                    ])
                    ->label('Tipo')
                    ->sortable()
                    ->action(
                        Action::make('updateType')
                            ->label('Atualizar Tipo de Serviço')
                            ->mountUsing(fn (Forms\ComponentContainer $form, Service $record) => $form->fill([
                                'type' => $record->type,
                            ]))
                            ->action(function (Service $record, array $data): void {
                                $record->update([
                                    'type' => data_get($data, 'type')
                                ]);
                            })
                            ->form([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo')
                                    ->options([
                                        ServiceTypeEnum::Simple->value => 'Simples',
                                        ServiceTypeEnum::Deep->value => 'Deep',
                                    ])
                                    ->required(),
                            ])
                            ->modalWidth('md')
                            ->modalHeading('Atualizar Tipo de Serviço')
                            ->modalButton('Salvar')
                            ->icon('heroicon-o-refresh')
                    )
                    ->alignCenter()
                    ->tooltip('Clique para editar o tipo de serviço'),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Início')
                    ->formatStateUsing(fn ($state) => substr($state, 0, -3))
                    ->icon('heroicon-o-clock'),
                Tables\Columns\TextColumn::make('finished_at')
                    ->label('Fim')
                    ->formatStateUsing(fn ($state) => substr($state, 0, -3))
                    ->icon('heroicon-o-clock'),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        ServiceStatusEnum::Pending->value => 'Pendente',
                        ServiceStatusEnum::InProgress->value => 'Em andamento',
                        ServiceStatusEnum::Done->value => 'Concluído',
                        ServiceStatusEnum::DoneWithPendency->value => 'Concluído com pendência',
                        ServiceStatusEnum::Rescheduled->value => 'Reagendado',
                        ServiceStatusEnum::Canceled->value => 'Cancelado',
                        ServiceStatusEnum::Expired->value => 'Expirado',
                    ])
                    ->colors([
                        'warning' => ServiceStatusEnum::Pending->value,
                        'primary' => ServiceStatusEnum::InProgress->value,
                        'success' =>ServiceStatusEnum::Done->value,
                        'custom-bg-warning' => ServiceStatusEnum::DoneWithPendency->value,
                        'secondary' => ServiceStatusEnum::Rescheduled->value,
                        'danger' => ServiceStatusEnum::Canceled->value,
                        'custom-bg-violet' => ServiceStatusEnum::Expired->value,
                    ])
                    ->label('Status')
                    ->sortable()
                    ->action(
                        Action::make('updateStatus')
                            ->label('Atualizar Status')
                            ->mountUsing(fn (Forms\ComponentContainer $form, Service $record) => $form->fill([
                                'status' => $record->status,
                            ]))
                            ->action(function (Service $record, array $data): void {
                                $record->update([
                                    'status' => data_get($data, 'status')
                                ]);
                            })
                            ->form([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        ServiceStatusEnum::Pending->value => 'Pendente',
                                        ServiceStatusEnum::InProgress->value => 'Em andamento',
                                        ServiceStatusEnum::Done->value => 'Concluído',
                                        ServiceStatusEnum::DoneWithPendency->value => 'Concluído com pendência',
                                        ServiceStatusEnum::Rescheduled->value => 'Reagendado',
                                        ServiceStatusEnum::Canceled->value => 'Cancelado',
                                        ServiceStatusEnum::Expired->value => 'Expirado',
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
