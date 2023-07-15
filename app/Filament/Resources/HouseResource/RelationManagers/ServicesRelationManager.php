<?php

namespace App\Filament\Resources\HouseResource\RelationManagers;

use App\Enums\ServiceStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Models\Service;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $pluralModelLabel = 'serviços';

    protected static ?string $modelLabel = 'serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Forms\Components\Textarea::make('notes')
                    ->label('Anotações')
                    ->maxLength(65535)
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
