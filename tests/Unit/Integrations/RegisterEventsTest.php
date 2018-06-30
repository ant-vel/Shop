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

use Antvel\Tests\TestCase;

class RegisterEventsTest extends TestCase
{
	public function setUp()
    {
    	parent::setUp();

    	$this->events = [
    		\Antvel\Features\Events\FeatureNameWasUpdated::class,
    		\Antvel\Users\Events\ProfileWasUpdated::class,
    	];
    }

	/** @test */
	function it_is_able_to_register_the_antvel_events_within_the_application()
	{
		$this->app->booted(function () {
	    	foreach ($this->events as $event) {
	    		$this->assertTrue(count($this->app->make('events')->getListeners($event)) > 0);
	    	}
    	});
	}
}
