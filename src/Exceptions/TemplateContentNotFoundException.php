<?php 

namespace Whitecube\NovaPage\Exceptions;

use Exception;

class TemplateContentNotFoundException extends Exception
{

    /**
     * Define the Exception
     *
     * @param string $source
     * @param string $name
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($source, $name, $code = 0, Exception $previous = null) {
        $message = 'Unable to load content for "' . $name . '" from source "' . $source . '".';
        parent::__construct($message, $code, $previous);
    }

}