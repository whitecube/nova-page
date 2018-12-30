<?php

namespace Whitecube\NovaPage\Page;

use Carbon\Carbon;
use Whitecube\NovaPage\Sources\SourceInterface;

class Container
{

    /**
     * The page resource identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * The page's title
     *
     * @var string
     */
    protected $title;

    /**
     * The page's timestamps
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The page's locale code
     *
     * @var string
     */
    protected $locale;

    /**
     * The page's attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The page's source
     *
     * @var Whitecube\NovaPage\Sources\SourceInterface
     */
    protected $source;

    /**
     * Create the page resource
     *
     * @param array $config
     */
    public function __construct($identifier, $data, SourceInterface $source)
    {
        $this->identifier = $identifier;
        $this->title = $data['title'] ?? null;
        $this->locale = $data['locale'] ?? null;
        $this->setDate('created_at', $data['created_at'] ?? null);
        $this->setDate('updated_at', $data['updated_at'] ?? null);
        $this->attributes = $data['attributes'] ?? [];
        $this->source = $source;
    }

    /**
     * Retrieve the page's identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->identifier;
    }

    /**
     * Retrieve the page's title
     *
     * @param string $default
     * @param string $prepend
     * @param string $append
     * @return string
     */
    public function getTitle($default = null, $prepend = '', $append = '')
    {
        $title = trim($prepend . ($this->title ?? $default ?? '') . $append);
        return strlen($title) ? $title : null;
    }

    /**
     * Retrieve the page's locale
     *
     * @param string $default
     * @return string
     */
    public function getLocale($default = null)
    {
        return strlen($this->locale) ? $this->locale : $default;
    }

    /**
     * Retrieve a page's attribute
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * Retrieve a timestamp linked to this page resource
     *
     * @param string $moment
     * @return Carbon\Carbon
     */
    public function getDate($moment)
    {
        return $this->dates[$moment] ?? null;
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

}