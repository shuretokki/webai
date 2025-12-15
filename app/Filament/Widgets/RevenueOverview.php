<?php

namespace App\Filament\Widgets;

use App\Models\UserUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = UserUsage::sum('cost');

        $currentMonthRevenue = UserUsage::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('cost');

        $lastMonthRevenue = UserUsage::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('cost');

        $revenueChange = $lastMonthRevenue > 0
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        return [
            Stat::make('Total Revenue', '$'.number_format($totalRevenue, 2))
                ->description('All time')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('This Month', '$'.number_format($currentMonthRevenue, 2))
                ->description($revenueChange > 0 ? '+'.number_format($revenueChange, 1).'% from last month' : number_format($revenueChange, 1).'% from last month')
                ->descriptionIcon($revenueChange > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($revenueChange > 0 ? 'success' : 'danger'),

            Stat::make('Total Requests', number_format(UserUsage::count()))
                ->description('API calls tracked')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}
