<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Listeners;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Config\Repository as ConfigRepository;

class UpdateNotificationType
{
    /**
     * The authenticated user provider.
     *
     * @var string
     */
    protected $userModelProvider = '';

    /**
     * Creates a new instance.
     *
     * @param ConfigRepository $config
     *
     * @return void
     */
    public function __construct(ConfigRepository $config)
    {
        $this->userModelProvider = $this->normalizeProvider($config);
    }

    /**
     * Normalize the authentication provider class name.
     *
     * @param  ConfigRepository $config
     *
     * @return string
     */
    protected function normalizeProvider(ConfigRepository $config) : string
    {
        $default = $config->get('auth.providers.users.model');

        if (is_null($default) || trim($default) == '') {
            return \Antvel\Users\Models\User::class;
        }

        return $default;
    }

    /**
     * Handle the event.
     *
     * @param  NotificationSent  $event
     *
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        if (! is_null($event->response) && $event->response->notifiable_type !== $this->userModelProvider) {
            $event->response->notifiable_type = $this->userModelProvider;
            $event->response->save();
        }
    }
}
