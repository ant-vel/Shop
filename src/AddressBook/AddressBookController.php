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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddressBookController extends Controller
{
    /**
     * The addressBook book componet.
     *
     * @var Antvel\Components\AddressBook\Repository
     */
    protected $addressBook = null;

    /**
     * The intended view.
     *
     * @var string
     */
    protected $redirecTo = '/addressBook';

    /**
     * Create a new Invitations instance.
     *
     * @param AddressBook $addressBook
     *
     * @return  void
     */
    public function __construct(AddressBook $addressBook)
    {
        $this->addressBook = $addressBook;
    }

    /**
     * Setting to default a given address.
     *
     * @param Request $request
     */
    public function setDefault(Request $request)
    {
        $this->addressBook->setDefault((int) $request->id);

        return $this->respondsWithSuccess(
            '', $this->redirecTo
        );
    }

    /**
     * List the user address.
     *
     * @return void
     */
    public function index(Request $request)
    {
        return view('address.list', [
            'addresses' => $this->addressBook->forUser()
        ]);
    }

    /**
     * Show the address creation form.
     *
     * @return void
     */
    public function create()
    {
        return view('address.form_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(AddressBookRequest $request)
    {
        $address = $this->addressBook->createAndSetToDefault(
            $request->all()
        );

        if (! $address) {
            return $this->respondsWithError(
                trans('address.errors.update')
            );
        }

        return $this->respondsWithSuccess(
            trans('address.success_save'), $this->redirecTo
        );
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
        return view('address.form_edit', [
            'address' => $this->addressBook->find($id)
        ]);
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
            ! $address = $this->addressBook->find($id);
        } catch(ModelNotFoundException $e) {
            return $this->respondsWithError(trans('address.errors.model_not_found'));
        }

        $address->update($request->all());

        return $this->respondsWithSuccess(
            trans('address.success_update'), $this->redirecTo
        );
    }

    /**
     * Remove a given address.
     *
     * @param Request $request
     *
     * @return Void
     */
    public function destroy(Request $request)
    {
        $this->addressBook->destroy($request->id);

        return $this->respondsWithSuccess(
            '', $this->redirecTo
        );
    }
}
