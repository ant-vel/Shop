<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Registration extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The email view template.
     *
     * @var string
     */
    protected $view = 'emails.accountVerification';

    /**
     * The email subject.
     *
     * @var string
     */
    protected $subject = 'Account Confirmation';

    /**
     * Create a new notification instance.
     *
     * @param array $sections
     *
     * @return void
     */
    public function __construct(array $sections = [])
    {
        if (count($sections) > 0) {
            $this->parse($sections);
        }
    }

    /**
     * Parses the email information.
     *
     * @param  array $sections
     */
    protected function parse(array $sections = [])
    {
        if (isset($sections['subject']) && ! is_null($sections['subject'])) {
            $this->subject = $sections['subject'];
        }

        if (isset($sections['view']) && ! is_null($sections['view'])) {
            $this->view = $sections['view'];
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view($this->view, [
                'name' => $notifiable->profile->fullName,
                'route' => $this->route($notifiable),
            ]);
    }

    /**
     * Returns the confirmation url.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return string
     */
    protected function route($user) : string
    {
        return route('register.confirm', [
            'token' => $user->confirmation_token,
            'email' => $user->email
        ]);
    }
}
