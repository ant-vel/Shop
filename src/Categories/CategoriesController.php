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

use Antvel\Http\Controller;
use Antvel\Categories\Models\Category;
use Antvel\Categories\Requests\CategoriesRequest;

class CategoriesController extends Controller
{
    /**
     * Shows categories list.
     *
     * @return void
     */
    public function index()
    {
        return view('dashboard.sections.categories.index', [
            'categories' => Category::with('parent')->paginate(50),
        ]);
    }

    /**
     * Creates a new category.
     *
     * @return void
     */
    public function create()
    {
        return view('dashboard.sections.categories.create', [
            'parents' => Category::parents()->get()
        ]);
    }

    /**
     * Stores a new category.
     *
     * @param  CategoriesRequest $request
     *
     * @return void
     */
    public function store(CategoriesRequest $request)
    {
        $category = Category::create(
            $request->all()
        );

        return redirect()->route('categories.edit', $category)->with(
            'status', trans('globals.success_text')
        );
    }

    /**
     * Edits a given category.
     *
     * @param  Category $category
     *
     * @return void
     */
    public function edit(Category $category)
    {
        return view('dashboard.sections.categories.edit', [
            'hasParent' => ! is_null($category->parent),
            'parents' => Category::parents()->get(),
            'category' => $category->load('parent'),
        ]);
    }

    /**
     * Updates the given category.
     *
     * @param  CategoriesRequest $request
     * @param  Category $category
     *
     * @return void
     */
    public function update(CategoriesRequest $request, Category $category)
    {
        $category->update(
            $request->all()
        );

        return redirect()->route('categories.edit', $category)->with(
            'status', trans('globals.success_text')
        );
    }

}
