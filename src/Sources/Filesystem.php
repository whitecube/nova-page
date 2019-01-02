<?php

namespace Whitecube\NovaPage\Sources;

class Filesystem implements SourceInterface
{

    use ParsesPathVariables;

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
        return 'filesystem';
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
     * @param string $locale
     * @return array
     */
    public function fetch($identifier, $locale = null)
    {
        if(!($file = $this->getFilePath($identifier, $locale))) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true, 512);
        $data['locale'] = $this->resolveLocalePathVariable($locale);
        if(!isset($data['created_at'])) $data['created_at'] = filectime($file);
        if(!isset($data['updated_at'])) $data['updated_at'] = filemtime($file);

        return $data;
    }

    /**
     * Build the path to the file using its identifier
     *
     * @param string $identifier
     * @param string $locale
     * @return string
     */
    protected function getFilePath($identifier, $locale)
    {
        $variables = [
            'locale' => $locale,
        ];

        return realpath($this->parsePath($this->directory, $variables) . DIRECTORY_SEPARATOR . $identifier . '.json');
    }
}