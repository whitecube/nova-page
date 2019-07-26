<?php

namespace Whitecube\NovaPage\Pages\Concerns;

use Laravel\Nova\Http\Requests\NovaRequest;

trait ResolvesResourceCards
{
    /**
     * Get the cards that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableCards(NovaRequest $request)
    {
        return $this->resolveCards($request)->filter->authorize($request)->values();
    }

    /**
     * Get the cards for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveCards(NovaRequest $request)
    {
        $cards = [];
        foreach ($this->repository->getTemplates() as $template) {
            $cards = array_merge($cards, array_values($this->filter($template->cards($request))));
        }
        return collect($cards);
    }

}
