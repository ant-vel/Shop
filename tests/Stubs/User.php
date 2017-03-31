<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Stubs;

use Antvel\User\hasAntvel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes, Notifiable, hasAntvel;

	protected $fillable = [
        'facebook', 'mobile_phone', 'work_phone', 'description',
        'pic_url', 'language', 'website', 'twitter',
        'nickname', 'email', 'password', 'role',
        'disabled_at', 'confirmation_token'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $dates = ['deleted_at'];

    protected $guarded = [];
}
