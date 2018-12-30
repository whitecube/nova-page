<?php

namespace Whitecube\NovaPage\Sources;

class Filesystem implements SourceInterface
{
    /**
     * The static files storage directory
     *
     * @var string
     */
    protected $directory;

    /**
     * Retrieve the source's name
     *
     * @return string
     */
    public function getName()
    {
        return 'files';
    }

    /**
     * Set the source's configuration parameters
     *
     * @return string
     */
    public function setConfig(array $config)
    {
        $this->directory = rtrim($config['directory'], '/');
    }

    /**
     * Retrieve data from the filesystem
     *
     * @param string $identifier
     * @return array
     */
    public function fetch($identifier)
    {
        if(!($file = $this->getFilePath($identifier))) {
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
     * @param string $identifier
     * @return string
     */
    protected function getFilePath($identifier)
    {
        return realpath($this->directory . DIRECTORY_SEPARATOR . $identifier . '.json');
    }
}