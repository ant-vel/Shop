<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\BackOffice;

use Illuminate\Http\Request;
use Antvel\Foundation\Http\Controller;

class DashBoardController extends Controller
{
	/**
	 * Loads the back office dashboard.
	 *
	 * @return void
	 */
	public function index()
	{
		return view('antvel::BackOffice.dashboard');
	}
}
