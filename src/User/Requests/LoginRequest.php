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
use Illuminate\Container\Container;

class LoginRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        if (env('APP_ENV') === 'production') {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
        }

        return $rules;
    }
}
