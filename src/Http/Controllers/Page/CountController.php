<?php

namespace Whitecube\NovaPage\Http\Controllers\Page;

use Illuminate\Routing\Controller;
use Whitecube\NovaPage\Pages\Manager;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class CountController extends Controller
{
    /**
     * Get the resource count for a given query.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceIndexRequest $request, Manager $manager)
    {
        return response()->json([
            'count' => $manager->queryResourcesCount($request, 'route')
        ]);
    }
}
