<?php

namespace Tests\Unit\Sources;

use FakeTestApp\Nova\Templates\Test;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Pages\Query;
use Whitecube\NovaPage\Pages\TemplatesRepository;
use Whitecube\NovaPage\Sources\Filesystem;

class FilesystemTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('novapage.sources.filesystem.path', __dir__ . '/../../test-application/resources/lang/{type}/{key}.json');
    }

    /** @test */
    public function can_create_an_instance()
    {
        $instance = new Filesystem();
        $this->assertInstanceOf(Filesystem::class, $instance);
    }

    /** @test */
    public function can_return_its_name()
    {
        $instance = new Filesystem();
        $this->assertSame('filesystem', $instance->getName());
    }

    /** @test */
    public function can_set_its_path_via_config()
    {
        $instance = (new class extends Filesystem {
            public function getPath()
            {
                return $this->path;
            }
        });

        $instance->setConfig([
            'path' => 'test'
        ]);

        $this->assertSame('test', $instance->getPath());
    }

    /** @test */
    public function can_fetch_the_values_of_a_page()
    {
        $instance = new Filesystem();
        $template = new Test('test', 'route', 'test');
        $instance->setConfig(config('novapage.sources.' . $instance->getName()));
        $data = $instance->fetch($template);
        $this->assertCount(3, $data['attributes']);
        $this->assertSame('Test value', $data['attributes']['test_field']);
    }

    /** @test */
    public function can_store_the_values_of_a_page()
    {
        $instance = new Filesystem();
        $template = new Test('test', 'route', 'test');
        $instance->setConfig(config('novapage.sources.' . $instance->getName()));
        $template->load();
        $this->assertNull($template->foobarbaz);
        $template->foobarbaz = 'foobarbaz';
        $instance->store($template);
        $contents = json_decode(file_get_contents(__dir__ . '/../../test-application/resources/lang/route/test.json'));
        $this->assertSame('foobarbaz', $contents->attributes->foobarbaz);

        unset($contents->attributes->foobarbaz);
        file_put_contents(__dir__ . '/../../test-application/resources/lang/route/test.json', json_encode($contents, JSON_PRETTY_PRINT, 512));
    }

    /** @test */
    public function can_return_the_file_path()
    {
        $instance = new Filesystem();
        $instance->setConfig(config('novapage.sources.' . $instance->getName()));
        $this->assertSame(__dir__ . '/../../test-application/resources/lang/route/test.json', $instance->getFilePath('route', 'test'));
    }

    /** @test */
    public function can_create_necessary_directories_when_storing_a_file()
    {
        $instance = new Filesystem();
        $template = new Test('test', 'route', 'test');
        $instance->setConfig(config('novapage.sources.' . $instance->getName()));
        $template->load();

        $this->rmrf(__dir__ . '/../../test-application/resources/lang/route');

        $instance->store($template);
        $this->assertDirectoryExists(__dir__ . '/../../test-application/resources/lang/route');
        $this->assertFileExists(__dir__ . '/../../test-application/resources/lang/route/test.json');
    }

    /**
     * Remove the directory and its content (all files and subdirectories).
     * @param string $dir the directory name
     */
    private function rmrf($dir) {
        if($dir == '' || $dir == '/') return;

        foreach (glob($dir) as $file) {
            if (is_dir($file)) {
                $this->rmrf("$file/*");
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }
}