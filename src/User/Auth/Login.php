<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Auth;

use Illuminate\Http\Request;
use Illuminate\Container\Container;

class Login
{
	/**
	 * The laravel auth component.
	 *
	 * @var Illuminate\Contracts\Auth\Factory
	 */
	protected $auth = null;

	/**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Failure message.
     *
     * @var string
     */
    protected $failedMessage = 'The credentials do not match our records';

    /**
     * Creates a new instance.
     *
     * @return void
     */
	public function __construct(Container $container)
    {
    	$this->auth = $container->make('auth');
    }

    /**
     * Sets the path destination after user is logged in.
     *
     * @param  string $path
     * @return self
     */
    public function withRedirectTo(string $path) : self
    {
    	$this->redirectTo = $path;

    	return $this;
    }

    /**
     * Sets a failure message.
     *
     * @param  string $message
     * @return self
     */
    public function withFailedMessage(string $message) : self
    {
    	$this->failedMessage = $message;

    	return $this;
    }

    /**
     * Authenticates an user.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function authenticate(Request $request)
    {
    	$credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $attempt = $this->auth->attempt(
            $credentials, $request->has('remember')
        );

        if ($attempt) {
            return redirect($this->redirectTo);
        }

        return $this->redirectWithErrors($request);
    }

    /**
     * Redirects back to the login page and send related errors.
     *
     * @param  Request $request
     * @return void
     */
    protected function redirectWithErrors(Request $request)
    {
        return redirect()->to('login')
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => $this->failedMessage,
            ]);
    }
}
