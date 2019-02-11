<?php

namespace Tests\Pages;

use Orchestra\Testbench\TestCase;
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
        $this->assertEquals([], $instance->getPages());
    }

}