<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Models;

use Antvel\Antvel;
use Illuminate\Database\Eloquent\Model;

class EmailChangePetition extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['confirmed_at', 'expires_at', 'created_at', 'updated_at'];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['user_id', 'old_email', 'new_email', 'expires_at', 'token'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_change_petitions';

    /**
     * Returns user for a given profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
