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

class Person extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'persons';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'first_name', 'last_name', 'birthday', 'gender'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id'];

    /**
     * Returns the people user.
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo(Antvel::user());
    }
}
