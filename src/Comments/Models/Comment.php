<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Comments\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	/**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['data', 'read_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * The model boot function.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            $comment->{$comment->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

	/**
     * Get all of the owning commentable models.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Mark the comment as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->fill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

   /**
     * Determine if a comment has been read.
     *
     * @return bool
     */
    public function read()
    {
        return ! is_null($this->read_at);
    }

    /**
     * Determine if a comment has not been read.
     *
     * @return bool
     */
    public function unread()
    {
        return is_null($this->read_at);
    }
}
