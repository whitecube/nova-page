<?php

namespace Whitecube\NovaPage\Sources;

use Whitecube\NovaPage\Pages\Template;

interface SourceInterface
{
    public function getName();
    public function setConfig(array $config);
    public function fetch($type, $key, $locale);
    public function store(Template $template, $locale);
}