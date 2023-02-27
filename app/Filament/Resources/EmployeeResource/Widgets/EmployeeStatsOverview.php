<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Country;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $ph = Country::where('country_code', 'PH')->withCount('employees')->first();
        $us = Country::where('country_code', 'US')->withCount('employees')->first();
        return [
            Card::make('All Employees', Employee::all()->count()),
            Card::make($ph->name . ' Employees', $ph->employees_count),
            Card::make($us->name . ' Employees', $us->employees_count),
        ];
    }
}
