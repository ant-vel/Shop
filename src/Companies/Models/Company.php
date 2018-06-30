<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Companies\Models;

use Antvel\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //Profile information
        'name', 'description', 'email', 'logo', 'slogan', 'theme', 'status',

        //Contact information
        'contact_email', 'sales_email', 'support_email', 'phone_number', 'cell_phone',
        'address', 'state', 'city', 'zip_code',

        //Social information
        'website', 'twitter', 'facebook', 'facebook_app_id', 'google_plus',
        'google_maps_key_api',

        //SEO information
        'keywords',

        //CMS information
        'about', 'terms', 'refunds'
    ];

    /**
     * Returns a given company locations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany(Company::class, 'company_id');
    }

    /**
     * Returns a given company users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'company_id');
    }
}
