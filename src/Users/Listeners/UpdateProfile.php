<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Listeners;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Antvel\Users\Events\ProfileWasUpdated;

class UpdateProfile
{
    /**
     * Handle the event.
     *
     * @param  ProfileWasUpdated  $event
     *
     * @return void
     */
    public function handle(ProfileWasUpdated $event)
    {
        //If the user requested a new email address, we create a new email change
        //petition record in the database and send out a confirmation email.
        if ($continuePropagation = $this->emailWasChanged($event)) {
            $this->createNewEmailPetition($event);
        }

        $this->updateUserPorfile($event->request);

        //The event propagation will continue as long as the user changed his email address.
        return $continuePropagation;
    }

    /**
     * Checks whether the user changed his email address.
     *
     * @param  ProfileWasUpdated $event
     *
     * @return bool
     */
    protected function emailWasChanged(ProfileWasUpdated $event) : bool
    {
        $newEmail = $event->request->get('email');

        return ! is_null($newEmail) && $newEmail != Auth::user()->email;
    }

    /**
     * Creates a new email petition.
     *
     * @param  ProfileWasUpdated $event
     *
     * @return void
     */
    protected function createNewEmailPetition(ProfileWasUpdated $event)
    {
       $event->petition = Auth::user()->makePetitionFor($event->request['email']);

        //By selecting a new email address users have to confirm their new selection
        //in order for them to use it as their new email account. Therefore,
        //we will not update the given user email address until he
        //confirms it through his new email account.
        unset($event->request['email']);
    }

    /**
     * Updates the user profile with the given data.
     *
     * @param  \Illuminate\Http\Request|Collection $data
     *
     * @return void
     */
    protected function updateUserPorfile($data)
    {
        $data = $data->except('email');

        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        Auth::user()->update($data);
    }
}
