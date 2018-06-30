<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Features;

use InvalidArgumentException;
use Illuminate\Support\Collection;

class ValidationRulesParser
{
	/**
	 * The feature validation rules.
	 *
	 * @var null
	 */
	protected $rules = null;

	/**
	 * The allowed feature validations rules.
	 *
	 * @var array
	 */
	protected $allowed = ['required', 'max', 'min'];

	/**
	 * Parses the given rules.
	 *
	 * @param  mixed $rules
	 *
	 * @return self
	 */
	public static function parse($rules)
	{
		$parser = new static;

		$parser->rules = $parser->mapRules($rules);

		return $parser;
	}

	/**
	 * Creates an instance for the given rules.
	 *
	 * @param  null|string $rules
	 *
	 * @return self
	 */
	public static function decode($rules = null)
	{
		$parser = new static;

		if (is_string($rules) && trim($rules) !== '') {
			$parser->rules = Collection::make(explode('|', $rules));
		} else {
			$parser->rules = new Collection;
		}

		return $parser;
	}

	/**
	 * Returns a collection with the given rules.
	 *
	 * @param  array $rules
	 *
	 * @return Collection
	 */
	protected function mapRules($rules) : Collection
	{
		return Collection::make($rules)
			->only($this->allowed)
			->flatMap(function ($item, $key) {
				if ($key == 'required') {
					$rule[] = $item ? 'required' : '';
				} else {
					$rule[] = $key . ':' . $item;
				}
				return $rule;
			});
	}

	/**
	 * Returns the feature validation rules in a json format.
	 *
	 * @return null|string
	 */
	public function toString()
	{
		$rules = $this->rules->implode('|');

		if (trim($rules) == '') {
			return null;
		}

		return $rules;
	}

	/**
	 * Returns the feature validation rules in a array format.
	 *
	 * @return Collection
	 */
	public function all() : Collection
	{
		return $this->rules;
	}

	/**
	 * Returns the allowed validation rules.
	 *
	 * @return Collection
	 */
	public static function allowed()
	{
		$parser = new static;

		return Collection::make($parser->allowed);
	}
}
