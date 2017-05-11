<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class ProfileWasUpdated
{
    use SerializesModels;

    /**
     * The laravel request component.
     *
     * @var array
     */
    public $request = null;

    /**
     * The antvel user component.
     *
     * @var Authenticatable
     */
    public $user = null;

    /**
     * The change email petition.
     *
     * @var \Antvel\User\Models\EmailChangePetition
     */
    public $petition = null;

    /**
     * Create a new event instance.
     *
     * @param array $request
     * @param Authenticatable $user
     *
     * @return void
     */
    public function __construct(array $request, Authenticatable $user)
    {
        $this->request = $request;
        $this->user = $user;
    }
}
