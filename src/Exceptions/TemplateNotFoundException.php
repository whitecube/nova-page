<?php 

namespace Whitecube\NovaPage\Exceptions;

use Exception;

class TemplateNotFoundException extends Exception
{

    /**
     * Define the Exception
     *
     * @param string $template
     * @param string $name
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($template = null, $name = null, $code = 0, Exception $previous = null) {
        $message = 'NovaPage Template';

        if($template) {
            $message .= ' "' . $template . '"';
        }

        if($name) {
            $message .= ' for "' . $name . '"';
        }

        $message .= ' not found.';

        parent::__construct($message, $code, $previous);
    }

}