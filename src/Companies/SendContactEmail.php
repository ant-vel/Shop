<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Companies;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Antvel\Companies\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendContactEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The default company.
     *
     * @var Company
     */
    protected $company = null;

    /**
     * The email information.
     *
     * @var Collection
     */
    public $data = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Company $company, array $data)
    {
        $this->company = $company;
        $this->data = Collection::make($data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(trans('company.contact.email_subject', ['name' => $this->data->get('name')]))
            ->view('emails.contact');
    }
}
