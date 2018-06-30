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

use Antvel\Support\Images\Uploadable;
use Illuminate\Notifications\Notifiable;
use Antvel\Users\Parsers\PreferencesParser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Antvel\Users\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable,
        Uploadable,
        Concerns\Orders,
        Concerns\AddressBook,
        Concerns\EmailPetitions;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
        //profile information
        'first_name', 'last_name', 'nickname', 'email', 'password', 'role',
        'pictures', 'language', 'time_zone', 'phone_number', 'gender',
        'birthday', 'rate_val', 'rate_count', 'preferences',
        'verified', 'confirmation_token', 'disabled_at',

        //social information
        'facebook', 'twitter', 'website',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'disabled_at', 'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'verified' => 'boolean',
    ];

     /**
     * Return the model storage folder.
     *
     * @return string
     */
    protected function storageFolder()
    {
        return 'images/avatars';
    }

    /**
     * Send the password reset notification mail.
     *
     * @param  string  $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Updates the user's preferences for the given key.
     *
     * @param  string $key
     * @param  mixed $data
     *
     * @return void
     */
    public function updatePreferences(string $key, $data)
    {
        $current = $this->preferences;

        $this->preferences = PreferencesParser::parse($current)->update($key, $data)->toJson();

        $this->save();
    }

    /**
     * Marks the given notification as read.
     *
     * @param  int $notification_id
     *
     * @return void
     */
    public function markNotificationAsRead($notification_id)
    {
        $notification = $this->notifications()
            ->where('id', $notification_id)
            ->whereNull('read_at');

        if ($notification->exists()) {
            $notification->first()->markAsRead();
        }
    }

    /**
     * Set the user's preferences.
     *
     * @param  string|array  $value
     *
     * @return void
     */
    public function setPreferencesAttribute($preferences)
    {
        if (is_array($preferences)) {
            $preferences = json_encode($preferences);
        }

        $this->attributes['preferences'] = $preferences;
    }

    /**
     * Returns the user's preferences.
     *
     * @param  string  $value
     *
     * @return null|string
     */
    public function getPreferencesAttribute($preferences)
    {
        if (is_string($preferences)) {
            return json_decode($preferences, true);
        }

        return $preferences;
    }

    /**
     * Checks whether the user has a phone number.
     *
     * @return bool
     */
    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name . ' ' . $this->last_name);
    }

     /**
     * Checks whether the user has a phone number.
     *
     * @return bool
     */
    public function getHasPhoneAttribute()
    {
        return ! is_null($this->phone_number);
    }

    /**
     * Confirms the user related to the given token & email.
     *
     * @param string $token
     * @param string $email
     *
     * @return self
     */
    public static function confirm(string $token, string $email)
    {
        $user = static::where('confirmation_token', $token)
            ->where('verified', false)
            ->where('email', $email)
            ->firstOrFail();

        $user->verified = true;
        $user->save();

        return $user;
    }

    public function enable()
    {
        $this->update(['disabled_at' => null]);
    }

    public function disable()
    {
        $this->update(['disabled_at' => \Carbon\Carbon::now()]);
    }

    // ======================================= //
    //        temporary while refactoring      //
    // ======================================= //
     public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->attributes['role'], $role);
        }

        return $this->attributes['role'] == $role;
    }

    public function isAdmin()
    {
        return $this->attributes['role'] == 'admin' || $this->attributes['role'] == 'seller';
    }
}
