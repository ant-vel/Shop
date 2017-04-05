<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit;

use Antvel\User\Models\Person;
use Illuminate\Support\Collection;
use Antvel\Tests\Features\Users\UsersTestCase;
use Illuminate\Contracts\Auth\Authenticatable;

class UsersRepositoryTest extends UsersTestCase
{
	/**
	 * The testing person.
	 *
	 * @var \Antvel\User\Models\Person
	 */
	protected $person = null;

	/**
	 * The users's repository.
	 *
	 * @var \Antvel\User\UsersRepository
	 */
	protected $repository = null;

	public function setUp()
	{
		parent::setUp();

		$this->person = $this->person();
		$this->repository =  $this->app->make('Antvel\User\UsersRepository');
	}

	public function test_a_repository_can_retrieve_a_signed_user()
	{
		$this->actingAs($this->person->user);
		$profile = $this->repository->user();

		$this->assertEquals($this->person->user->id, $profile->id);
		$this->assertInstanceOf(Authenticatable::class, $profile);
	}

	public function test_a_repository_can_check_whether_a_user_is_signed_in()
	{
		$this->actingAs($this->person->user);

		$this->assertTrue($this->repository->isLoggedIn());
	}

	public function test_a_repository_can_find_users_in_the_database()
	{
		$user = $this->repository->find(
			$this->person->user_id
		);

		$this->assertInstanceOf(Authenticatable::class, $user);
		$this->assertEquals($user->id, $this->person->user_id);
	}

	public function test_a_repository_is_able_to_load_users_relationship()
	{
		$profile = $this->repository->find(
			$this->person->user_id, 'profile'
		);

		$this->assertTrue($profile->profile->count() > 0);
	}

	public function test_a_repository_can_show_a_user_profile()
	{
		$profile = $this->repository->profile(
			$this->person->user_id
		);

		$this->assertInstanceOf(Authenticatable::class, $this->person->user);
		$this->assertEquals($this->person->user->id, $profile->id);
		$this->assertInstanceOf(Authenticatable::class, $profile);
		$this->assertTrue($profile->profile->count() > 0);
	}

	public function test_a_repository_is_able_to_update_a_user_profile()
	{
		$this->repository->update($this->person->user, [
			'first_name' => 'Gustavo',
			'last_name' => 'Ocanto',
			'nickname' => 'gocanto',
		]);

		$newPerson = $this->repository->profile($this->person->user_id);

		$this->assertEquals($newPerson->profile->fullName, 'Gustavo Ocanto');
		$this->assertEquals($newPerson->nickname, 'gocanto');
	}

	public function test_a_repository_is_able_to_create_a_user_profile()
	{
		$user = $this->repository->create([
            'password' => '123456',
            'email' => 'gustavoocanto@gmail.com',
            'nickname' => 'gocanto',
            'role' => 'person',
            'profile' => [
                'first_name' => 'Gustavo',
                'last_name'  => 'Ocanto',
            ]
        ]);

		$this->assertInstanceOf(Authenticatable::class, $user);
        $this->assertInstanceOf(Person::class, $user->profile);
        $this->assertEquals($user->email, 'gustavoocanto@gmail.com');
        $this->assertEquals($user->profile->fullName, 'Gustavo Ocanto');

		$this->assertTrue(
			$this->app->make('hash')->check('123456', $user->password)
		);

	}

	public function test_a_repository_is_able_to_update_the_user_password()
	{
		$this->repository->update($this->person->user, [
			'password' => '123456',
		]);

		$hash = $this->app->make('hash');

		$newPerson = $this->repository->profile($this->person->user_id);

		$this->assertTrue(
			$hash->check('123456', $newPerson->password)
		);
	}

	public function test_a_repository_can_enable_users()
	{
		$user_id = $this->person->user_id;

		$this->repository->enable($user_id);

		$this->assertNull($this->person->user->disabled_at);
	}

	public function test_a_repository_can_disable_users()
	{
		$user_id = $this->person->user_id;

		$this->repository->disable($user_id);

		$this->assertTrue(! is_null($this->person->user->disabled_at));
	}
}
