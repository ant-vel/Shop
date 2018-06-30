<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Features;

use Antvel\Http\Controller;
use Antvel\Features\Models\Feature;
use Antvel\Features\Requests\FeaturesRequest;
use Antvel\Features\Events\FeatureNameWasUpdated;

class FeaturesController extends Controller
{
	/**
     * Shows features list.
     *
     * @return void
     */
	public function index()
	{
        return view('dashboard.sections.features.index', [
            'features' => Feature::paginate(50)
        ]);
	}

	/**
     * Creates a new feature.
     *
     * @return void
     */
    public function create()
    {
    	return view('dashboard.sections.features.create', [
            'allowed_rules' => ValidationRulesParser::allowed(),
            'validation_rules' => collect(),
        ]);
    }

     /**
     * Stores a new feature.
     *
     * @param  FeaturesRequest $request
     *
     * @return void
     */
    public function store(FeaturesRequest $request)
    {
        $feature = Feature::create($request->all());

        return redirect()->route('features.edit', $feature)->with('status', trans('globals.success_text'));
    }

    /**
     * Edits a given category.
     *
     * @param  Feature $feature
     *
     * @return void
     */
    public function edit(Feature $feature)
    {
        return view('dashboard.sections.features.edit', [
            'validation_rules' => ValidationRulesParser::decode($feature->validation_rules)->all(),
            'allowed_rules' => ValidationRulesParser::allowed(),
            'feature' => $feature,
        ]);
    }

    /**
     * Updates the given feature.
     *
     * @param  FeaturesRequest $request
     * @param  Feature $feature
     *
     * @return void
     */
    public function update(FeaturesRequest $request, Feature $feature)
    {
        if ($request->has('name') && $feature->name != $request->get('name')) {
            event(new FeatureNameWasUpdated($feature, $request->get('name')));
        }

        $feature->update(
            $request->except('name')
        );

        return redirect()->route('features.edit', $feature)->with('status', trans('globals.success_text'));
    }
}
