<?php 

namespace Whitecube\NovaPage;
 
use Illuminate\Support\Facades\Facade;
use Whitecube\NovaPage\Pages\Manager;
 
class NovaPageFacade extends Facade {
 
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
 
}