<?php

namespace Tests\Unit\Pages;

use Carbon\Carbon;
use FakeTestApp\Nova\Templates\Test;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Exceptions\TemplateContentNotFoundException;
use Whitecube\NovaPage\Exceptions\ValueNotFoundException;
use Whitecube\NovaPage\Pages\Query;
use Whitecube\NovaPage\Pages\Template;
use Whitecube\NovaPage\Sources\Filesystem;

class TemplateTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    protected function getInstance()
    {
        $instance = (new class('test', 'route', 'test') extends Test {
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
        $instance = new Test('test', 'route', 'test', true);
        $instance->load();
    }

    /** @test */
    public function can_transform_method_calls_to_getters()
    {
        $instance = new Test('test', 'route', 'test', false);
        $this->assertSame('test', $instance->name());
    }

    /** @test */
    public function throws_an_exception_when_calling_method_that_does_not_exist()
    {
        $this->expectException(\BadMethodCallException::class);
        $instance = new Test('test', 'route', 'test', false);
        $instance->foobarbaz();
    }

    /** @test */
    public function can_return_key()
    {
        $instance = new Test('test', 'route', 'test', false);
        $this->assertSame('test', $instance->getKey());
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
        $instance = new Test('test', 'route', 'test', false);
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
        $this->assertSame('foo', $instance->computed);
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

    /** @test */
    public function can_return_timestamps_linked_to_template()
    {
        $instance = $this->getInstance();
        $this->assertInstanceOf(Carbon::class, $instance->getDate('created_at'));
    }

    /** @test */
    public function can_set_a_date_and_convert_it_to_a_carbon_instance()
    {
        $instance = $this->getInstance();
        $this->assertNull($instance->getDate('foobarbaz'));
        $instance->setDate('foobarbaz', '17 march 2019');
        $this->assertInstanceOf(Carbon::class, $instance->getDate());
    }

    /** @test */
    public function can_conditionnally_set_a_date_with_a_closure_condition()
    {
        $instance = $this->getInstance();
        $instance->setDateIf('foobarbaz', '17 march 2019', function($date) {
            return false;
        });
        $this->assertNull($instance->getDate('foobarbaz'));
        $instance->setDateIf('foobarbaz', '17 march 2019', function($date) {
            return true;
        });
        $this->assertInstanceOf(Carbon::class, $instance->getDate('foobarbaz'));
    }

    /** @test */
    public function can_set_values()
    {
        $instance = $this->getInstance();
        $this->assertNull($instance->foobarbaz);
        $instance->foobarbaz = 'test';
        $this->assertSame('test', $instance->foobarbaz);
        $instance->nova_page_title = 'Page title modified';
        $this->assertSame('Page title modified', $instance->title());
        $instance->nova_page_created_at = '17 march 2019';
        $this->assertSame(3, $instance->getDate()->month);
    }

    /** @test */
    public function can_check_if_an_attribute_is_set()
    {
        $instance = $this->getInstance();
        $this->assertFalse($instance->offsetExists('foobarbaz'));
        $instance->foobarbaz = 'test';
        $this->assertTrue($instance->offsetExists('foobarbaz'));
    }

    /** @test */
    public function can_get_a_value()
    {
        $instance = $this->getInstance();
        $this->assertSame('Test value', $instance->offsetGet('test_field'));
    }

    /** @test */
    public function can_set_a_value()
    {
        $instance = $this->getInstance();
        $instance->offsetSet('foobarbaz', 'test');
        $this->assertSame('test', $instance->foobarbaz);
    }

    /** @test */
    public function can_unset_an_attribute()
    {
        $instance = $this->getInstance();
        $instance->foobarbaz = 'test';
        $instance->offsetUnset('foobarbaz');
        $this->assertNull($instance->foobarbaz);
    }

    /** @test */
    public function can_get_a_fresh_query_instance()
    {
        $instance = $this->getInstance();
        $this->assertInstanceOf(Query::class, $instance->newQueryWithoutScopes());
    }

    /** @test */
    public function can_save_the_data()
    {
        $instance = $this->getInstance();
        $source = $this->createMock(Filesystem::class);
        $source->method('fetch')->willReturn([
            'title' => 'Page title',
            'attributes' => [
                'test_field' => 'Test value'
            ]
        ]);
        $source->expects($this->once())->method('store');
        $instance->setSource($source);
        $instance->save();
    }

    /** @test */
    public function can_return_raw_values()
    {
        $instance = $this->getInstance();
        $this->assertCount(2, $instance->getRaw());
        $this->assertArrayHasKey('attributes', $instance->getRaw());
    }

    /** @test */
    public function can_get_json_attributes()
    {
        $instance = $this->getInstance();
        $this->assertCount(1, $instance->getJsonAttributes());
        $this->assertSame('foo_json', $instance->getJsonAttributes()[0]);
    }

    /** @test */
    public function can_check_if_has_json_attribute()
    {
        $instance = $this->getInstance();
        $this->assertTrue($instance->isJsonAttribute('foo_json'));
        $this->assertFalse($instance->isJsonAttribute('bar_json'));
    }

}
