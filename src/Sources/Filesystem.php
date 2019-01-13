<?php

namespace Whitecube\NovaPage\Sources;

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