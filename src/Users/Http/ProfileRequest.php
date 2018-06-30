<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Http;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * The allowed form references.
     *
     * @var array
     */
    protected $allowedReferral = ['profile', 'social', 'account'];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return Auth::check() && Auth::user()->is($this->user()) && $this->isAllowed();
    }

    /**
     * Checks whether the referral form is allowed to make the incoming request.
     *
     * @return bool
     */
    protected function isAllowed() : bool
    {
        return $this->request->has('referral')
            && in_array($this->request->get('referral'), $this->allowedReferral);
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
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'email' => [
                Rule::unique('users')->ignore(Auth::user()->id),
                'required',
                'email',
            ],
            'nickname' => [
                Rule::unique('users')->ignore(Auth::user()->nickname, 'nickname'),
                'required',
                'max:20',
            ],
            'pictures.storing' => [
                Rule::dimensions()->maxWidth(500)->maxHeight(500),
                'mimes:jpeg,png,jpg',
                'image',
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
            'facebook' => 'url',
            'twitter' => 'url',
            'website' => 'url',
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
            'password_confirmation' => 'required_with:old_password,password|different:old_password|same:password',
            'password' => 'required_with:old_password,password_confirmation|confirmed|different:old_password',
            'old_password'  => 'required_with:password,password_confirmation',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'pictures.storing.*' => trans('user.validation_errors.avatar')
        ];
    }
}
