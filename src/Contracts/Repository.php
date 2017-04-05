<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Contracts;

interface Repository
{
	/**
     * Save a new model and return the instance.
     *
     * @param  array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes = []);

    /**
     * Find a Model in the Database using the given constraints.
     *
     * @param mixed $constraints
     * @param mixed $columns
     * @param array $loaders
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($constraints, $columns = '*', ...$loaders);
}
