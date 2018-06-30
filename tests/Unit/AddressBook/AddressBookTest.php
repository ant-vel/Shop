<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\AddressBook;

use Antvel\Tests\TestCase;

class AddressBookTest extends TestCase
{
    /** @test */
    function an_address_belongs_to_a_user()
    {
        $user = factory('Antvel\Users\Models\User')->create();
        $address = factory('Antvel\AddressBook\Models\Address')->create(['user_id' => $user->id]);

        $this->assertInstanceOf('Antvel\Users\Models\User', $address->user);
    }
}
