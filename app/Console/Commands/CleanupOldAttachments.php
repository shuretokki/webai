<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;

class CleanupOldAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachments:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted attachments older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Attachment::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->each(function ($attachment) {
                $attachment->forceDelete();
            });

        $this->info('Succesfully deleted old attachments');
    }
}
