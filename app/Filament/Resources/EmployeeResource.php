<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\City;
use Filament\Tables;
use App\Models\Region;
use App\Models\Country;
use App\Models\Employee;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeStatsOverview;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->options(Country::all()->pluck('name', 'id')->toArray())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('region_id', null)),
                        Select::make('region_id')
                            ->label('Region')
                            ->options(function (callable $get) {
                                $country = Country::find($get('country_id'));
                                if (!$country) {
                                    return Region::all()->pluck('name', 'id');
                                }
                                return $country->regions->pluck('name', 'id');
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                        Select::make('city_id')
                            ->label('City')
                            ->options(function (callable $get) {
                                $region = Region::find($get('region_id'));
                                if (!$region) {
                                    return City::all()->pluck('name', 'id');
                                }
                                return $region->cities->pluck('name', 'id');
                            })
                            ->required()
                            ->reactive(),
                        Select::make('department_id',)
                            ->relationship('department', 'name')->required(),
                        TextInput::make('first_name')->required()->maxLength(255),
                        TextInput::make('last_name')->required()->maxLength(255),
                        TextInput::make('address')->required()->maxLength(255),
                        TextInput::make('zip_code')->required()->maxLength(5),
                        DatePicker::make('birthdate')->required(),
                        DatePicker::make('date_hired')->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('department.name')->sortable()->searchable(),
                TextColumn::make('date_hired')->sortable()->date(),
                TextColumn::make('created_at')->sortable()->dateTime(),
            ])
            ->filters([
                SelectFilter::make('department')->relationship('department', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getWidgets(): array
    {
        return [
            EmployeeStatsOverview::class,
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
