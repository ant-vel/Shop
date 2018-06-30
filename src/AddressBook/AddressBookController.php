<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\AddressBook;

use Antvel\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressBookController extends Controller
{
    /**
     * Signed user's address book.
     *
     * @return void
     */
    public function index()
    {
        return view('address.list', [
            'addresses' => $addresses = Auth::user()->addresses,
            'defaultId' => $addresses->where('default', true)->pluck('id')->first(),
        ]);
    }

    /**
     * Show the address book creation form.
     *
     * @return void
     */
    public function create()
    {
        return view('address.form_create');
    }

    /**
     * Store a new address for the signed user.
     *
     * @return void
     */
    public function store(AddressBookRequest $request)
    {
        if (Auth::user()->newAddress($request->all())) {
            return $this->respondsWithSuccess(trans('address.success_save'), route('addressBook.index'));
        }

        return $this->respondsWithError(trans('address.errors.update'));
    }

    /**
     * Show the edition address form.
     *
     * @param int $id
     *
     * @return void
     */
    public function edit(int $id)
    {
        try {
            $address = Auth::user()->findAddress($id);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->respondsWithError(trans('address.errors.model_not_found'));
        }

        return view('address.form_edit', compact('address'));
    }

    /**
     * Update a given address.
     *
     * @param  AddressBookFormRequest $request
     * @param  int $id
     *
     * @return void
     */
    public function update(AddressBookRequest $request, int $id)
    {
        try {
            $address = Auth::user()->findAddress($id);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->respondsWithError(trans('address.errors.model_not_found'));
        }

        $address->update($request->all());

        return $this->respondsWithSuccess(trans('address.success_update'), route('addressBook.index'));
    }

    /**
     * Remove a given address.
     *
     * @param Request $request
     *
     * @return Void
     */
    public function destroy(int $id)
    {
        try {
            $address = Auth::user()->findAddress($id);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->respondsWithError(trans('address.errors.model_not_found'));
        }

        $address->delete();

        return $this->respondsWithSuccess('', route('addressBook.index'));
    }

    /**
     * Setting to default a given address.
     *
     * @param Request $request
     */
    public function setDefault(Request $request)
    {
        try {
            $address = Auth::user()->findAddress($request->id);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->respondsWithError(trans('address.errors.model_not_found'));
        }

        Auth::user()->resetDefaultAddress();

        $address->update(['default' => true]);

        return $this->respondsWithSuccess('', route('addressBook.index'));
    }
}
