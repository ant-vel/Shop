<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Categories;

use Antvel\Support\Pictures;
use Antvel\Support\Repository;
use Antvel\Categories\Models\Category;

class Categories extends Repository
{
    /**
     * Creates a new instance.
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->setModel($category);
    }

    /**
     * The files directory.
     *
     * @var string
     */
    protected $filesDirectory = 'img/categories';

	/**
	 * Creates a new category with a given attributes.
	 *
	 * @param  array $attributes
     *
	 * @return Category
	 */
    public function create(array $attributes = []) : Category
	{
        if (isset($attributes['_pictures_file'])) {
            $attributes['image'] = Pictures::prepare($attributes)->store($this->filesDirectory);
        }

        return Category::create($attributes);
	}

    /**
     * Update a Model in the database.
     *
     * @param array $attributes
     * @param Category|mixed $idOrModel
     * @param array $options
     *
     * @return bool
     */
    public function update(array $attributes, $idOrModel, array $options = [])
    {
        $category = $this->modelOrFind($idOrModel);

        $picture = Pictures::prepare($attributes);

        if ($picture->wasUpdated()) {
           $attributes['image'] = $picture->store($this->filesDirectory);
        }

        return $category->update($attributes, $options);
    }

    /**
     * Returns the master categories.
     *
     * @param  mixed $columns
     * @param  int $limit
     *
     * @return \Illuminate/Database/Eloquent/Collection
     */
    public function parents($columns = '*', $limit = 50)
    {
        return Category::select($columns)
            ->whereNull('category_id')
            ->take($limit)
            ->get();
    }

    /**
     * Returns the master categories except the given one.
     *
     * @param  int $category_id
     * @param  mixed $columns
     * @param  int $limit
     *
     * @return \Illuminate/Database/Eloquent/Collection
     */
    public function parentsExcept($category_id, $columns = '*', $limit = 50)
    {
        return Category::select($columns)
            ->whereNull('category_id')
            ->where('id', '<>', $category_id)
            ->take($limit)
            ->get();
    }

    /**
     * Returns the children for a given category.
     *
     * @param  Category|mixed $idOrModel $idOrModel
     * @param array $columns
     * @param int $limit
     *
     * @return \Illuminate/Database/Eloquent/Collection
     */
    public function children($idOrModel, $columns = ['id'], int $limit = 50)
    {
        $category_id = $idOrModel instanceof Category ? $idOrModel->id : $idOrModel;

        return Category::select($columns)->with('children')
            ->where('category_id', $category_id)
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }
}
