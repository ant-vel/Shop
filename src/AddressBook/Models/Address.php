<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\AddressBook\Models;

use Antvel\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'line1', 'line2', 'phone',
        'country', 'state', 'user_id',
        'name_contact', 'zipcode', 'city'
    ];

    protected $hidden = ['id'];

    /**
     * Get the user who owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
