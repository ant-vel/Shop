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

class Label
{
	/**
	 * The notification data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * The source where to select labels from.
	 *
	 * @var string
	 */
	protected $source = '';

	/**
	 * Creates a new instance for the given resource.
	 *
	 * @param  string $source
	 *
	 * @return self
	 */
	public function __construct(string $source)
	{
		$this->source = $source;
	}

	/**
	 * Creates a new static instance for the given resource.
	 *
	 * @param  string $source
	 *
	 * @return self
	 */
	public static function make(string $source)
	{
		return new static($source);
	}

	/**
	 * Set the notification data.
	 *
	 * @param  mixed $data
	 *
	 * @return self
	 */
	public function with($data)
	{
		if (get_parent_class($data) === \Illuminate\Database\Eloquent\Model::class) {
			$this->data = [
				'source_id' => $data->id,
				'status' => $data->status
			];
		}

		else {
			$this->data = $data;
		}

		return $this;
	}

	/**
	 * Get the label data.
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Prints the notification label.
	 *
	 * @return string
	 */
	public function print() : string
	{
		$label = Templates::make($this->source)->get($this->data('status'));

		if (is_null($label)) {
			return $this->defaultLabel();
		}

		return str_replace('source_id', $this->data('source_id'), $label);
	}

	/**
	 * Returns the value for the given key.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	protected function data(string $key)
	{
		if (empty($this->data[$key])) {
			throw new NotificationLabelsException("The [{$key}] has to be provided within the notification data.");
		}

		return $this->data[$key];
	}

	/**
	 * Returns the default notification label.
	 *
	 * @return string
	 */
	protected function defaultLabel() : string
	{
		return vsprintf('There is no template label for the given source [%s] and status code [%s].', [
			$this->source, $this->data('status')
		]);
	}
}
