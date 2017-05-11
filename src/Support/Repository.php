<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Repository
{
	/**
     * The Eloquent Model.
     *
     * @var Model
     */
    protected $model = null;

    /**
     * Sets the Model to the Repo.
     *
     * @param Model $model
     *
     * @return self
     */
    protected function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Gets the Eloquent Model instance.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Returns the given model instance.
     *
     * @param  Model\Mixed $idOrModel
     *
     * @return Mixed
     */
    protected function modelOrFind($idOrModel)
    {
    	if ($idOrModel instanceof Model && $idOrModel->exists()) {
    		return $idOrModel;
    	}

    	return $this->getModel()->findOrFail($idOrModel);
    }

	/**
     * Save a new model and return the instance.
     *
     * @param  array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract public function create(array $attributes = []);

    /**
     * Update a Model in the database.
     *
     * @param array $attributes
     * @param Category|mixed $idOrModel
     * @param array $options
     *
     * @return bool
     */
    abstract public function update(array $attributes, $idOrModel, array $options = []);

    /**
     * Paginate the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null $builder
     * @param  array $options
     *
     * @return LengthAwarePaginator
     */
    public function paginate($builder = null, $options = [])
    {
		$options = array_merge(['pageName' => 'page', 'columns' => ['*'],
            'perPage' => null, 'page' => null
        ], $options);

        return $this->getModel()
        	->paginate(
        		$options['perPage'], $options['columns'], $options['pageName'], $options['page']
        	);
    }

    /**
     * Paginates the given query and load relationship.
     *
     * @param  string|array $loaders
     * @param  array $constraints
     * @param  array $paginate
     *
     * @return LengthAwarePaginator
     */
    public function paginateWith($loaders, $constraints = [], $paginate = [])
    {
        $categories = $this->getModel()->with($loaders);

        if (count($constraints) > 0) {
            $categories->where($constraints);
        }

        return $this->paginate($categories, $paginate);
    }

    /**
     * Find a Model in the Database using the given constraints.
     *
     * @param mixed $constraints
     * @param mixed $columns
     * @param array $loaders
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($constraints, $columns = '*', ...$loaders)
    {
    	if (! is_array($constraints)) {
            $constraints = ['id' => $constraints];
        }

        //We fetch the user using a given constraint.
        $model = $this->getModel()->select($columns)->where($constraints)->get();

        //We throw an exception if the user was not found to avoid whether
        //somebody tries to look for a non-existent user.
        abort_if( ! $model, 404);

        //If loaders were requested, we will lazy load them.
        if (count($loaders) > 0) {
            $model->load(implode(',', $loaders));
        }

        return $model;
    }
}
