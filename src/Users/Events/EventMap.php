<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Events;

trait EventMap
{
	/**
     * All of the Antvel User event / listener mappings.
     *
     * @var array
     */
    protected $events = [
        \Illuminate\Notifications\Events\NotificationSent::class => [
            \Antvel\Users\Listeners\UpdateNotificationType::class
        ],

		\Antvel\Users\Events\ProfileWasUpdated::class => [
            \Antvel\Users\Listeners\UpdateProfile::class,
            \Antvel\Users\Listeners\SendNewEmailConfirmation::class,
        ],

        \Antvel\Features\Events\FeatureNameWasUpdated::class => [
            \Antvel\Features\Listeners\UpdateFeatureName::class,
        ],
    ];
}
