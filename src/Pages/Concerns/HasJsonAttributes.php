<?php

namespace Whitecube\NovaPage\Pages\Concerns;

trait HasJsonAttributes
{

    /**
     * The defined JSON attributes that should be automatically decoded
     *
     * @var array
     */
    protected $jsonAttributes = [];

    /**
     * Retrieve all defined JSON attributes
     *
     * @return array
     */
    public function getJsonAttributes() : array
    {
        return is_array($this->jsonAttributes) ? $this->jsonAttributes : [];
    }

    /**
     * Check if given attribute is a JSON attribute
     *
     * @param string $attribute
     * @return bool
     */
    public function isJsonAttribute($attribute) : bool
    {
        return in_array($attribute, $this->getJsonAttributes());
    }

}