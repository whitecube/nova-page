<?php

namespace Whitecube\NovaPage\Pages;

use App;
use Whitecube\NovaPage\Exceptions\TemplateContentNotFoundException;
use Whitecube\NovaPage\Sources\SourceInterface;

class Manager
{

    /**
     * The registered Template's data sources. First one is default.
     *
     * @var array
     */
    protected $sources;

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

    public function __construct()
    {
        $this->getSource(config('novapage.default_source'));
    }

    /**
     * Load a new Page Template
     *
     * @param string $identifier
     * @param string $locale
     * @param bool $current
     * @param string $source
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function load($identifier, $locale = null, $current = true, $source = null)
    {
        $source = $this->getSource($source);
        $key = $source->getName() . '.' . $identifier;

        if(isset($this->loaded[$key][$locale ?? App::getLocale()])) {
            return $this->loaded[$key][$locale ?? App::getLocale()];
        }

        if(!($raw = $source->fetch($identifier, $locale))) {
            throw new TemplateContentNotFoundException($source, $identifier);
        }

        $template = $this->getNewTemplate($identifier, $raw, $source);

        if(!isset($this->loaded[$key])) {
            $this->loaded[$key] = [];
        }

        $this->loaded[$key][$template->getLocale()] = $template;
        if($current) $this->current = $template;

        return $template;
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

        if(!(($source = new $classname()) instanceof SourceInterface)) {
            return null;
        }

        $source->setConfig(config('novapage.sources.' . $source->getName()) ?? []);

        return $this->sources[$classname] = $source;
    }

    /**
     * Return a new Template instance
     *
     * @param string $identifier
     * @param array $data
     * @param Whitecube\NovaPage\Sources\SourceInterface $source
     * @return Whitecube\NovaPage\Pages\Template
     */
    protected function getNewTemplate($identifier, array $data, SourceInterface $source)
    {
        return new Template($identifier, $data, $source);
    }

    /**
     * Get a loaded Template by its identifier
     *
     * @param string $identifier
     * @return Whitecube\NovaPage\Pages\Template
     */
    public function find($identifier = null)
    {
        if(is_null($identifier)) {
            return $this->current;
        }

        foreach ($this->loaded as $key => $template) {
            if($key === $identifier) return $template;
            if(substr($key, strpos($key, '.') + 1) === $identifier) return $template;
        }

        return;
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