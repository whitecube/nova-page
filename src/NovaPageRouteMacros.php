<?php

namespace Whitecube\NovaPage;

use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Template;

class NovaPageRouteMacros
{

    /**
     * Get or set the NovaPage template attached to the route.
     *
     * @param  string|null $template
     * @return $this|string|null
     */
    public function template()
    {
        return function($template = null) {
            if (is_null($template)) {
                return $this->action['nova-page-template'] ?? null;
            }

            $this->action['nova-page-template'] = $template;

            return $this;
        };
    }

}