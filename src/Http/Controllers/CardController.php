<?php

namespace Whitecube\NovaPage\Http\Controllers;

use Illuminate\Routing\Controller;
use Whitecube\NovaPage\Pages\Manager;
use Laravel\Nova\Http\Requests\NovaRequest;

class CardController extends Controller
{

    /**
     * List the cards for the given resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Manager $manager, NovaRequest $request)
    {
        return $manager->availableCards($request);
    }

}
