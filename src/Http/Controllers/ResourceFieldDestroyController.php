<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

abstract class ResourceFieldDestroyController extends Controller
{
    /**
     * The queried resource's name
     *
     * @var string
     */
    protected $resourceName;

    /**
     * Update a resource.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(UpdateResourceRequest $request)
    {
        $route = call_user_func($request->getRouteResolver());
        $route->setParameter('resource', $this->resourceName);
        $request->findResourceOrFail()->authorizeToUpdate($request);

        $resource = $request->newResource();
        $field = $resource->updateFields($request)->findFieldByAttribute($request->field);

        if (!($field instanceof File)) {
            abort(404);
        }

        $template = $request->findModelQuery()->firstOrFail();

        if (!$template->visible() || (!office()->isTemplate() && !$template->overridable())) {
            // This template can't be overriden.
            return response('', 403);
        }

        DeleteField::forRequest(
            $request,
            $field,
            $template
        )->save();
    }
}
