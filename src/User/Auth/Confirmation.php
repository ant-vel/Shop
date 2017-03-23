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

use Antvel\Antvel;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class Confirmation
{
    /**
     * The registration token.
     *
     * @var string
     */
	protected $token = '';

    /**
     * The registered user email.
     *
     * @var string
     */
	protected $email = '';

    /**
     * The user related to the confirmation process.
     *
     * @var Illuminate\Contracts\Auth\Authenticatable
     */
	protected $user = null;

	/**
     * Creates a new instance.
     *
     * @param string $token
     * @param string $email
     * @return void
     */
    public function __construct(string $token, string $email)
    {
    	$this->token = $token;
    	$this->email = $email;
        $this->user = $this->fetch();
    }

    /**
     * Creates a new static instance.
     *
     * @param string $token
     * @param string $email
     * @return void
     */
    public static function make(string $token, string $email)
    {
    	$verifier = new static($token, $email);

    	return $verifier;
    }

    /**
     * Fetches an user using the given token and email address.
     *
     * @return null|Illuminate\Contracts\Auth\Authenticatable
     */
    protected function fetch()
    {
    	$user = Antvel::user();

    	return $user::where([
            'confirmation_token' => $this->token,
            'email' => $this->email,
            'verified' => 'no',
        ])->firstOrFail();
    }

    /**
     * Checks whether the user was found.
     *
     * @return bool
     */
    public function passes() : bool
    {
		return ! is_null($this->user);
    }

    /**
     * Activates the retrieved user.
     *
     * @return void
     */
    public function activateUser()
    {
        $this->user->verified = 'yes';
        $this->user->save();

        $this->login();
    }

    /**
     * Login the retrieved user.
     *
     * @return void
     */
    protected function login()
    {
    	Container::getInstance()
        	->make(AuthFactory::class)
        	->login($this->user);
    }
}
