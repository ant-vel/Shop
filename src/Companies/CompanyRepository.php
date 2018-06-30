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

use Antvel\Http\Controller;
use Antvel\Companies\Models\Company;

class CompanyRepository
{
	/**
	 * Returns the default company.
	 *
	 * @return Company
	 */
	public function default() : Company
	{
		$company = Company::where(['status' => true, 'default' => true])->first();

		if (is_null($company)) {
			return $this->fake();
		}

		return $company;
	}

	/**
	 * Returns a testing company.
	 *
	 * @return Company
	 */
	public function fake() : Company
	{
		return Company::make([
	        'name' => 'Antvel e-commerce',
	        'description' => 'Laravel e-commerce solution.',
	        'email' => 'gocanto@antvel.com',
	        'logo' => 'antvel.jpg',
	        'slogan' => 'Antvel e-commerce.',
	        'contact_email' => 'contact@antvel.com',
	        'sales_email' => 'sales@antvel.com',
	        'support_email' => 'support@antvel.com',
	        'phone_number' => '+14056696453',
	        'cell_phone' => '+14056696453',
	        'address' => '4576 SE 44',
	        'state' => 'OK',
	        'city' => 'Norman',
	        'zip_code' => '79002',
	        'website' => 'http://antvel.com',
	        'twitter' => 'https://twitter.com/_antvel',
	        'facebook' => 'https://www.facebook.com/antvelecommerce',
	        'keywords' => 'antvel',
	        'about' => 'Laravel e-commerce solution.',
	        'terms' => 'Antvel e-commerce terms & conditions.',
	        'refunds' => 'Antvel e-commerce refunds policies',
		]);
	}
}
