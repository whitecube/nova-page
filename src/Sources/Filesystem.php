<?php

namespace Whitecube\NovaPage\Sources;

use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem AS BaseFilesystem;
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
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return array
     */
    public function fetch(Template $template)
    {
        $file = $this->getFilePath($template->getType(), $template->getName());

        if(!($file = realpath($file))) {
            return;
        }

        return $this->parse($template, $file);
    }

    /**
     * Save template in the filesystem
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

        $path = $this->getFilePath($template->getType(), $template->getName());
        $this->makeDirectory($path);

        return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT, 512));
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
     * Create the path's directory structure if it does not yet exist
     *
     * @param string $path
     * @return void
     */
    protected function makeDirectory($path)
    {
        $files = resolve(BaseFilesystem::class);

        if(!$files->isDirectory(dirname($path))) {
            $files->makeDirectory(dirname($path), 0755, true, true);
        }
    }

    /**
     * Transforms a JSON file into a valid raw NovaPage content array
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @param string $file
     * @return array
     */
    protected function parse(Template $template, $file)
    {
        $json = json_decode(file_get_contents($file), true);

        return [
            'title' => $json['title'] ?? basename($file, '.json'),
            'created_at' => $json['created_at'] ?? filectime($file),
            'updated_at' => $json['updated_at'] ?? filemtime($file),
            'attributes' => $this->getParsedAttributes($template, $json['attributes'] ?? [])
        ];
    }

    /**
     * Retrieve and parse attributes array
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @param array $attributes
     * @return array
     */
    protected function getParsedAttributes(Template $template, $attributes)
    {
        foreach ($attributes as $key => $value) {
            if(!is_array($value) && !is_object($value)) continue;
            if($template->isJsonAttribute($key)) continue;
            $attributes[$key] = json_encode($value);
        }

        return $attributes;
    }
}