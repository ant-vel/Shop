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

use Antvel\AddressBook\Models\Address;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Antvel\User\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
        'facebook', 'mobile_phone', 'work_phone', 'description',
        'pic_url', 'language', 'website', 'twitter',
        'nickname', 'email', 'password', 'role',
        'disabled_at', 'confirmation_token'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

     /**
     * Returns the user profile.
     *
     * @return mixed
     */
	public function profile()
    {
        if (in_array($this->role, ['business', 'nonprofit'])) {
            return $this->hasOne(Business::class);
        }

        return $this->hasOne(Person::class);
    }

    /**
     * Returns the user addressBook.
     *
     * @return Address
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * The user email change petitions.
     *
     * @return EmailChangePetition
     */
    public function emailChangePetitions()
    {
        return $this->hasMany(EmailChangePetition::class);
    }

    /**
     * Send the password reset notification mail.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }




    // ======================================= //
    //        temporary while refactoring      //
    // ======================================= //

    public function getCartCount()
    {
        return 0;
    }

     public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->attributes['role'], $role);
        }

        return $this->attributes['role'] == $role;
    }

    public function isAdmin()
    {
        return $this->attributes['role'] == 'admin';
    }

    public function isPerson()
    {
        return $this->attributes['role'] == 'person';
    }

    public function isCompany()
    {
        return $this->attributes['role'] == 'business';
    }

   public function isTrusted()
   {
       return $this->attributes['type'] == 'trusted';
   }
}
