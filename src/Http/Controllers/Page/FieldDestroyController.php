<?php

namespace Whitecube\NovaPage\Http\Controllers\Page;

use Whitecube\NovaPage\Http\Controllers\ResourceFieldDestroyController;

class FieldDestroyController extends ResourceFieldDestroyController
{
    /**
     * The queried resource's name
     *
     * @var string
     */
    protected $resourceName = 'nova-page';
}
