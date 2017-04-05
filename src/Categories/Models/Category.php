<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Categories\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	/**
     * The database table.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'category_id', 'name', 'description', 'icon',
        'image', 'status', 'type',
    ];

    /**
     * The parent category.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function children()
    {
        return $this->hasMany(Category::class);
    }

    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
