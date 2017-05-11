<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User\Requests;

use Antvel\Http\Request;
use Illuminate\Validation\Rule;
use Antvel\User\UsersRepository;

class ProfileRequest extends Request
{
    /**
     * The user repository.
     *
     * @var UsersRepository
     */
    protected $user = null;

    /**
     * The allowed form references.
     *
     * @var array
     */
    protected $allowedReferral = ['profile', 'social', 'account', 'upload'];

    /**
     * Creates a new instance.
     *
     * @param UsersRepository $user
     *
     * @return void
     */
    public function __construct(UsersRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return $this->user->isLoggedIn() && $this->isAllowed();
    }

    /**
     * Checks whether the referral form is allowed to make the incoming request.
     *
     * @return bool
     */
    protected function isAllowed() : bool
    {
        return in_array($this->request->get('referral'), $this->allowedReferral);
    }

    /**
     * Resolves the validation rules for a given referral form.
     *
     * @return array
     */
    public function rules() : array
    {
        $referral = mb_strtolower($this->request->get('referral') ?? 'profile');

        $resolver = 'rulesFor' . ucfirst($referral);

        return $this->$resolver();
    }

    /**
     * Returns validation rules for the form profile.
     *
     * @return array
     */
    protected function rulesForProfile() : array
    {
      return [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'nickname' => [
                'required',
                'max:255',
                Rule::unique('users')->ignore($this->user->nickname, 'nickname'),
            ],
        ];
    }

    /**
     * Returns validation rules for the form social information.
     *
     * @return array
     */
    protected function rulesForSocial() : array
    {
        return [
            //
        ];
    }

    /**
     * Returns validation rules for the form account.
     *
     * @return array
     */
    protected function rulesForAccount() : array
    {
        return [
            'old_password'  => 'required_with:password,password_confirmation',
            'password'  => 'required_with:old_password,password_confirmation|confirmed|different:old_password',
            'password_confirmation' => 'required_with:old_password,password|different:old_password|same:password',
        ];
    }

    /**
     * Returns validation rules for the form profile.
     *
     * @return array
     */
    protected function rulesForUpload() : array
    {
        return [
            'file' => [
                'required',
                'image',
                Rule::dimensions()->maxWidth(600)->maxHeight(600),
            ],
        ];
    }
}
