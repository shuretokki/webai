<?php

namespace App\Observers;

use App\Models\Attachment;
use Storage;

class AttachmentObserver
{
    /**
     * Handle the Attachment "force deleting" event.
     */
    public function forceDeleting(Attachment $attachment): void
    {
        if ($attachment->path && Storage::disk('public')->exists($attachment->path)) {
            Storage::disk('public')->delete($attachment->path);
        }
    }
}
