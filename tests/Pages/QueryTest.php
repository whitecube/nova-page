<?php

namespace Tests\Pages;

use FakeTestApp\Nova\Templates\Test;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Pages\Query;
use Whitecube\NovaPage\Pages\TemplatesRepository;

class QueryTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    /** @test */
    public function can_create_an_instance()
    {
        $repository = $this->createMock(TemplatesRepository::class);
        $instance = new Query($repository);
        $this->assertInstanceOf(Query::class, $instance);
    }

    /** @test */
    public function can_set_a_where_condition()
    {
        $repository = $this->createMock(TemplatesRepository::class);
        $instance = (new class($repository) extends Query {
            public function getName() {
                return $this->name;
            }
        });
        $instance->whereKey('test');
        $this->assertSame('test', $instance->getName());
    }

    /** @test */
    public function can_get_templates()
    {
        $repository = $this->createMock(TemplatesRepository::class);
        $repository->method('getPages')->willReturn([
            'route.test' => Test::class
        ]);
        $template = new Test();
        $repository->method('getPageTemplate')->willReturn($template);
        $repository->method('load')->willReturn($template);
        $instance = new Query($repository);
        $this->assertCount(1, $instance->get());
        $this->assertInstanceOf(Test::class, $instance->firstOrFail());

    }


}