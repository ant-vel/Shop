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

use Antvel\Antvel;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use Environment;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFactories();
        $this->loadMigrations();
    }
}
