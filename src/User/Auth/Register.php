<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Auth;

use Antvel\User\Models\User;
use Antvel\User\Mail\Registration;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Session\Store as Session;
use Antvel\User\Requests\RegisterRequest;

class Register
{
    /**
     * The default registration role.
     *
     * @var string
     */
    protected $role = 'person';

    /**
     * The registered user.
     *
     * @var Antvel\User
     */
    protected $user = null;

    /**
     * The Laravel session component.
     *
     * @var Session
     */
    protected $session = null;

    /**
     * The Laravel mail component.
     *
     * @var Mailer
     */
    protected $mailer = null;

    /**
     * The response output.
     *
     * @var string
     */
    protected $response = 'notOk';

    /**
     * Creates a new instance.
     *
     * @param Mailer $mailer
     * @return void
     */
    public function __construct(Mailer $mailer, Session $session)
    {
        $this->mailer = $mailer;
        $this->session = $session;
    }

    /**
     * Registers a new user in the database.
     *
     * @param  RegisterRequest $request
     * @return self
     */
    public function store(RegisterRequest $request): self
    {
        //we create the user with the given request.
        $this->user = User::create([
            'password' => bcrypt($request->get('password')),
            'confirmation_token' => str_random(60),
            'nickname' => $request->get('email'),
            'email' => $request->get('email'),
            'role' => $this->role,
        ]);

        //we fill the given user profile information.
        $this->user->profile()->create([
            'first_name' => $request->get('first_name'),
            'last_name'  => $request->get('last_name'),
        ]);

        return $this;
    }

    /**
     * Flashes the registration success message.
     *
     * @param  array  $message
     * @return self
     */
    public function withMessage(string $message = 'Process was successfully completed.') : self
    {
        $this->session->flash('message', $message);

        return $this;
    }

    /**
     * Send the registration email.
     *
     * @param  array  $template
     * @return self
     */
    public function withEmail(array $template = []) : self
    {
        $this->mailer->send(
            new Registration($this->user, $template)
        );

        return $this;
    }

    /**
     * Confirms a registered user.
     *
     * @param  string $token
     * @param  string $email
     * @return void
     */
    public function confirm(string $token, string $email)
    {
        $verifier = Confirmation::make($token, $email);

        if ($verifier->passes()) {
            $verifier->activateUser();
            return $this->setResponse('ok');
        }

        return $this;
    }

    /**
     * Flash a confirmation error message.
     *
     * @param  string $message
     * @return self
     */
    public function flashError(string $message) : self
    {
        $this->withMessage($message);

        return $this;
    }

    /**
     * Sets the response output.
     *
     * @param string $response
     */
    public function setResponse(string $response) : self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Returns the response output.
     *
     * @return string
     */
    public function response() : string
    {
        return $this->response;
    }
}
