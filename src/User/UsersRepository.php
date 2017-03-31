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

use Carbon\Carbon;
use Antvel\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Authenticatable;

class UsersRepository
{
    /**
     * The laravel auth component.
     *
     * @var Container
     */
    protected $auth = null;

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
     * Gets a field from the user model.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->user()->$key ?? null;
    }

    /**
     * Returns the logged user.
     *
     * @return Authenticatable
     */
    public function user()
    {
        return $this->auth->user();
    }

    /**
     * Checks whether the user is logged in.
     *
     * @return boolean
     */
    public function isLoggedIn() : bool
    {
        return !! $this->auth->check();
    }

    /**
     * Finds a given user in the database.
     *
     * @param int $user_id
     * @param array $loaders
     * @return null|Authenticatable
     */
	public function find($user_id = null, ...$loaders)
	{
        //We fetch the user using either the given id. If the id was not given,
        //we use the one in session..
        $user = User::where('id', $user_id ?? $this->id)->first();

        //We throw an exception if the user was not found, so we avoid the fact
        //that somebody tries to look for a non-existent user..
        abort_if( ! $user, 404);

        //If there was any requested loader, we will try to lazy load the relationship
        //related to it within retrieved query.
        if (count($loaders) > 0) {
            $user->load(implode(',', $loaders));
        }

        return $user;
	}

    /**
     * Returns the user profile.
     *
     * @param  int $user_id
     * @return null|Authenticatable
     */
    public function profile($user_id = null)
    {
        //if user id was not provided, we assumed the update will be done on the logged user.
        if (is_null($user_id)) {
            return $this->user();
        }

        //we retrieve the user for a given id.
        return $this->find($user_id, 'profile');
    }

    /**
     * Updates the user information with a given data.
     *
     * @param  Authenticatable $user
     * @param  array $data
     * @param  array $except
     * @return void
     */
    public function update($user, array $data, array $except = [])
    {
        $data = $this->normalizeData(
          $user, Collection::make($data)->except($except)
        );

        $user->fill($data)->save();
        $user->profile->fill($data)->save();
    }

    /**
     * Returns a normalized data to be used within insert/update calls.
     *
     * @param  Authenticatable $user
     * @param  Collection $data
     * @return array
     */
    protected function normalizeData($user, Collection $data) : array
    {
        if ($data->has('password')) {
            $data['password'] = bcrypt($data['password']);
        }

        if ($data->has('file')) {
            $data['pic_url'] = $data['file']->store('img/profile/' . $user->id);
            $data->forget('file');
        }

        return $data->all();
    }

    /**
     * Enables and Disables a given user profile.
     *
     * @param  int|null $user_id
     * @param  string $action
     * @return string
     */
    public function enableDisable($user_id = null, $action = 'disable') : string
    {
        $user = is_null($user_id) ? $this->user() : $this->find($user_id);

        if (! $user) {
            return 'notOk';
        }

        $this->update($user, [
            'disabled_at' => $action == 'disable' ? Carbon::now() : null
        ]);

        return 'ok';
    }

    /**
     * Disables a given user profile.
     *
     * @param  int $user_id
     * @return string
     */
    public function disable($user_id = null)
    {
        return $this->enableDisable($user_id);
    }

    /**
     * Enables a given user profile.
     *
     * @param  int $user_id
     * @return string
     */
    public function enable($user_id = null)
    {
        return $this->enableDisable($user_id, 'enable');
    }
}
