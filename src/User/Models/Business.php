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

class Business extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'businesses';

    /**
     * Avoiding timestamps.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The table primary key.
     *
     * @var string
     */
    public $primaryKey = 'user_id';

    /**
     * The auto-increment controller.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'business_name', 'local_phone', 'creation_date'];

     /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id'];

    /**
     * Returns the business user.
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the user full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return ucwords($this->business_name);
    }
}
