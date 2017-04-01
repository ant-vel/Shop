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

use Antvel\User\Events\ProfileWasUpdated;
use Antvel\User\UsersRepository as Users;
use Antvel\User\ChangeEmailRepository as ChangeEmail;

class UpdateProfile
{
    /**
     * The users repository.
     *
     * @var Users
     */
    protected $users = null;

    /**
     * The email petitions repository.
     *
     * @var EmailPetitions
     */
    protected $changeEmail = null;

    /**
     * Creates a new instance.
     *
     * @param Users $users
     * @param EmailPetitions $changeEmail
     * @return void
     */
    public function __construct(Users $users, ChangeEmail $changeEmail)
    {
        $this->users = $users;
        $this->changeEmail = $changeEmail;
    }

    /**
     * Handle the event.
     *
     * @param  ProfileWasUpdated  $event
     * @return void
     */
    public function handle(ProfileWasUpdated $event)
    {
        //If the user requested a new email address, we create a new email change
        //petition record in the database and send out a confirmation email.
        if ($continuePropagation = $this->emailWasChanged($event)) {
            $this->createNewEmailPetition($event);
        }

        $this->users->update(
           $event->user, $event->request, $except = ['email']
        );

        //If the user did not ask for a new email address, We stop the event propagation.
        return $continuePropagation;
    }

    /**
     * Checks whether the user changed his email address.
     *
     * @param  ProfileWasUpdated $event
     * @return bool
     */
    protected function emailWasChanged(ProfileWasUpdated $event) : bool
    {
        $request = $event->request;

        return isset($request['email']) &&
            $request['email'] != $event->user->email &&
            $request['email'] !== null;
    }

    /**
     * Creates a new email petition.
     * @param  ProfileWasUpdated $event
     * @return void
     */
    protected function createNewEmailPetition(ProfileWasUpdated $event)
    {
        $event->petition = $this->changeEmail->store([
            'old_email' => $event->user->email,
            'user_id' => $event->user->id,
            'request' => $event->request,
        ]);

        //We delete the email field because the user will have to confirm it clicking
        //on the link sent to his requested email address.
        if (isset($event->request['email'])) {
            unset($event->request['email']);
        }
    }
}
