<?php

namespace Whitecube\NovaPage\Page;

use Whitecube\NovaPage\Exceptions\ContainerNotFoundException;
use Whitecube\NovaPage\Sources\SourceInterface;
use Whitecube\NovaPage\Sources\Filesystem;

class Manager
{

    /**
     * NovaPage Configuration
     *
     * @var array
     */
    protected $config;

    /**
     * The registered container's data sources. First one is default.
     *
     * @var array
     */
    protected $sources;

    /**
     * The default current page container
     *
     * @var Whitecube\NovaPage\Page\Container
     */
    protected $current;

    /**
     * The loaded containers
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * Set default manager source and other configuration
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->getSource($config['default_source'] ?? Filesystem::class);
    }

    /**
     * Load a new Page Container
     *
     * @param string $identifier
     * @param bool $current
     * @param string $source
     * @return Whitecube\NovaPage\Page\Container
     */
    public function load($identifier, $current = true, $source = null)
    {
        $source = $this->getSource($source);

        if(isset($this->loaded[$source->getName() . '.' . $identifier])) {
            return $this->loaded[$source->getName() . '.' . $identifier];
        }

        if(!($raw = $source->fetch($identifier))) {
            throw new ContainerNotFoundException($source, $identifier);
        }

        $container = $this->getNewContainer($identifier, $raw, $source);

        $this->loaded[$source->getName() . '.' . $identifier] = $container;
        if($current) $this->current = $container;

        return $container;
    }

    /**
     * Return an instance of the requested (or default) source
     *
     * @param string $classname
     * @return Whitecube\NovaPage\Sources\SourceInterface
     */
    public function getSource($classname = null) : SourceInterface
    {
        if(is_null($classname)) {
            return array_values($this->sources)[0];
        }

        if(isset($this->sources[$classname])) {
            return $this->sources[$classname];
        }

        $source = new $classname($this->config);

        if(!($source instanceof SourceInterface)) {
            return null;
        }

        $source->setConfig($this->config['sources'][$source->getName()] ?? []);

        return $this->sources[$classname] = $source;
    }

    /**
     * Return a new Container instance
     *
     * @param string $identifier
     * @param array $data
     * @param Whitecube\NovaPage\Sources\SourceInterface $source
     * @return Whitecube\NovaPage\Page\Container
     */
    protected function getNewContainer($identifier, array $data, SourceInterface $source)
    {
        return new Container($identifier, $data, $source);
    }

    /**
     * Get a loaded container by its identifier
     *
     * @param string $identifier
     * @return Whitecube\NovaPage\Page\Container
     */
    public function get($identifier = null)
    {
        if(is_null($identifier)) {
            return $this->current;
        }

        foreach ($this->loaded as $key => $container) {
            if($key === $identifier) return $container;
            if(substr($key, strpos($key, '.') + 1) === $identifier) return $container;
        }

        return;
    }

    /**
     * Get an attribute on the current container
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
     * Forward a method call to the current container
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