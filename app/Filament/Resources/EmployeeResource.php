<?php

namespace App\Filament\Resources;

use App\Enums\EmployeeStatusEnum;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $pluralModelLabel = 'colaboradores';

    protected static ?string $modelLabel = 'colaborador';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        EmployeeStatusEnum::Active->value => 'Ativo',
                        EmployeeStatusEnum::Inactive->value => 'Inativo'
                    ])
                    ->colors([
                        'success' => EmployeeStatusEnum::Active->value,
                        'danger' => EmployeeStatusEnum::Inactive->value,
                    ])
                    ->label('Status')
                    ->sortable()
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
                            ->label('Atualizar Status')
                            ->mountUsing(fn (Forms\ComponentContainer $form, Employee $record) => $form->fill([
                                'status' => $record->status,
                            ]))
                            ->action(function (Employee $record, array $data): void {
                                $record->update([
                                    'status' => data_get($data, 'status')
                                ]);
                            })
                            ->form([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        EmployeeStatusEnum::Active->value => 'Ativo',
                                        EmployeeStatusEnum::Inactive->value => 'Inativo'
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
                    ->options([
                        EmployeeStatusEnum::Active->value => 'Ativo',
                        EmployeeStatusEnum::Inactive->value => 'Inativo'
                    ])
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }    
}
