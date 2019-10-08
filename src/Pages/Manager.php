<?php

namespace Whitecube\NovaPage\Pages;

use Illuminate\Routing\Route;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class Manager
{
    use ConditionallyLoadsAttributes,
        Concerns\QueriesResources,
        Concerns\ResolvesResourceCards;

    /**
     * The registered NovaPage Templates & Pages.
     *
     * @var Whitecube\NovaPage\Pages\TemplatesRepository
     */
    protected $repository;

    /**
     * The default current Page Template
     *
     * @var Whitecube\NovaPage\Pages\Template
     */
    protected $current;

    /**
     * Create the Main Service Singleton
     *
     * @return void
     */
    public function __construct(TemplatesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Load the TemplateRepository with registered routes
     *
     * @return void
     */
    public function booted()
    {
        $this->repository->registerRouteTemplates();
    }

    /**
     * Register a Template into the TemplatesRepository
     * and to the Laravel service container as well.
     *
     * @param string $type
     * @param string $name
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function register($type, $name, $template)
    {
        app()->bind($template, function () use ($name) {
            return $this->option($name);
        });

        return $this->repository->register($type, $name, $template);
    }

    /**
     * Register an option Template into the TemplatesRepository
     * and to the Laravel service container as well.
     *
     * @param string $type
     * @param string $name
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function registerOption($name, $template)
    {
        return $this->register('option', $name, $template);
    }

    /**
     * Load a new Page Template
     *
     * @param string $name
     * @param string $type
     * @param bool $current
     * @param bool $throwOnMissing
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function load($name, $type = 'route', $current = true, $throwOnMissing = false)
    {
        $template = $this->repository->load($type, $name, $throwOnMissing);

        if ($type == 'route' && $current) {
            $this->current = $template;
        }

        return $template;
    }

    /**
     * Load Page Template for given route instance
     *
     * @param Illuminate\Routing\Route $route
     * @param bool $current
     * @param bool $throwOnMissing
     * @return void
     */
    public function loadForRoute(Route $route, $current = true, $throwOnMissing = false)
    {
        if(!$route->template()) return;
        
        $this->load($route->getName(), 'route', $current, $throwOnMissing);
    }

    /**
     * Get a loaded Template by its name
     *
     * @param string $name
     * @param string $type
     * @return null|Whitecube\NovaPage\Pages\Template
     */
    public function find($name = null, $type = 'route')
    {
        if(is_null($name)) {
            return $this->current;
        }

        return $this->repository->getLoaded($type, $name);
    }

    /**
     * Get an option template by its name
     * 
     * @param string $name
     * @param bool $throwOnMissing
     * @return mixed
     */
    public function option($name, $throwOnMissing = false)
    {
        return  $this->find($name, 'option') ??
                $this->load($name, 'option', false, $throwOnMissing);
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

    /**
     * Mimic eloquent model method and return a fake Query builder
     *
     * @return Whitecube\NovaPage\Pages\Query
     */
    public function newQueryWithoutScopes()
    {
        return new Query($this->getRepository());
    }

    /**
     * Get the underlying template repository
     * @return TemplatesRepository|Whitecube\NovaPage\Pages\TemplatesRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
}