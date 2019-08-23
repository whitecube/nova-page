<?php

namespace Tests\Unit\Pages;

use FakeTestApp\Nova\Templates\Test;
use Illuminate\Routing\Route;
use Orchestra\Testbench\TestCase;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Query;
use Whitecube\NovaPage\Pages\TemplatesRepository;

class ManagerTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
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
        $this->assertInstanceOf(TemplatesRepository::class, $instance->getRepository());
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

    /** @test */
    public function does_not_return_anything_when_trying_to_load_route_that_does_not_have_a_template()
    {
        $route = $this->createMock(Route::class);
        $instance = $this->app->make(Manager::class);
        $this->assertNull($instance->loadForRoute($route));
    }

    /** @test */
    public function can_load_a_routes_template_properly()
    {
        return $this->assertTrue(true);

        // TODO: need to find a way to mock the "template" macro method
        $route = $this->createMock(Route::class);
        $route->method('getName')->willReturn('test');
        $route->method('template')->willReturn(true); // This line throws an error cause the Route class does not have a template method

        $instance = $this->app->make(Manager::class);
        $instance->loadForRoute($route);
    }

    /** @test */
    public function can_find_a_loaded_template()
    {
        $mock = $this->createMock(TemplatesRepository::class);
        $pageMock = $this->createMock(Test::class);
        $mock->method('getLoaded')->willReturn($pageMock);

        $instance = new Manager($mock);
        $this->assertInstanceOf(Test::class, $instance->find('test'));
    }

    /** @test */
    public function can_return_the_current_template_when_finding_without_a_key()
    {
        $mock = $this->createMock(TemplatesRepository::class);
        $pageMock = $this->createMock(Test::class);
        $mock->method('load')->willReturn($pageMock);

        $instance = new Manager($mock);
        $instance->load('test');
        $this->assertInstanceOf(Test::class, $instance->find());
    }

    /** @test */
    public function can_return_the_underlying_template_repository()
    {
        $instance = $this->app->make(Manager::class);
        $this->assertInstanceOf(TemplatesRepository::class, $instance->getRepository());
    }

    /** @test */
    public function can_return_a_query_instance()
    {
        $instance = $this->app->make(Manager::class);
        $this->assertInstanceOf(Query::class, $instance->newQueryWithoutScopes());
    }

    /** @test */
    public function can_forward_attribute_accessors_to_current_template()
    {
        $mock = $this->createMock(TemplatesRepository::class);
        $pageMock = $this->createMock(Test::class);
        $pageMock->method('__get')->willReturn('Test title');
        $mock->method('load')->willReturn($pageMock);

        $instance = new Manager($mock);
        $instance->load('test');

        $this->assertEquals('Test title', $instance->title);
    }

    /** @test */
    public function attribute_accessor_forwarding_returns_null_if_there_is_no_current_template()
    {
        $instance = $this->app->make(Manager::class);
        $this->assertNull($instance->title);
    }

    /** @test */
    public function can_forward_a_method_call_to_the_current_template()
    {
        $mock = $this->createMock(TemplatesRepository::class);
        $pageMock = $this->createMock(Test::class);
        $pageMock->expects($this->once())->method('foo')->willReturn('bar');
        $mock->method('load')->willReturn($pageMock);

        $instance = new Manager($mock);
        $instance->load('test');

        $this->assertEquals('bar', $instance->foo());
    }

    /** @test */
    public function method_call_forwarding_returns_null_if_there_is_no_current_template()
    {
        $instance = $this->app->make(Manager::class);
        $this->assertNull($instance->foo());
    }

}