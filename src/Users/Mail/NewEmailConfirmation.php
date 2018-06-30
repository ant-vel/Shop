<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Antvel\Users\Models\EmailChangePetition;

class NewEmailConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The change email petition.
     *
     * @var EmailChangePetition
     */
    public $petition = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailChangePetition $petition)
    {
        $this->petition = $petition;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subjectKey = 'user.emails.email_confirmation.subject';

        return $this->subject($this->getSubject($subjectKey))
            ->to($this->petition->new_email)
            ->view('emails.newEmailConfirmation', [
                'name' => Auth::user()->fullName,
                'route' => $this->route(),
        ]);
    }

    /**
     * Returns the confirmation email subject.
     *
     * @param  string $key
     *
     * @return string
     */
    public function getSubject($key)
    {
        if (Lang::has($key)) {
            return Lang::get('user.emails.email_confirmation.subject');
        }

        return 'Please confirm your new email address';
    }

    /**
     * Returns the confirmation url.
     *
     * @return string
     */
    protected function route()
    {
        return route('user.email', [
            'token' => $this->petition->token,
            'email' => $this->petition->new_email
        ]);
    }
}
