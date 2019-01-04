<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Illuminate\Routing\Controller;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Resource;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceIndexController extends Controller
{
    /**
     * List the resources for administration.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @param  \Whitecube\NovaPage\Pages\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function handle(ResourceIndexRequest $request, Manager $manager)
    {
        $paginator = $this->paginator($request, $manager);

        return response()->json([
            'label' => config('novapage.label'),
            'resources' => $paginator->getCollection()->values(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'softDeletes' => null,
        ]);
    }

    /**
     * Get the paginator instance for the index request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @param  \Whitecube\NovaPage\Pages\Manager  $manager
     * @return \Illuminate\Pagination\Paginator
     */
    protected function paginator(ResourceIndexRequest $request, Manager $manager)
    {
        $page = Paginator::resolveCurrentPage() ?: 1;

        $items = $manager->queryIndex($request);

        $perPage = $request->perPage ?? 25;

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page);
    }

}
