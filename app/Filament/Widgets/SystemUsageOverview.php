<?php

namespace App\Filament\Widgets;

use App\Models\UserUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemUsageOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalMessages = UserUsage::where('type', 'message_sent')->sum('messages');
        $totalTokens = UserUsage::where('type', 'ai_response')->sum('tokens');
        $totalStorage = UserUsage::where('type', 'file_upload')->sum('bytes');

        $messagesThisMonth = UserUsage::where('type', 'message_sent')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('messages');

        return [
            Stat::make('Total Messages', number_format($totalMessages))
                ->description($messagesThisMonth.' this month')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('success'),

            Stat::make('Total Tokens', number_format($totalTokens))
                ->description('AI processing usage')
                ->descriptionIcon('heroicon-o-cpu-chip')
                ->color('warning'),

            Stat::make('Storage Used', $this->formatBytes($totalStorage))
                ->description('File attachments')
                ->descriptionIcon('heroicon-o-cloud-arrow-up')
                ->color('info'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
