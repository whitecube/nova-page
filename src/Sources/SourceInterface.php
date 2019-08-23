<?php

namespace Whitecube\NovaPage\Sources;

use Whitecube\NovaPage\Pages\Template;

interface SourceInterface
{
    public function getName();
    public function setConfig(array $config);
    public function fetch(Template $template);
    public function store(Template $template);
    public function getOriginal(Template $template);
    public function getErrorLocation($type, $name);
}
