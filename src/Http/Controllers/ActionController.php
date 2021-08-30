<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Laravel\Nova\Http\Controllers\ActionController as Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActionController extends Controller
{
    /**
     * List the actions for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(NovaRequest $request)
    {
        return response()->json([
            'actions' => [],
            'pivotActions' => [
                'name' => $request->pivotName(),
                'actions' => [],
            ],
        ]);
    }
}
