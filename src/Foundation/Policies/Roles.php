<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Foundation\Policies;

class Roles
{
	public static function default()
	{
		return 'person';
	}

	public static function allowed()
	{
		return ['nonprofit', 'admin', 'business', 'person'];
	}
}
