<?php

namespace Whitecube\NovaPage\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateTemplate extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:template {name? : The name of the template}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new template';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucwords($this->getNameArgument());
        $path = $this->getPath($name);

        // Make directory if it does not exist
        $this->makeDirectory($path);

        // Write file
        $this->files->put($path, $this->buildClass($name));

        $this->info('Created ' . $path);
    }

    public function getNameArgument()
    {
        if(!$this->argument('name')) {
            return $this->ask('Please provide a name for your template');
        }

        return $this->argument('name');
    }

    public function getPath($name)
    {
        return app_path('Nova/Templates/' . $name . '.php');
    }

    public function makeDirectory($path)
    {
        if(!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    public function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        return str_replace('DummyTemplate', $name, $stub);
    }

    public function getStub()
    {
        return __DIR__ . '/../Stubs/Template.php';
    }

}
