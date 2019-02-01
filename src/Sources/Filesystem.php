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
     * @return array
     */
    public function fetch($type, $key)
    {
        if(!($file = realpath($this->getFilePath($type, $key)))) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true, 512);
        if(isset($data['attributes'])) $data['attributes'] = $this->encodeNested($data['attributes']);
        if(!isset($data['created_at'])) $data['created_at'] = filectime($file);
        if(!isset($data['updated_at'])) $data['updated_at'] = filemtime($file);

        return $data;
    }

    /**
     * Retrieve data from the filesystem
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return bool
     */
    public function store(Template $template)
    {
        $data = [];
        $data['title'] = $template->getTitle();
        $data['created_at'] = $template->getdate('created_at')->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();
        $data['attributes'] = $template->getAttributes();

        return file_put_contents($this->getFilePath(
            $template->getType(),
            $template->getName()
        ), json_encode($data, JSON_PRETTY_PRINT, 512));
    }

    /**
     * Build the path to the file using its identifier
     *
     * @param string $type
     * @param string $key
     * @return string
     */
    public function getFilePath($type, $key)
    {
        $variables = [
            'type' => $type,
            'key' => $key,
        ];

        return $this->parsePath($this->path, $variables);
    }

    /**
     * Encode nested arrays and objects as if they came from a MySQL JSON column
     *
     * @param array $attributes
     * @return array
     */
    protected function encodeNested(array $attributes)
    {
        return array_map(function($value) {
            if(!is_array($value) && !is_object($value)) return $value;
            return json_encode($value);
        }, $attributes);
    }
}