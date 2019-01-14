<?php

namespace Whitecube\NovaPage\Sources;

use Carbon\Carbon;
use Whitecube\NovaPage\Pages\Template;

class Filesystem implements SourceInterface
{

    use ParsesPathVariables;

    /**
     * The static files storage path route
     *
     * @var string
     */
    protected $path;

    /**
     * Retrieve the source's name
     *
     * @return string
     */
    public function getName()
    {
        return 'filesystem';
    }

    /**
     * Set the source's configuration parameters
     *
     * @return string
     */
    public function setConfig(array $config)
    {
        $this->path = rtrim($config['path'], '/');
    }

    /**
     * Retrieve data from the filesystem
     *
     * @param string $type
     * @param string $key
     * @param string $locale
     * @return array
     */
    public function fetch($type, $key, $locale)
    {
        if(!($file = $this->getFilePath($type, $key, $locale))) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true, 512);
        if(!isset($data['created_at'])) $data['created_at'] = filectime($file);
        if(!isset($data['updated_at'])) $data['updated_at'] = filemtime($file);

        return $data;
    }

    /**
     * Retrieve data from the filesystem
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @param string $locale
     * @return bool
     */
    public function store(Template $template, $locale)
    {
        $data = [];
        $data['title'] = $template->getTitle();
        $data['created_at'] = $template->getdate('created_at')->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();
        $data['attributes'] = $template->getLocalized($locale);

        return file_put_contents($this->getFilePath(
            $template->getType(),
            $template->getName(),
            $locale
        ), json_encode($data, JSON_PRETTY_PRINT, 512));
    }

    /**
     * Build the path to the file using its identifier
     *
     * @param string $type
     * @param string $key
     * @param string $locale
     * @return string
     */
    protected function getFilePath($type, $key, $locale)
    {
        $variables = [
            'type' => $type,
            'key' => $key,
            'locale' => $locale,
        ];

        return realpath($this->parsePath($this->path, $variables));
    }
}