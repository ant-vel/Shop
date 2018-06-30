<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Http;

use Antvel\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
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
        Auth::user()->confirmPetition($token, $email);

        Auth::logout();

        return redirect()->route('user.index');
    }

    /**
     * Update user's profile with a given action.
     *
     * @param  string $action
     *
     * @return void
     */
    public function update(string $action)
    {
        $action = mb_strtolower($action);

        if (! in_array($action, ['enable', 'disable'])) {
            return $this->respondsWithError(trans('globals.action_not_allowed'));
        }

        Auth::user()->$action();

    	return $this->respondsWithSuccess(trans('user.success'));
    }
}
