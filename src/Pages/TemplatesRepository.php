<?php

namespace Whitecube\NovaPage\Pages;

class TemplatesRepository
{

    /**
     * The registered Templates
     *
     * @var array
     */
    protected $templates = [];

    /**
     * Get all registered templates
     *
     * @return void
     */
    public function all()
    {
        return $this->templates;
    }

    /**
     * Add a template
     *
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function register($template)
    {
        $template = new $template;

        $this->templates[get_class($template)] = $template;

        return $template;
    }

    /**
     * Get a specific template based on its route name
     *
     * @param string $route
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function find($route)
    {
        return $this->templates[$route] ?? null;
    }
    
}