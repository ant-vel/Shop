<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Integrations;

use Antvel\Antvel;
use Antvel\Tests\TestCase;

class RegisterProvidersTest extends TestCase
{
	/** @test */
	function check_whether_the_antvel_providers_were_loaded()
	{
		foreach (Antvel::providers() as $provider) {
			$this->assertArrayHasKey($provider, $this->app->getLoadedProviders());
		}
	}
}
