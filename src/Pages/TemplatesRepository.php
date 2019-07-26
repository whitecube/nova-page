<?php

namespace Whitecube\NovaPage\Pages;

use Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Whitecube\NovaPage\Exceptions\TemplateNotFoundException;

class TemplatesRepository
{

    /**
     * The registered Templates
     *
     * @var array
     */
    protected $templates = [];

    /**
     * The registered pages & options
     *
     * @var array
     */
    protected $resources = [];

    /**
     * The loaded page templates
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * Fill the repository with registered routes
     *
     * @return void
     */
    public function registerRouteTemplates()
    {
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if(!$route->template()) continue;
            $this->register('route', $route->getName(), $route->template());
        }
    }

    /**
     * Get all registered templates
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Get all registered pages matching given filter
     * 
     * @param string $filter
     * @return array
     */
    public function getFiltered($filter = '*') {
        return Arr::where($this->resources, function($resource, $key) use ($filter) {
            return Str::is($filter, $key);
        });
    }

    /**
     * Get a registered page or option template by its key
     *
     * @param string $key
     * @return null|Whitecube\NovaPage\Pages\Template
     */
    public function getResourceTemplate($key)
    {
        if(array_key_exists($key, $this->resources)) {
            return $this->templates[$this->resources[$key]];
        }
    }

    /**
     * Load a new Template Instance
     *
     * @param string $type
     * @param string $name
     * @param bool $throwOnMissing
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function load($type, $name, $throwOnMissing)
    {
        $key = $this->getKey($type, $name);

        if(!($template = $this->getResourceTemplate($key))) {
            throw new TemplateNotFoundException($this->resources[$key] ?? null, $key);
        }

        if(!isset($this->loaded[$key])) {
            $this->loaded[$key] = $template->getNewTemplate($type, $key, $name, $throwOnMissing);
        } else {
            $this->loaded[$key]->load($throwOnMissing);
        }

        return $this->loaded[$key];
    }

    /**
     * Get a loaded page template by its key
     *
     * @param string $type
     * @param string $name
     * @return null|Whitecube\NovaPage\Pages\Template
     */
    public function getLoaded($type, $name)
    {
        $key = $this->getKey($type, $name);

        if(array_key_exists($key, $this->loaded)) {
            return $this->loaded[$key];
        }
    }

    /**
     * Add a page template
     *
     * @param string $type
     * @param string $name
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function register($type, $name, $template)
    {
        if(!array_key_exists($template, $this->templates)) {
            $this->templates[$template] = new $template;
        }

        $this->resources[$this->getKey($type, $name)] = $template;

        return $this->templates[$template];
    }

    /**
     * Get a resource identifier key
     *
     * @param string $type
     * @param string $name
     * @return string
     */
    public function getKey($type, $name)
    {
        return $type . '.' . $name;
    }
    
}