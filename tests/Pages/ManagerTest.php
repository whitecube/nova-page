<?php

namespace Tests\Pages;

use FakeTestApp\Nova\Templates\Test;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\TemplatesRepository;

class ManagerTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
    }

    /** @test */
    public function can_create_an_instance()
    {
        $instance = $this->app->make(Manager::class);
        $this->assertInstanceOf(Manager::class, $instance);
    }

    /** @test */
    public function can_create_a_template_repository()
    {
        $instance = $this->app->make(Manager::class);
        $this->assertAttributeNotEmpty('repository', $instance);
    }

    /** @test */
    public function can_load_a_template()
    {
        $mock = $this->createMock(TemplatesRepository::class);
        $pageMock = $this->createMock(Test::class);
        $mock->method('load')->willReturn($pageMock);

        $instance = new Manager($mock);
        $this->assertInstanceOf(Test::class, $instance->load('test'));
    }

    /** @test */
    public function can_register_a_template()
    {
        $repository = $this->createMock(TemplatesRepository::class);
        $page = $this->createMock(Test::class);
        $repository->expects($this->once())->method('register')->willReturn($page);
        $repository->method('getTemplates')->willReturn([$page]);

        $instance = new Manager($repository);
        $instance->register('route', 'test', Test::class);
    }

}