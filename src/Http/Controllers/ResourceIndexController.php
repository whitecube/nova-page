<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Illuminate\Routing\Controller;
use Whitecube\NovaPage\Pages\Manager;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class ResourceIndexController extends Controller
{
    /**
     * Get the queried resource's plural label
     * 
     * @return string
     */
    abstract protected function resourceLabel();

    /**
     * Get the queried resource's index items
     * 
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @param  \Whitecube\NovaPage\Pages\Manager $manager
     * @return \Illuminate\Support\Collection 
     */
    abstract protected function resourceIndexItems(ResourceIndexRequest $request, Manager $manager);

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
            'label' => $this->resourceLabel(),
            'resources' => $paginator->getCollection()->values()->map->serializeForIndex($request),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
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

        $items = $this->resourceIndexItems($request, $manager);

        $perPage = $request->perPage ?? 25;

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page);
    }

}
