<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Models;

use Carbon\Carbon;
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'confirmed' => 'boolean',
    ];

    /**
     * A petition belongs to an user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Refresh a given petition.
     *
     * @return void
     */
    public function refresh()
    {
        $this->expires_at = Carbon::now()->addMonth();
        $this->token = str_random(60);
        $this->save();
    }

    /**
     * Confirm a given petition.
     *
     * @return void
     */
    public function confirmed()
    {
        $this->confirmed = true;
        $this->confirmed_at = Carbon::now();
        $this->save();
    }

    /**
     * Retrieve a petition by the given token and email address.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  string $token
     * @param  string $email
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnconfirmedByTokenAndEmail($query, string $token, string $email)
    {
        return $query->where('expires_at', '>=', Carbon::now())
            ->where('new_email', $email)
            ->where('confirmed', false)
            ->where('token', $token);
    }

    /**
     * Retrieve a petition by the given email address.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  string $email
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnconfirmedByEmail($query, string $email)
    {
        return $query->where('new_email', $email)->where('confirmed', false);
    }
}
