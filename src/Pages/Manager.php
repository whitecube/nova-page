<?php

namespace Whitecube\NovaPage\Pages;

use Illuminate\Routing\Route;
use Whitecube\NovaPage\Sources\SourceInterface;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class Manager
{

    use ConditionallyLoadsAttributes,
        QueriesPages,
        ResolvesPageCards;

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
     * Load a new Page Template
     *
     * @param string $key
     * @param string $type
     * @param bool $current
     * @param bool $throwOnMissing
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function load($key, $type = null, $current = true, $throwOnMissing = true)
    {
        $template = $this->repository->load($type ?? 'route', $key, $throwOnMissing);

        if($current) {
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
     * @return mixed
     */
    public function loadForRoute(Route $route, $current = true, $throwOnMissing = true)
    {
        if(!$route->template()) {
            return;
        }

        return $this->load($route->getName(), 'route', $current, $throwOnMissing);
    }

    /**
     * Get a loaded Template by its name
     *
     * @param string $key
     * @param string $type
     * @return null|Whitecube\NovaPage\Pages\Template
     */
    public function find($key = null, $type = null)
    {
        if(is_null($key)) {
            return $this->current;
        }

        return $this->repository->getLoaded($type ?? 'route', $key);
    }

    /**
     * Register a Template into the TemplatesRepository.
     *
     * @param string $type
     * @param string $key
     * @param string $template
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function register($type, $key, $template)
    {
        return $this->repository->register($type, $key, $template);
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
        return new Query($this->repository);
    }

    public function getRepository()
    {
        return $this->repository;
    }
    
}