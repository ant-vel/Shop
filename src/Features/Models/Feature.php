<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Features\Models;

use Illuminate\Database\Eloquent\Model;
use Antvel\Features\ValidationRulesParser;

class Feature extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'features';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'input_type', 'product_type', 'default_values',
        'validation_rules', 'help_message', 'status',
        'filterable'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'filterable' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Set the validation_rules with the given value.
     *
     * @param  array|string  $value
     *
     * @return void
     */
    public function setValidationRulesAttribute($value)
    {
        //If the passed value is a string, we assume the request wants to save a validation
        //string. Otherwise, we parse the array given to build such a string.
        if (is_string($value)) {
            $this->attributes['validation_rules'] = $value;
        }

        else {
            $this->attributes['validation_rules'] = ValidationRulesParser::parse($value)->toString();
        }
    }

    /**
     * Exposes the features allowed to be in the products filtering.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterable($query)
    {
        return $query->where('status', true)->where('filterable', true);
    }
}
