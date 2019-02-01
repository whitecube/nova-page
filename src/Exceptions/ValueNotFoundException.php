<?php 

namespace Whitecube\NovaPage\Exceptions;

use Exception;

class ValueNotFoundException extends Exception
{

    /**
     * Define the Exception
     *
     * @param string $key
     * @param string $class
     * @param string $path
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($key, $class, $path, $code = 0, Exception $previous = null) {
        $message = $class . ': Unable to load value for key "' . $key . '". Values are stored in '. $path;
        parent::__construct($message, $code, $previous);
    }

}