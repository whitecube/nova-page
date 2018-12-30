<?php 

namespace Whitecube\NovaPage\Exceptions;

use \Exception;

class ContainerNotFoundException extends Exception
{

    /**
     * Define the Exception
     *
     * @param string $source
     * @param string $identifier
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($source, $identifier, $code = 0, Exception $previous = null) {
        $message = 'Unable to load "' . $identifier . '" from source "' . $source . '".';
        parent::__construct($message, $code, $previous);
    }

}