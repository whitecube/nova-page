<?php

namespace Tests\Pages;

use Carbon\Carbon;
use FakeTestApp\Nova\Templates\Test;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Exceptions\TemplateContentNotFoundException;
use Whitecube\NovaPage\Exceptions\ValueNotFoundException;
use Whitecube\NovaPage\Pages\Template;
use Whitecube\NovaPage\Sources\Filesystem;

class TemplateTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    /** @test */
    public function can_create_an_instance()
    {
        $instance = $this->app->make(Test::class);
        $this->assertInstanceOf(Template::class, $instance);
    }

    /** @test */
    public function can_return_its_source()
    {
        $instance = $this->app->make(Test::class);
        $this->assertInstanceOf(Filesystem::class, $instance->getSource());
    }

    /** @test */
    public function can_load_its_values()
    {
        $instance = $this->getInstance();
        $this->assertSame('Test value', $instance->get('test_field'));
    }

    /** @test */
    public function throws_an_error_when_values_dont_exist()
    {
        $this->expectException(TemplateContentNotFoundException::class);
        $instance = new Test('test', 'route', true);
        $instance->load();
    }

    /** @test */
    public function can_transform_method_calls_to_getters()
    {
        $instance = new Test('test', 'route', false);
        $this->assertSame('test', $instance->name());
    }

    /** @test */
    public function throws_an_exception_when_calling_method_that_does_not_exist()
    {
        $this->expectException(\BadMethodCallException::class);
        $instance = new Test('test', 'route', false);
        $instance->foobarbaz();
    }

    /** @test */
    public function can_return_key()
    {
        $instance = new Test('test', 'route', false);
        $this->assertSame('route.test', $instance->getKey());
        $this->assertNull($instance->getKeyName());
    }

    /** @test */
    public function can_get_the_templates_title()
    {
        $instance = $this->getInstance();
        $this->assertSame('Page title', $instance->getTitle());
    }

    /** @test */
    public function can_get_a_default_title()
    {
        $instance = new Test('test', 'route', false);
        $this->assertSame('Default title', $instance->getTitle('Default title'));
    }

    /** @test */
    public function can_append_and_prepend_to_title()
    {
        $instance = $this->getInstance();
        $this->assertSame('Prepend Page title Append', $instance->getTitle('Default title', 'Prepend ', ' Append'));
    }

    /** @test */
    public function can_get_a_value_and_apply_a_callback()
    {
        $instance = $this->getInstance();
        $this->assertSame('Test value callback', $instance->get('test_field', function($value) { return $value . ' callback'; }));
    }

    /** @test */
    public function can_reroute_attribute_calls()
    {
        $instance = $this->getInstance();
        $this->assertSame('Page title', $instance->nova_page_title);
        $this->assertInstanceOf(Carbon::class, $instance->nova_page_created_at);
        $instance->setThrowOnMissing(true);
        $this->expectException(ValueNotFoundException::class);
        $instance->foobarbaz;
    }

    /** @test */
    public function returns_null_when_getting_attribute_without_a_key()
    {
        $instance = $this->getInstance();
        $this->assertNull($instance->getAttribute(null));
    }

    /** @test */
    public function get_attribute_method_returns_null_if_attribute_does_not_exist()
    {
        $instance = $this->getInstance();
        $this->assertNull($instance->getAttribute('foobarbaz'));
    }

    protected function getInstance()
    {
        $instance = (new class('test', 'route', false) extends Test {
            public function setSource($source)
            {
                $this->source = $source;
            }
            public function setThrowOnMissing($throwOnMissing)
            {
                $this->throwOnMissing = $throwOnMissing;
            }
        });
        $source = $this->createMock(Filesystem::class);
        $source->method('fetch')->willReturn([
            'title' => 'Page title',
            'attributes' => [
                'test_field' => 'Test value'
            ]
        ]);
        $instance->setSource($source);
        $instance->load(true);
        return $instance;
    }

}