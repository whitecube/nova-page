<?php

namespace Whitecube\NovaPage\Pages;

use Illuminate\Routing\Route;
use Whitecube\NovaPage\Exceptions\TemplateNotFoundException;
use Whitecube\NovaPage\Sources\SourceInterface;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class Manager
{

    use ConditionallyLoadsAttributes,
        QueriesPages,
        ResolvesPageCards;

    /**
     * The registered NovaPage Templates.
     *
     * @var Whitecube\NovaPage\Pages\TemplatesRepository
     */
    protected $templates;

    /**
     * The default current page Template
     *
     * @var Whitecube\NovaPage\Pages\Template
     */
    protected $current;

    /**
     * The loaded Templates
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * Create the Main Service Singleton
     *
     * @return void
     */
    public function __construct()
    {
        $this->templates = new TemplatesRepository();
    }

    /**
     * Load a new Page Template
     *
     * @param string $name
     * @param string $template
     * @param string $locale
     * @param bool $current
     * @param bool $throwOnMissing
     * @return Whitecube\NovaPage\Pages\Template
     * @throws TemplateNotFoundException
     */
    public function load($name, $template, $locale = null, $current = true, $throwOnMissing = true)
    {
        if(!($template = $this->templates->find($template))) {
            throw new TemplateNotFoundException($template, $name);
        }

        $key = $template->getSource()->getName() . '.' . $name;

        if(!isset($this->loaded[$key])) {
            $this->loaded[$key] = $template->getNewTemplate($name, $locale, $throwOnMissing);
        }
        else {
            $this->loaded[$key]->setLocale($locale)->load($throwOnMissing);
        }

        if($current) {
            $this->current = $this->loaded[$key];
        }

        return $this->loaded[$key];
    }

    /**
     * Load Page Template for given route instance
     *
     * @param Illuminate\Routing\Route $route
     * @param string $locale
     * @param bool $current
     * @param bool $throwOnMissing
     * @return mixed
     */
    public function loadForRoute(Route $route, $locale = null, $current = true, $throwOnMissing = true)
    {
        if(!$route->template()) {
            return;
        }

        return $this->load($route->getName(), $route->template(), $locale, $current, $throwOnMissing);
    }

    /**
     * Get a loaded Template by its name
     *
     * @param string $name
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function find($name = null)
    {
        if(is_null($name)) {
            return $this->current;
        }

        foreach ($this->loaded as $key => $template) {
            if($key === $name) return $template;
            if(substr($key, strpos($key, '.') + 1) === $name) return $template;
        }
    }

    /**
     * Register a Template into the TemplatesRepository.
     *
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function register($template)
    {
        return $this->templates->register($template);
    }

    /**
     * Get an attribute on the current Template
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if(!$this->current) {
            return;
        }

        return $this->current->$attribute;
    }

    /**
     * Forward a method call to the current Template
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if(!$this->current) {
            return;
        }

        return call_user_func_array([$this->current, $method], $arguments);
    }
    
}