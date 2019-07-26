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
     * The key filter used to retrieve the resource
     *
     * @var null|string
     */
    protected $key;

    /**
     * The type filter used to retrieve the resource
     *
     * @var null|string
     */
    protected $type;

    /**
     * The locale filter used to retrieve the resource
     *
     * @var null|string
     */
    protected $locale;

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
     * @param string $key
     * @return self
     */
    public function whereKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Mimic eloquent's Builder and register a Where statement
     *
     * @param string $type
     * @return self
     */
    public function whereType($type)
    {
        $this->type = $type;
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
        $results = $this->get(false);

        if($results->count()) {
            return $results->first();
        }

        throw new TemplateNotFoundException(null, $this->key);        
    }

    /**
     * Mimic eloquent's Builder and execute the query
     *
     * @param bool $throwOnMissing
     * @return Illuminate\Support\Collection
     */
    public function get($throwOnMissing = false)
    {
        $resources = $this->repository->getFiltered(trim($this->type . '.*', '.'));

        return Collection::make($resources)
            ->map(function($template, $key) {
                return $this->repository->getResourceTemplate($key);
            })
            ->filter()
            ->reject([$this, 'shouldReject'])
            ->map(function($template, $key) use ($throwOnMissing) {
                list($type, $name) = explode('.', $key, 2);
                return $this->repository->load($type, $name, $this->locale, $throwOnMissing);
            });
    }

    /**
     * Checks if template should be included in results based
     * on the current Where clauses.
     *
     * @param Whitecube\NovaPage\Pages\Template $item
     * @param string $key
     * @return Illuminate\Support\Collection
     */
    public function shouldReject($item, $key) {
        if (!is_null($this->key)) {
            return $this->key !== $key;
        }
        return false;
    }
}
