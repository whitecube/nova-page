<?php

namespace Whitecube\NovaPage\Pages;

use App;
use Closure;
use Carbon\Carbon;
use BadMethodCallException;
use Whitecube\NovaPage\Sources\SourceInterface;
use Whitecube\NovaPage\Exceptions\TemplateContentNotFoundException;
use Illuminate\Http\Request;

abstract class Template
{

    /**
     * The page name (usually the route's name)
     *
     * @var string
     */
    protected $name;

    /**
     * The page's current locale code
     *
     * @var string
     */
    protected $locale;

    /**
     * The page's title for the currently loaded locales
     *
     * @var array
     */
    protected $localizedTitle = [];

    /**
     * The page's attributes for the currently loaded locales
     *
     * @var array
     */
    protected $localizedAttributes = [];

    /**
     * The page's timestamps
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The page's source
     *
     * @var mixed
     */
    protected $source;

    /**
     * Create A Template Instance.
     *
     * @param string $name
     * @param string $locale
     * @param bool $throwOnMissing
     */
    public function __construct($name = null, $locale = null, $throwOnMissing = true)
    {
        $this->name = $name;
        $this->setLocale($locale);
        $this->load($throwOnMissing);
    }

    /**
     * Get the template's source class name
     *
     * @return string
     */
    public function getSource() : SourceInterface
    {
        if(is_string($this->source) || is_null($this->source)) {
            $source = $this->source ?? config('novapage.default_source');
            $this->source = new $source;
            $this->source->setConfig(config('novapage.sources.' . $this->source->getName()) ?? []);
        }

        return $this->source;
    }

    /**
     * Load the page's static content for the current locale if needed
     *
     * @param bool $throwOnMissing
     * @return $this
     */
    public function load($throwOnMissing = true)
    {
        if(!$this->name || isset($this->localizedAttributes[$this->locale])) {
            return $this;
        }

        if($data = $this->getSource()->fetch($this->name, $this->locale)) {
            $this->fill($this->locale, $data);
            return $this;
        }

        if($throwOnMissing) {
            throw new TemplateContentNotFoundException($this->getSource()->getName(), $this->name);
        }

        return $this;
    }

    /**
     * Set all the template's attributes for given locale
     *
     * @param string $locale
     * @param array $data
     * @return void
     */
    public function fill($locale, array $data = [])
    {
        $this->localizedTitle[$locale] = $data['title'] ?? null;
        $this->localizedAttributes[$locale] = $data['attributes'] ?? [];

        $this->setDateIf('created_at', $data['created_at'] ?? null,
            function(Carbon $new, Carbon $current = null) {
                return (!$current || $new->isBefore($current));
            });

        $this->setDateIf('updated_at', $data['updated_at'] ?? null,
            function(Carbon $new, Carbon $current = null) {
                return (!$current || $new->isAfter($current));
            });
    }

    /**
     * Create a new loaded template instance
     *
     * @param string $name
     * @param string $locale
     * @param bool $throwOnMissing
     * @return \Whitecube\NovaPage\Pages\Template
     */
    public function getNewTemplate($name, $locale, $throwOnMissing = true)
    {
        return new static($name, $locale, $throwOnMissing);
    }

    /**
     * Wrap calls to getter methods without the "get" prefix
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $getter = 'get' . ucfirst($method);
        if(method_exists($this, $getter)) {
            return call_user_func_array([$this, $getter], $arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }

    /**
     * Retrieve the page name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve the page's localized title
     *
     * @param string $default
     * @param string $prepend
     * @param string $append
     * @return string
     */
    public function getTitle($default = null, $prepend = '', $append = '')
    {
        $title = $this->localizedTitle[$this->locale] ?? $default ?? '';
        $title = trim($prepend . $title . $append);
        return strlen($title) ? $title : null;
    }

    /**
     * Retrieve the page's current locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the page's current locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale = null)
    {
        $this->locale = $locale ?? App::getLocale();
    }

    /**
     * Retrieve a page's attribute
     *
     * @param string $attribute
     * @param Closure $closure
     * @return mixed
     */
    public function get($attribute, Closure $closure = null)
    {
        if($closure) {
            return $closure($this->__get($attribute));
        }

        return $this->__get($attribute);
    }

    /**
     * Magically retrieve a page's attribute
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->localizedAttributes[$this->locale][$attribute] ?? null;
    }

    /**
     * Retrieve a timestamp linked to this page resource
     *
     * @param string $timestamp
     * @return Carbon\Carbon
     */
    public function getDate($timestamp = 'created_at')
    {
        return $this->dates[$timestamp] ?? null;
    }

    /**
     * Define a timestamp
     *
     * @param string $moment
     * @param mixed $date
     * @return Carbon\Carbon
     */
    public function setDate($moment, $date = null)
    {
        if(!$date) return;

        if($date instanceof Carbon) {
            return $this->dates[$moment] = $date;
        }

        return $this->dates[$moment] = new Carbon($date);
    }

    /**
     * Define a timestamp if closure condition is met
     *
     * @param string $moment
     * @param mixed $date
     * @param Closure $closure
     * @return mixed
     */
    public function setDateIf($moment, $date = null, Closure $closure)
    {
        if(!($date instanceof Carbon)) {
            $date = new Carbon($date);
        }

        if($closure($date, $this->getDate($moment))) {
            return $this->setDate($moment, $date);
        }
    }

    /**
     * Get the fields displayed by the template.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function fields(Request $request);

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function cards(Request $request);

}