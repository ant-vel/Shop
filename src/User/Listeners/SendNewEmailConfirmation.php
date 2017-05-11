<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Antvel\User\Events\ProfileWasUpdated;
use Antvel\User\Mail\NewEmailConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewEmailConfirmation implements ShouldQueue
{
    /**
     * The Laravel mail component.
     *
     * @var Mailer
     */
    protected $mailer = null;

    /**
     * Create a new event instance.
     *
     * @param array $request
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * Handle the event.
     *
     * @param  ProfileWasUpdated  $event
     *
     * @return void
     */
    public function handle(ProfileWasUpdated $event)
    {
        if (! is_null($event->petition)) {
            $this->mailer->send(
                new NewEmailConfirmation($event->petition, $event->user)
            );
        }
    }
}
