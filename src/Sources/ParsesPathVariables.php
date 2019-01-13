<?php

namespace Whitecube\NovaPage\Sources;

use App;

trait ParsesPathVariables
{

    /**
     * The available path variables
     *
     * @var array
     */
    protected $pathVariableResolvers = [
        '{type}' => 'resolveTypePathVariable',
        '{key}' => 'resolveKeyPathVariable',
        '{locale}' => 'resolveLocalePathVariable',
    ];

    /**
     * Replaces path variables with the request's matching values
     *
     * @param string $path
     * @param array $defaults
     * @return string
     */
    public function parsePath($path, $defaults = [])
    {
        foreach ($this->pathVariableResolvers as $variable => $resolver) {
            $default = $defaults[trim($variable, '{}')] ?? null;
            $path = str_replace($variable, call_user_func_array([$this, $resolver], [$default]), $path);
        }

        return $path;
    }

    /**
     * Provides the {type} variable value
     *
     * @param mixed $default
     * @return string
     */
    protected function resolveTypePathVariable($default = null)
    {
        return $default ?? 'route';
    }

    /**
     * Provides the {key} variable value
     *
     * @param mixed $default
     * @return string
     */
    protected function resolveKeyPathVariable($default = null)
    {
        return $default ?? '';
    }

    /**
     * Provides the {locale} variable value
     *
     * @param mixed $default
     * @return string
     */
    protected function resolveLocalePathVariable($default = null)
    {
        return $default ?? App::getLocale();
    }

}