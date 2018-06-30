<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Antvel\Users\Http\Middleware;

use Closure;
use Antvel\Users\Models\User;

class Managers
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure  $next
     * @param string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = $request->user();

        if ($this->isAuthorized($user)) {
            return $next($request);
        }

        abort(401);
    }

    /**
     * Checks whether the given user is authorized.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     *
     * @return bool
     */
    protected function isAuthorized($user)
    {
        return $user->can('manage-store', User::class);
    }
}
