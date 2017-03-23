<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests;

use Antvel\Tests\TestCase;
use Antvel\Tests\Stubs\User;
use Antvel\AddressBook\AddressBook;
use Antvel\AddressBook\Models\Address;

class AddressBookTest extends TestCase
{
    /**
     * Temporary user.
     *
     * @var Antvel\User\Models\User
     */
    protected $user = null;

    /**
     * Temporary addressbook repository.
     *
     * @var Antvel\AddressBook\Repository
     */
    protected $addressBook = null;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create()->first();
        $this->addressBook = $this->app->make(AddressBook::class);
    }

	public function test_the_given_user_must_has_2_addresses()
    {
        factory(Address::class, 2)->create([
            'user_id' => $this->user->id
        ]);

    	$this->assertCount(2, $this->user->addresses);
    }

    public function test_find_address_by_id()
    {
        factory(Address::class)->create([
            'user_id' => $this->user->id
        ]);

    	$address = $this->addressBook->find(1)->first();
    	$this->assertTrue($this->user->id == $address->user_id);
    }

    public function test_create_a_new_address()
    {
    	$address = $this->addressBook->create(
            $this->fakedData(), $this->user
        );

        $this->assertTrue($this->user->id == $address->user_id);
    }

    public function test_create_a_new_address_and_set_it_as_default()
    {
        $address = $this->addressBook->createAndSetToDefault(
    		$this->fakedData(), $this->user
    	);

    	$this->assertTrue((bool) $address->default);
    }

    /**
     * @expectedException  Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_destroy_an_address_from_the_database()
    {
        $address = factory(Address::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->addressBook->destroy($address->id);
        $address = $this->addressBook->find($address->id)->first();

        $this->assertNull($address);
    }

    public function test_retrieves_addresses_for_a_logged_user()
    {
        factory(Address::class, 2)->create([
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user);
        $addresses = $this->addressBook->forUser();
        $this->assertNotEmpty($addresses);
        $this->assertCount(2, $addresses);
    }

    public function test_retrieves_addresses_for_a_given_user()
    {
        factory(Address::class, 2)->create([
            'user_id' => $this->user->id
        ]);

        $addresses = $this->addressBook->forUser($this->user);

        $this->assertNotEmpty($addresses);
        $this->assertCount(2, $addresses);
    }

    /**
     * Returns a fake address for a given user.
     *
     * @param  int $user_id
     * @return array
     */
    protected function fakedData(int $user_id = null)
    {
		return [
            'default' => 0,
            'city' => 'Guacara',
            'zipcode' => '2001',
            'state' => 'Carabobo',
            'country' => 'Venezuela',
            'phone' => '+ 1 405 669 00 00',
            'name_contact' => 'Gustavo Ocanto',
			'user_id' => $user_id ?: $this->user->id,
            'line1' => 'Urb. Augusto Malave Villalba',
            'line2' => 'Conj#2, Piso#6, Apt#6-2, Los Azules',
		];
    }
}
