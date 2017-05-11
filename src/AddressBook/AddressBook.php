<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\AddressBook;

use Illuminate\Support\Collection;
use Antvel\AddressBook\Models\Address;
use Illuminate\Contracts\Auth\Authenticatable;

class AddressBook
{
	/**
	 * The user who owns the address.
	 *
	 * @var Antvel\Customer\Models\User
	 */
	protected $user = null;

	/**
	 * Return the current user.
	 *
	 * @return Antvel\Customer\Models\User
	 */
	protected function user() : Authenticatable
	{
		return auth()->user();
	}

	/**
	 * Retrieve the address book for a given user.
	 *
	 * @param  User|null $user
	 * @param  string $sort
	 *
	 * @return Collection
	 */
	public function forUser(Authenticatable $user = null, string $sort = 'default') : Collection
	{
		$user = is_null($user) ? $this->user() : $user;

		return $user->addresses->sortByDesc($sort);
	}

	/**
	 * Retrieve a requested address.
	 *
	 * @param int $id
	 *
	 * @return Address
	 */
	public function find(int $id) : Address
	{
		return Address::findOrFail($id);
	}

	/**
	 * Create an address in the database.
	 *
	 * @param  array $data
	 *
	 * @return Address
	 */
	public function create(array $data, Authenticatable $user = null) : Address
	{
		$this->user = $user;

		$data = array_merge([
			'user_id' => $this->user_id()
		], $data);

		return Address::create($data);
	}

	/**
	 * Crate a new address in the database and set it to default.
	 *
	 * @param  array $data
	 *
	 * @return Address
	 */
	public function createAndSetToDefault(array $data, Authenticatable $user = null) : Address
	{
		$address = $this->create($data, $user);
		$this->setDefault($address);

		return $address;
	}

	/**
	 * Set to default a given address.
	 *
	 * @param Address\int $address
	 *
	 * @return void
	 */
	public function setDefault($address)
	{
		$this->resetDefault();

		if (is_integer($address)) {
			$address = $this->find($address);
		}

		$address->default = 1;
		$address->save();
	}

	/**
	 * Reset the default address.
	 *
	 * @return void
	 */
	protected function resetDefault()
	{
		Address::where('user_id', $this->user_id())
			->where('default', 1)
			->update(['default' => 0]);
	}

	/**
	 * Destroy a given address.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function destroy(int $id)
	{
		Address::destroy($id);
	}

	/**
	 * Return the user id.
	 *
	 * @return int
	 */
	protected function user_id() : int
	{
		return is_null($this->user) ? $this->user()->id : $this->user->id;
	}
}
