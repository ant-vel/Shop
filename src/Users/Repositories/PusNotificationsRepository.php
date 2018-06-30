<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PusNotificationsRepository
{
    /**
     * Shows the unread user notifications formatted.
     *
     * @param int $limit
     * @return array
     */
    public function unread($limit = 5)
    {
        $unread = Auth::user()->unreadNotifications->take($limit);

        return $unread->map(function ($item) {
            return $this->toCollect($item);
        });
    }

    /**
     * Shows the read user notifications formatted.
     *
     * @param int $limit
     * @return array
     */
    public function read($limit = 5)
    {
        $read = Auth::user()->notifications()->whereNotNull('read_at')->take($limit)->get();

        return $read->map(function ($item) {
            return $this->toCollect($item);
        });
    }

    /**
     * Shows the read user notifications formatted.
     *
     * @param int $limit
     * @return array
     */
    public function all($limit = 5)
    {
        $all = Auth::user()->notifications->take($limit);

        return $all->map(function ($item) {
            return $this->toCollect($item);
        });
    }

    /**
     * Formats the given notification to the desired array.
     *
     * @param  mixed $notification
     *
     * @return array
     */
    protected function toCollect($notification) : Collection
    {
        return Collection::make([
            'hasBeenRead' => ! is_null($notification->read_at),
            'path' => $notification->data['source_path'],
            'label' => $notification->data['label'],
            'id' => $notification->id,
        ]);
    }
}
