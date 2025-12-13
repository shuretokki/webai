<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Summary of message
     *
     * @return BelongsTo<Message, Attachment>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
