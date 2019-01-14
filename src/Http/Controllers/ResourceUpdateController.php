<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class ResourceUpdateController extends Controller
{
    /**
     * Update a resource.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(UpdateResourceRequest $request)
    {
        $request->resource = 'nova-page';
        $request->findResourceOrFail()->authorizeToUpdate($request);

        $resource = $request->resource();

        $resource::validateForUpdate($request);

        $template = $request->findModelQuery()->firstOrFail();

        if ($this->templateHasBeenUpdatedSinceRetrieval($request, $template)) {
            return response('', 409);
        }
        
        [$template, $callbacks] = $resource::fillForUpdate($request, $template);

        tap($template)->save();
        collect($callbacks)->each->__invoke();

        return response('');
    }

    /**
     * Determine if the resource has been updated since it was retrieved.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @param  \Whitecube\NovaPage\Pages\Template  $template
     * @return void
     */
    protected function templateHasBeenUpdatedSinceRetrieval(UpdateResourceRequest $request, $template)
    {
        $date = $template->getDate('updated_at');
        return $request->input('_retrieved_at') && $date && $date->gt(
            Carbon::createFromTimestamp($request->input('_retrieved_at'))
        );
    }
}
