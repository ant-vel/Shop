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

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator
{
	/**
	 * The illuminate request component.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request = null;

	/**
	 * Creates a new instance with the given request.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return void
	 */
	public function __construct($request)
	{
		$this->request = $request;
	}

	/**
	 * Creates a new instance with the given request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return self
	 */
	public static function trace($request)
	{
		return new static($request);
	}

	/**
	 * Returns the given items pagination.
	 *
	 * @param  mixed  $items
	 * @param  integer $perPage
	 *
	 * @return LengthAwarePaginator
	 */
	public function paginate($items, $perPage = 50)
	{
		$perPage = $perPage; // Number of items per page.
        $page = $this->request->get('page', 1); // Get the ?page=1 from the url.
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($items->toArray(), $offset, $perPage, true),  // Only grabs the needed items.
            $items->count(), // Total items.
            $perPage, // Items per page.
            $page, // Current page.
            [
                'path' => $this->request->url(),
                'query' => $this->request->query()
            ] // Keep all old query parameters from the url.
        );
	}
}
