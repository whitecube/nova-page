<?php

namespace Whitecube\NovaPage\Sources;

interface SourceInterface
{
    public function getName();
    public function setConfig(array $config);
    public function fetch($type, $key, $locale);
}