<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Models\Concerns;

trait EmailPetitions
{
 /**
     * An user has many email change petitions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailChangePetitions()
    {
        return $this->hasMany('Antvel\Users\Models\EmailChangePetition');
    }

    /**
     * Make a new email change petition with the given data.
     *
     * @param  array $data
     *
     * @return self
     */
    public function makePetitionFor($new_email)
    {
        $petition = $this->emailChangePetitions()
            ->unconfirmedByEmail($new_email)
            ->first();

        if (! is_null($petition)) {
            $petition->refresh();
            return $petition->fresh();
        }

        return $this->emailChangePetitions()->create([
            'expires_at' => \Carbon\Carbon::now()->addMonth(),
            'old_email' => $this->email,
            'new_email' => $new_email,
            'token' => str_random(60),
        ]);
    }

    /**
     * Mark a found petition by the given token and email as confirmed.
     *
     * @param  string $token
     * @param  string $email
     *
     * @return void
     */
    public function confirmPetition(string $token, string $email)
    {
        $petition = $this->emailChangePetitions()
            ->unconfirmedByTokenAndEmail($token, $email)
            ->first();

        if (! is_null($petition)) {
            $petition->confirmed();
        }

        $this->email = $email;
        $this->save();
    }
}
