<?php

namespace Tests\Unit\Pages;

use FakeTestApp\Nova\Templates\Test;
use Illuminate\Routing\Route;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Orchestra\Testbench\TestCase;
use Whitecube\NovaPage\Exceptions\TemplateNotFoundException;
use Whitecube\NovaPage\Http\Controllers\ResourceIndexController;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\PageResource;
use Whitecube\NovaPage\Pages\Template;

class PageResourceTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['Whitecube\NovaPage\NovaPageServiceProvider'];
    }

    /** @test */
    public function can_create_an_instance()
    {
        $template = $this->createMock(Template::class);
        $instance = new PageResource($template);
        $this->assertInstanceOf(PageResource::class, $instance);
    }

    /** @test */
    public function does_not_softdelete()
    {
        $this->assertFalse(PageResource::softDeletes());
    }

    /** @test */
    public function has_a_label()
    {
        $this->assertNotNull(PageResource::label());
        $this->assertNotNull(PageResource::singularLabel());
    }

    /** @test */
    public function can_get_the_templates_name_as_the_resource_subtitle()
    {
        $template = $this->createMock(Template::class);
        $template->method('getName')->willReturn('Foobar');
        $instance = new PageResource($template);
        $this->assertSame('Foobar', $instance->subtitle());
    }

    /** @test */
    public function can_get_a_fresh_instance_of_the_model_represented_by_the_resource()
    {
        $this->assertInstanceOf(Manager::class, PageResource::newModel());

        $this->expectException(TemplateNotFoundException::class);
        request()->resourceId = 'route.test';
        PageResource::newModel();
    }

    /** @test */
    public function can_provide_a_uri_key()
    {
        $this->assertNotNull(PageResource::uriKey());
    }

    /** @test */
    public function can_get_the_resources_fields_and_adds_default_nova_page_fields()
    {
        $template = $this->createMock(Template::class);
        $template->method('fields')->willReturn([
            Text::make('Foo')
        ]);
        $instance = new PageResource($template);
        $fields = $instance->fields(request());
        $this->assertCount(2, $fields);
        $this->assertInstanceOf(Panel::class, $fields[0]);
        $this->assertInstanceOf(Text::class, $fields[1]);
    }

    /** @test */
    public function can_get_the_resources_cards()
    {
        $template = $this->createMock(Template::class);
        $template->method('cards')->willReturn([
            'Test\Cards\TestCard'
        ]);
        $instance = new PageResource($template);
        $cards = $instance->cards(request());
        $this->assertCount(1, $cards);
        $this->assertSame('Test\Cards\TestCard', $cards[0]);
    }

    /** @test */
    public function returns_no_filters()
    {
        $template = $this->createMock(Template::class);
        $instance = new PageResource($template);
        $this->assertCount(0, $instance->filters(request()));
    }

    /** @test */
    public function returns_no_lenses()
    {
        $template = $this->createMock(Template::class);
        $instance = new PageResource($template);
        $this->assertCount(0, $instance->lenses(request()));
    }

    /** @test */
    public function returns_no_actions()
    {
        $template = $this->createMock(Template::class);
        $instance = new PageResource($template);
        $this->assertCount(0, $instance->actions(request()));
    }

    /** @test */
    public function does_not_allow_creation()
    {
        $this->assertFalse(PageResource::authorizedToCreate(request()));
    }

    /** @test */
    public function does_not_allow_deletion()
    {
        $template = $this->createMock(Template::class);
        $instance = new PageResource($template);
        $this->assertFalse($instance->authorizedToDelete(request()));
    }

    /** @test */
    public function can_prepare_the_resource_to_be_json_serialized()
    {
        $route = $this->createMock(Route::class);
        $route->method('getAction')->willReturn([
            'controller' => ResourceIndexController::class . '@handle'
        ]);

        $this->app->bind(NovaRequest::class, function() use ($route) {
            return new class ($route) extends NovaRequest {
                public function __construct($route) 
                {
                    $this->routeMock = $route;
                }
                public function route($param = null, $default = null)
                {
                    return $this->routeMock;
                }
            };
        });

        $template = $this->createMock(Template::class);
        $template->method('fields')->willReturn([]);

        $instance = new PageResource($template);

        $result = $instance->jsonSerialize();
        
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('fields', $result);
    }

}