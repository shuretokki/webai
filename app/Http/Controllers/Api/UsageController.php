<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    /**
     * Get current month usage statistics for authenticated user.
     *
     * Returns message count, token usage, byte count, and calculated cost
     * for the current billing period (calendar month).
     */
    public function current(Request $request)
    {
        $user = $request->user();
        $stats = $user->currentMonthUsage();

        $limit = match ($user->subscription_tier ?? 'free') {
            'free' => 100,
            'plus' => 1000,
            'enterprise' => PHP_INT_MAX,
            default => 100,
        };

        $percentage = $stats['messages'] > 0
            ? min(($stats['messages'] / $limit) * 100, 100)
            : 0;

        return response()->json([
            'stats' => [
                'messages' => $stats['messages'],
                'tokens' => $stats['tokens'],
                'cost' => number_format($stats['cost'], 2),
                'bytes' => $this->formatBytes($stats['bytes'] ?? 0),
            ],
            'limits' => [
                'messages' => $limit,
            ],
            'percentage' => round($percentage, 1),
            'tier' => $user->subscription_tier ?? 'free',
        ]);
    }

    /**
     * Format bytes into human-readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), 2).' '.$units[$pow];
    }
}
