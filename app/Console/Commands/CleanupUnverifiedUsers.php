<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-unverified {--hours=24 : Delete unverified users older than X hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unverified user accounts older than specified hours (default: 24)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');

        $count = User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subHours($hours))
            ->delete();

        $this->info("Deleted {$count} unverified user(s) older than {$hours} hours.");

        return Command::SUCCESS;
    }
}
