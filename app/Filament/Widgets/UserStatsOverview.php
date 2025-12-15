<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $freeUsers = User::where('subscription_tier', 'free')->count();
        $plusUsers = User::where('subscription_tier', 'plus')->count();
        $enterpriseUsers = User::where('subscription_tier', 'enterprise')->count();

        $newUsersThisMonth = User::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('Registered accounts')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('New This Month', number_format($newUsersThisMonth))
                ->description('Fresh signups')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('success'),

            Stat::make('Subscription Breakdown', '')
                ->description("Free: $freeUsers | Plus: $plusUsers | Enterprise: $enterpriseUsers")
                ->descriptionIcon('heroicon-o-chart-pie')
                ->color('info'),
        ];
    }
}
