<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Integrations;

use Antvel\Tests\TestCase;
use Antvel\Contracts\CategoryRepositoryContract;
use Antvel\Contracts\FeaturesRepositoryContract;
use Antvel\Features\Repositories\FeaturesRepository;
use Antvel\Categories\Repositories\CategoriesRepository;

class ContractsBindingsTest extends TestCase
{
	/** @test */
	function it_returns_the_categories_repository_object_when_referring_to_its_contract()
	{
		$categoriesRepository = $this->app->make(CategoryRepositoryContract::class);

		$this->assertNotNull($categoriesRepository);
		$this->assertInstanceOf(CategoriesRepository::class, $categoriesRepository);
	}

	/** @test */
	function it_returns_the_features_repository_object_when_referring_to_its_contract()
	{
		$categoriesRepository = $this->app->make(FeaturesRepositoryContract::class);

		$this->assertNotNull($categoriesRepository);
		$this->assertInstanceOf(FeaturesRepository::class, $categoriesRepository);
	}
}
