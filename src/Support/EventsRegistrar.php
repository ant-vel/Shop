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

use Illuminate\Support\Facades\Event;

class EventsRegistrar
{
	 /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Antvel\User\Events\ProfileWasUpdated::class => [
            \Antvel\User\Listeners\UpdateProfile::class,
            \Antvel\User\Listeners\SendNewEmailConfirmation::class,
        ],
    ];

    /**
     * Registers the antvel events and listeners.
     *
     * @return void
     */
	public function registrar()
	{
		foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
	}
}
