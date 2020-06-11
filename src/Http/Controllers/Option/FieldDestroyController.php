<?php

namespace Whitecube\NovaPage\Http\Controllers\Option;

use Whitecube\NovaPage\Http\Controllers\ResourceFieldDestroyController;

class FieldDestroyController extends ResourceFieldDestroyController
{
    /**
     * The queried resource's name
     *
     * @var string
     */
    protected $resourceName = 'nova-option';
}
