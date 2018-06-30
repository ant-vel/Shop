<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Notifications\Parsers;

use Antvel\Antvel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Filesystem\Filesystem;

class Templates
{
	/**
	 * The notification resources.
	 *
	 * @var null
	 */
	protected $source = null;

	/**
	 * The notification labels.
	 *
	 * @var array
	 */
	protected $labels = [];

	/**
	 * The default labels file name.
	 *
	 * @var string
	 */
	protected $templatesFile = 'notifications';

	/**
	 * Creates a new instance.
	 *
	 * @param string
	 */
	public function __construct(string $source)
	{
		$this->source = $source;
		$this->labels = $this->resolve();
	}

	/**
	 * Creates a new static instance.
	 *
	 * @param string
	 */
	public static function make(string $source)
	{
		return new static($source);
	}

	/**
	 * Resolves the notification resources.
	 *
	 * @return array
	 */
	protected function resolve()
	{
		$key = $this->templatesFile . '.' . $this->source;

		if (Lang::has($key)) {
			return Lang::get($key);
		}

		return $this->default();
	}

	/**
	 * Returns the default notifications resources.
	 *
	 * @return array
	 */
	public function default()
	{
		$file = Antvel::langPath() . DIRECTORY_SEPARATOR . "en" . DIRECTORY_SEPARATOR . $this->templatesFile . ".php";

		$templates = (new Filesystem)->getRequire($file);

		return Arr::get($templates, $this->source);
	}

	/**
	 * Returns all the labels.
	 *
	 * @return array
	 */
	public function all()
	{
		return is_null($this->labels) ? [] : $this->labels;
	}

	/**
	 * Returns the given key label.
	 *
	 * @param  string $key
	 *
	 * @return string
	 */
	public function get(string $key)
	{
		return Arr::get($this->labels, $key);
	}
}
