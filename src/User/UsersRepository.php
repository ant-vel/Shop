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
     * @param mixed $constraints
     * @param array $loaders
     *
     * @return null|Authenticatable
     */
	public function find($constraints, ...$loaders)
	{
        if (! is_array($constraints)) {
            $constraints = ['id' => $constraints ?? $this->id];
        }

        //We fetch the user using a given constraint.
        $user = User::where($constraints)->first();

        //We throw an exception if the user was not found to avoid whether
        //somebody tries to look for a non-existent user.
        abort_if( ! $user, 404);

        //If loaders were requested, we will lazy load them.
        if (count($loaders) > 0) {
            $user->load(implode(',', $loaders));
        }

        return $user;
	}

    /**
     * Returns the user profile.
     *
     * @param  int $user_id
     *
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
     * Creates a new user in the database.
     *
     * @param  array $data
     *
     * @return Authenticatable
     */
    public function create(array $data) : Authenticatable
    {
        $data = Parser::parse($data);

        $user = User::create(
            $data->except('profile')->all()
        );

        $user->profile()->create(
            $data->only('profile')->collapse()->all()
        );

        return $user;
    }

    /**
     * Updates the user information with a given data.
     *
     * @param  Authenticatable $user
     * @param  array $data
     * @param  array $except
     *
     * @return void
     */
    public function update($user, array $data, array $except = [])
    {
        $data = Parser::parse($data, $user->id)->all();

        $user->fill($data)->save();
        $user->profile->fill($data)->save();
    }

    /**
     * Enables and Disables a given user profile.
     *
     * @param  int|null $user_id
     * @param  string $action
     *
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
     *
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
     *
     * @return string
     */
    public function enable($user_id = null)
    {
        return $this->enableDisable($user_id, 'enable');
    }

    /**
     * Updates the user preferences for a given key and data.
     *
     * @param  string $key
     * @param  mixed $data
     *
     * @return self
     */
    public static function updatePreferences(string $key, $data)
    {
        $self = new static (Container::getInstance());

        if ($self->isLoggedIn()) {
            $user = $self->user();

            $user->preferences = Preferences::parse($user->preferences)
                ->update('my_searches', $data)
                ->toJson();

            $user->save();
        }
    }
}
