<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Illuminate\Routing\Controller;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\OptionResource;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class OptionResourceCountController extends Controller
{
    /**
     * Get the resource count for a given query.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceIndexRequest $request, Manager $manager)
    {
        return response()->json(['count' => $manager->queryOptionsCount($request)]);
    }
}
