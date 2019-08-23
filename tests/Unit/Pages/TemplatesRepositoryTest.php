<?php

namespace Tests\Unit\Pages;

use FakeTestApp\Nova\Templates\Test;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Exceptions\TemplateNotFoundException;
use Whitecube\NovaPage\Pages\TemplatesRepository;

class TemplatesRepositoryTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    /** @test */
    public function can_create_an_instance()
    {
        $instance = $this->app->make(TemplatesRepository::class);
        $this->assertInstanceOf(TemplatesRepository::class, $instance);
    }

    /** @test */
    public function can_return_the_list_of_templates_it_contains()
    {
        $instance = $this->app->make(TemplatesRepository::class);
        $this->assertEquals([], $instance->getTemplates());
    }

    /** @test */
    public function can_return_the_list_of_registered_pages()
    {
        $instance = $this->app->make(TemplatesRepository::class);
        $this->assertEquals([], $instance->getFiltered());
    }

    /** @test */
    public function can_register_templates_defined_with_routes()
    {
        \Route::get('/test', 'TestController@foo')->template(Test::class)->name('test');
        $instance = $this->app->make(TemplatesRepository::class);
        $instance->registerRouteTemplates();
        $this->assertCount(1, $instance->getFiltered());
    }

    /** @test */
    public function can_register_templates()
    {
        $instance = $this->app->make(TemplatesRepository::class);
        $instance->register('route', 'test', Test::class);
        $this->assertCount(1, $instance->getFiltered());
        $this->assertEquals('FakeTestApp\Nova\Templates\Test', $instance->getFiltered()['route.test']);
    }

    /** @test */
    public function can_load_a_templates_values()
    {
        $instance = $this->app->make(TemplatesRepository::class);
        $instance->register('route', 'test', Test::class);

        // Load twice so we cover every bit of code in the load method
        $instance->load('route', 'test', false);
        $instance->load('route', 'test', false);

        $this->assertNotNull($instance->getLoaded('route', 'test'));
        $this->assertNull($instance->getLoaded('route', 'foo'));
    }

    /** @test */
    public function throws_an_exception_when_loading_a_template_that_does_not_exist()
    {
        $this->expectException(TemplateNotFoundException::class);
        $instance = $this->app->make(TemplatesRepository::class);
        $instance->register('route', 'test', Test::class);
        $instance->load('route', 'foo', false);
    }

}