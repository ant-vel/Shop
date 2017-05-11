<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User;

use Antvel\Http\Controller;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
	/**
     * The users repository.
     *
     * @var UsersRepository
     */
    protected $users = null;

    /**
     * Creates a new instance.
     *
     * @param UsersRepository $users
     *
     * @return void
     */
	public function __construct(UsersRepository $users)
    {
        $this->users = $users;
    }

	/**
     * Confirms the users's new email address.
     *
     * @param  string $token
     * @param  string $email
     *
     * @return void
     */
    public function confirmEmail(string $token, string $email)
    {
        $user = $this->users->profile();

        $petition = (new ChangeEmailRepository())->confirm(
            $user->id, $token, $email
        );

        if ($petition) {
            $this->users->update(
                $user, ['email' => $email]
            );
        }

        return redirect()->route('user.index');
    }

    /**
     * Update user's profile with a given action.
     *
     * @param  string $action
     * @param  int|null $user
     *
     * @return void
     */
    public function update(string $action, $user = null)
    {
    	$allowed = ['enable', 'disable'];

    	if (! in_array($action, ['enable', 'disable'])) {
    		return $this->respondsWithError('action not allowed');
    	}

    	$action = mb_strtolower($action);

    	$response = $this->user->$action($user);

    	if ($response == 'notOk') {
            $message = 'There was an error trying to update your information. Please, Try again!';
        } else {
            $message = 'Your profile has been successfully updated.';
        }

    	return $this->respondsWithSuccess($message);
    }
}
