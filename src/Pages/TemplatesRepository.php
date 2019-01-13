<?php

namespace Whitecube\NovaPage\Pages;

use Route;
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
     * The registered pages
     *
     * @var array
     */
    protected $pages = [];

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
     * Get all registered pages
     *
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Get a registered page template by its key
     *
     * @param string $name
     * @return null|Whitecube\NovaPage\Pages\Template
     */
    public function getPageTemplate($name)
    {
        if(array_key_exists($name, $this->pages)) {
            return $this->templates[$this->pages[$name]];
        }
    }

    /**
     * Load a new Page Template Instance
     *
     * @param string $type
     * @param string $key
     * @param string $locale
     * @param bool $throwOnMissing
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function load($type, $key, $locale, $throwOnMissing)
    {
        $name = $type . '.' . $key;

        if(!($template = $this->getPageTemplate($name))) {
            throw new TemplateNotFoundException($this->pages[$name] ?? null, $name);
        }

        if(!isset($this->loaded[$name])) {
            $this->loaded[$name] = $template->getNewTemplate($type, $key, $locale, $throwOnMissing);
        }
        else {
            $this->loaded[$name]->setLocale($locale)->load($throwOnMissing);
        }

        return $this->loaded[$name];
    }

    /**
     * Get a loaded page template by its key
     *
     * @param string $type
     * @param string $key
     * @return null|Whitecube\NovaPage\Pages\Template
     */
    public function getLoaded($type, $key)
    {
        foreach ($this->loaded as $identifier => $template) {
            if($identifier === $type . '.' . $key) return $template;
        }
    }

    /**
     * Add a page template
     *
     * @param string $type
     * @param string $key
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function register($type, $key, $template)
    {
        if(!array_key_exists($template, $this->templates)) {
            $this->templates[$template] = new $template;
        }

        $this->pages[$type . '.' . $key] = $template;

        return $this->templates[$template];
    }
    
}