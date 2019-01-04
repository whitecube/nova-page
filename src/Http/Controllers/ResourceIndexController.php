<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Illuminate\Routing\Controller;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Resource;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class ResourceIndexController extends Controller
{
    /**
     * List the resources for administration.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(ResourceIndexRequest $request, Manager $manager)
    {
        $collection = $manager->queryIndex($request);

        return response()->json([
            'label' => config('novapage.label'),
            'resources' => $collection->values(),
            'prev_page_url' => null,
            'next_page_url' => null,
            'softDeletes' => null,
        ]);

        \Log::debug($response);

        return $response;
    }
}
