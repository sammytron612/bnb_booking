<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewReply extends Model
{
    protected $fillable = [
        'review_id',
        'reply',

    ];

    public $timestamps = true;

    /**
     * Get the review that this reply belongs to.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class,);
    }
}
