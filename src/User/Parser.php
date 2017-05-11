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

use Illuminate\Support\Collection;
use Illuminate\Hashing\BcryptHasher;

class Parser
{
	/**
	 * The data to be persisted.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * The laravel BcryptHasher component.
	 *
	 * @var BcryptHasher
	 */
	protected $bcrypt = null;

	/**
	 * Creates a new instance.
	 *
	 * @return void
	 */
	public function __construct()
    {
    	$this->bcrypt = new BcryptHasher;
    }

	/**
	 * Parses the user data.
	 *
	 * @param  array  $data
	 * @param  mixed $user_id
	 *
	 * @return array
	 */
	public static function parse(array $data, $user_id = null) : Collection
	{
		if (count($data) == 0) {
			return Collection::make($data);
		}

		$parser = new static;

		$parser->data = Collection::make($data);

		$parser->parsePassword();
		$parser->parseFile($user_id);

        return $parser->data;
	}

	/**
	 * Parse the password field.
	 *
	 * @return void
	 */
	protected function parsePassword()
	{
		if ($this->data->has('password') && $this->data->get('password') !== null) {
			$this->data['password'] = $this->bcrypt->make($this->data['password']);

			return;
		}

		$this->data->forget('password');
		$this->data->forget('old_password');
		$this->data->forget('password_confirmation');
	}

	/**
	 * Parse the pic url field.
	 *
	 * @return void
	 */
	protected function parseFile($user_id = 0)
	{
		if ($this->data->has('file') && $this->data->get('file') !== null) {
            $this->data['pic_url'] = $this->data['file']->store('img/profile/' . $user_id);
        }

        $this->data->forget('file');
	}
}
