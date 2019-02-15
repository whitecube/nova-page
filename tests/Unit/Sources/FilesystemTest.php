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
        $template = new Test('test', 'route', false);
        $instance->setConfig(config('novapage.sources.' . $instance->getName()));
        $data = $instance->fetch($template);
        $this->assertCount(1, $data['attributes']);
        $this->assertSame('Test value', $data['attributes']['test_field']);
    }
}