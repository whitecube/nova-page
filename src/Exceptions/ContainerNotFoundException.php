<?php 

namespace Whitecube\NovaPage\Exceptions;

use Exception;
use Whitecube\NovaPage\Sources\SourceInterface;

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
    public function __construct(SourceInterface $source, $identifier, $code = 0, Exception $previous = null) {
        $message = 'Unable to load "' . $identifier . '" from source "' . $source->getName() . '".';
        parent::__construct($message, $code, $previous);
    }

}