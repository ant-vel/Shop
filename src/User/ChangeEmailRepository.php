<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User;

use Carbon\Carbon;
use Antvel\User\Models\EmailChangePetition;

class ChangeEmailRepository
{
    /**
     * Stores a new petition.
     *
     * @param  array  $data
     *
     * @return EmailChangePetition
     */
    public function store(array $data) : EmailChangePetition
    {
        $petition = $this->findBy([
            'new_email' => $data['request']['email'],
            'user_id' => $data['user_id'],
            'confirmed' => '0',
        ])->first();

        if (is_null($petition)) {
            return $this->create($data);
        }

        return $this->refresh($petition);
    }

    /**
     * Refreshes the expiration date for a given petition.
     *
     * @param  EmailChangePetition $petition
     *
     * @return EmailChangePetition
     */
    public function refresh(EmailChangePetition $petition) : EmailChangePetition
    {
        $petition->expires_at = Carbon::now()->addMonth();
        $petition->token = str_random(60);
        $petition->save();

        return $petition;
    }

    /**
     * Creates a new petition.
     *
     * @param  array $data
     *
     * @return EmailChangePetition
     */
    public function create(array $data) : EmailChangePetition
    {
        return EmailChangePetition::create([
            'expires_at' => Carbon::now()->addMonth(),
            'new_email' => $data['request']['email'],
            'old_email' => $data['old_email'],
            'user_id' => $data['user_id'],
            'token' => str_random(60),
        ]);
    }

    /**
     * Finds petitions by a given constraints.
     *
     * @param  array $constraints
     * @param  integer $take
     *
     * @return EmailChangePetition
     */
    public function findBy($constraints, $take = 1)
    {
        return EmailChangePetition::where($constraints)->take($take)->get();
    }

    /**
     * Confirms the user's new email address.
     *
     * @param  int $user_id
     * @param  string $token
     * @param  string $email
     *
     * @return EmailChangePetition
     */
    public function confirm($user_id, string $token, string $email)
    {
        $petition = $this->findBy([
            ['expires_at', '>=', Carbon::now()],
            'user_id' => $user_id,
            'new_email' => $email,
            'token' => $token,
            'confirmed' => 0,
        ])->first();

        if ($petition) {
            $petition->confirmed = 1;
            $petition->confirmed_at = Carbon::now();
            $petition->save();
        }

        return $petition;
    }
}
