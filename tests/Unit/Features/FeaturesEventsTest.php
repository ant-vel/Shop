<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Features;

use Antvel\Tests\TestCase;
use Antvel\Features\Listeners\UpdateFeatureName;
use Antvel\Features\Events\FeatureNameWasUpdated;

class FeaturesEventsTest extends TestCase
{
	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function update_feature_name_and_related_products_features_by_emitting_an_event()
	{
		$this->usingMySql();

		$feature = factory('Antvel\Features\Models\Feature')->states('filterable')->create([
			'validation_rules' => 'required',
			'name' => 'color',
			'help_message' => 'old help message',
			'status' => true,
		]);

		$productA = factory('Antvel\Product\Models\Product')->create(['name' => 'white', 'features' => '{"color": "white", "weight": "11"}']);
		$productB = factory('Antvel\Product\Models\Product')->create(['name' => 'red', 'features' => '{"color": "red", "weight": "12"}']);
		$productC = factory('Antvel\Product\Models\Product')->create(['name' => 'none', 'features' => '{"weight": "13"}']);

		event($event = new FeatureNameWasUpdated($feature, 'foo'));
		$listener = new UpdateFeatureName();
		$listener->handle($event);

		tap($feature->fresh(), function ($feature) {
			$this->assertEquals('foo', $feature->name);
			$this->assertEquals('required', $feature->validation_rules);
			$this->assertEquals('old help message', $feature->help_message);
			$this->assertTrue($feature->status);
		});

		tap($productA->fresh()->features, function ($features) {
			$this->assertEquals('white', $features['foo']);
			$this->assertEquals('11', $features['weight']);
		});

		tap($productB->fresh()->features, function ($features) {
			$this->assertEquals('red', $features['foo']);
			$this->assertEquals('12', $features['weight']);
		});

		tap($productC->fresh()->features, function ($features) {
			$this->assertTrue(empty($features['foo']));
			$this->assertEquals('13', $features['weight']);
		});

		$this->artisan('migrate:reset', ['--database' => self::TESTING_DB]);
	}
}
