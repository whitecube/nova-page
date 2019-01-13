<?php

namespace Whitecube\NovaPage\Pages;

use Whitecube\NovaPage\Exceptions\TemplateNotFoundException;
use Illuminate\Support\Collection;

class Query
{

    /**
     * The registered NovaPage Templates.
     *
     * @var Whitecube\NovaPage\Pages\TemplatesRepository
     */
    protected $repository;

    /**
     * The name filter used to retrieve the resource
     *
     * @var null|string
     */
    protected $name;

    /**
     * Create a new NovaPage Resource QueryBuilder
     *
     * @return void
     */
    public function __construct(TemplatesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Mimic eloquent's Builder and register a Where statement
     *
     * @param string $name
     * @return self
     */
    public function whereKey($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Mimic eloquent's Builder and return corresponding Resource
     *
     * @return mixed
     * @throws TemplateNotFoundException
     */
    public function firstOrFail()
    {
        $results = $this->get();

        if($results->count()) {
            return $results->first();
        }

        throw new TemplateNotFoundException(null, $this->name);        
    }

    /**
     * Mimic eloquent's Builder and execute the query
     *
     * @return Illuminate\Support\Collection
     */
    public function get()
    {
        return Collection::make($this->repository->all())
            ->reject([$this, 'shouldReject']);
    }

    /**
     * Checks if template should be included in results based
     * on the current Where clauses.
     *
     * @param Whitecube\NovaPage\Pages\Template $item
     * @param string $name
     * @return Illuminate\Support\Collection
     */
    public function shouldReject($item, $name) {
        return is_null($this->name) ? false : ($this->name !== $name);
    }
}