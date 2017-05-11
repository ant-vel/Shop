<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Categories\Requests;

use Antvel\Http\Request;
use Illuminate\Validation\Rule;
use Antvel\Categories\Categories;

class CategoriesRequest extends Request
{
    /**
     * The categories repository.
     *
     * @var Categories
     */
    protected $categories = null;

    /**
     * Creates a new instance.
     *
     * @param Categories $categories
     *
     * @return void
     */
    public function __construct(Categories $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => 'required|max:100',

            '_pictures_file' => [
                'mimes:jpeg,png,jpg',
                Rule::dimensions()->maxWidth(1000)->maxHeight(500)
            ],

            'name' => [
                'required',
                Rule::unique('categories')->ignore($this->request->get('current_category')),
            ],
        ];
    }
}
