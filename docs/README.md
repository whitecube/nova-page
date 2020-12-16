# Nova Page

Ever wanted to expose static content of an "About" page as editable fields in your app's administration without having to create specific models & migrations? Using this package, you'll be able to do so. By default, it will store the content in JSON files in the application's `resources/lang` directory, making them available for version control.

This package adds basic **flat-file CMS features** to Laravel Nova in a breeze using template configurations as if it were administrable Laravel Models, meaning it allows the usage of all the available Laravel Nova fields and tools.

## Installation

```bash
composer require whitecube/nova-page
```

If using Laravel >= 5.5, the service providers and aliases will register automatically.

If for some reason you must do it manually, register the package in the `providers` section of the app config file in `app/config/app.php` and also add the facade:

```php
'providers' => [
    // ...
    Whitecube\NovaPage\NovaPageServiceProvider::class,
],

'aliases' => [
    // ...
    'Page' => Whitecube\NovaPage\NovaPageFacade::class,
],
```

Finally, register the Nova tool in `app/Providers/NovaServiceProvider.php`:

```php
/**
 * Get the tools that should be listed in the Nova sidebar.
 *
 * @return array
 */
public function tools()
{
    return [
        \Whitecube\NovaPage\NovaPageTool::make(),
    ];
}
```

## Configuration

You can publish the package's configuration file with the `php artisan vendor:publish` command. This will add the `app/config/novapage.php` file containing the package's default configuration.

### Using the database as the data source

A database source is available if you do not wish to make use of the package's flat-file capabilities.

#### Changing the config
In the config file, change the `default_source` option to `\Whitecube\NovaPage\Sources\Database::class`.

#### Running the migration

You will need to run the following command:

```bash
php artisan vendor:publish --tag=nova-page-migrations
```

Doing so will copy the migration to create the `static_pages` table into your project's `database/migrations` directory. Then, simply run:

```bash
php artisan migrate
```

#### Customizing the table name

You can customize the table name in the migration file and then update the `table_name` parameter in the `novapage.php` config file, if you wish.

## Templates

In order to assign fields (and even cards!) to a page's edition form, we'll have to create a `Template` class and register this class on one or more routes. You'll see, it's quite easy.

### Creating templates

Each Template is defined in an unique class that resembles regular Nova Resources. You can store those classes wherever you want, but the package is configured to use `app/Nova/Templates` by default.

You can use the artisan command to generate the file:

```bash
php artisan make:template About
```

Which will result in:

```php
namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Whitecube\NovaPage\Pages\Template;

class About extends Template
{

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }
}
```

Fields and cards definition is exactly the same as regular [Laravel Nova Resources](https://nova.laravel.com/docs/1.0/resources/fields.html#defining-fields).

### Assigning templates

Once the template is defined, simply assign it to the routes you want using the `template()` method (which is added to the original `Route` api by this package):

```php
Route::get('/about-me', 'AboutController@show')
    ->template(\App\Nova\Templates\About::class)
    ->name('about');
```

**Important**: Do not forget to name the routes you'll be using with NovaPage templates. Route names are used for retrieving and naming the route's JSON files.

### Option templates

Most websites or applications have repeated content, such as a copyright or contact information. These  attributes should not change from page to page, that's why you can define Option "templates". These templates are not bound to a specific route or page and are loaded on request, making it convenient to use them in the app's layouts. You can add as many Option "pages" as you want.

First, create a regular `Template` as described above (e.g.: `php artisan make:template FooterOptions`) and fill the template's `fields()` method with all the wanted Nova fields.

Now, register the template in NovaPage's Manager using the `registerOption(string $name, string $template)`. A good place to do this is in a ServiceProvider's `boot` method:

```php
use Whitecube\NovaPage\Pages\Manager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Manager $pages)
    {
        $pages->register('option', 'footer', \App\Nova\Templates\FooterOptions::class);
    }
}
```

## Loading pages for display

### Middleware autoloading, the easiest way

It is possible to load the page's static content automatically using the `LoadPageForCurrentRoute` middleware. This way, the application will fetch the current page's data using the current route name as identifier.

Add `\Whitecube\NovaPage\Http\Middleware\LoadPageForCurrentRoute::class` to the `routeMiddleware` array located in the `App\Http\Kernel` file:

```php
/**
 * The application's route middleware.
 *
 * These middleware may be assigned to groups or used individually.
 *
 * @var array
 */
protected $routeMiddleware = [
    // ...
    'loadNovaPage' => \Whitecube\NovaPage\Http\Middleware\LoadPageForCurrentRoute::class,
];
```

You can now assign the `loadNovaPage` middleware to all routes that need it, or even add it to the `web` middleware group in the same `App\Http\Kernel` file:

```php
/**
 * The application's route middleware groups.
 *
 * @var array
 */
protected $middlewareGroups = [
    'web' => [
        // ...
        'loadNovaPage',
    ],
    // ...
};
```

### Manual loading

At any time, pages can be loaded using the package's Page Manager. Simply type-hint the `Whitecube\NovaPage\Pages\Manager` dependency in a controller and call its `load($name, $type = 'route', $current = true, $throwOnMissing = false)` method:

```php
use App\Nova\Templates\Aboutpage;
use Whitecube\NovaPage\Pages\Manager;

class AboutController extends Controller
{

    public function show(Manager $page)
    {
        $page->load('about');
        return view('pages.about');
    }

}
```

In most cases, this will probably not be very convenient since we alreay registered the current route's template in our routes definitions. Therefore, it is also possible to load the page's content with an `Illuminate\Routing\Route` instance using the `loadForRoute($route, $locale = null, $current = true)` method:

```php
use Illuminate\Http\Request;
use Whitecube\NovaPage\Pages\Manager;

class AboutController extends Controller
{

    public function show(Request $request, Manager $page)
    {
        $page->loadForRoute($request->route());
        return view('pages.about');
    }

}
```

Anyway, if no locale is provided, NovaPage will use the application's current locale (using `App::getLocale()`). By default, loading a page's content will define it as the current page, making its attributes accessible with the `Page` facade. If you just want to load content without setting it as the current page, you should call `load()` or `loadForRoute()` with `$current` set to `false`.

## Front-end Template Usage

Retrieving the page's static values in your application's blade templates is made possible using the `Page` facade and its different methods:

```php
@extends('layout')

@section('pageName', Page::name())

@section('content')
    <h1>{{ Page::title('Default title', 'My website: ', ' • Awesome appended string') }}</h1>
    <p>Edited on <time datetime="{{ Page::date('updated_at')->format('c') }}">{{ Page::date('updated_at')->toFormattedDateString() }}</time></p>
    <p>{{ Page::get('introduction') }}</p>
    <a href="{!! Page::get('cta.href') !!}">{{ Page::get('cta.label') }}</a>
    <p>Copyright: {{ Page::option('footer')->copyright }}</p>
@endsection
```

Alternatively, we also added two useful Blade directives, `@get($attribute)` and `@option($key)`, which allow to access static attributes in a shorter way:

```php
@extends('layout')

@section('pageName', Page::name())

@section('content')
    <p>@get('introduction')</p>
    <a href="@get('cta.href')">@get('cta.label')</a>
    <p>Copyright: @option('footer.copyright')</p>
@endsection
```

### Useful Facade methods

**`Page::name()`**

Returns the page's name (usually the route's name).

**`Page::title($default = null, $prepend = null, $append = null)`**

Returns and optionally formats the page's title. The title is an automatic & required field and **is not** linked to or overwritten by an alternative `title` attribute you could add to the page's fields.

**`Page::locale()`**

Returns the locale code of the loaded page. Usually (and should be) the same as `App::getLocale()`.

**`Page::date($timestamp = 'created_at')`**

Returns a [Carbon](https://carbon.nesbot.com/) instance of the requested timestamp. Possible timestamps are:

- `created_at`
- `updated_at`

> **Note**: Since most UNIX systems do not have a creation date for their files, the `created_at` and `updated_at` timestamps are stored in the file's JSON attributes. Keep this in mind if you want to edit the files in a text editor.

**`Page::get($attribute, $callback = null)`**

Returns a defined field's value. Optionally, you can provide a callback `Closure` that will be applied to the returned value.

**`Page::find($name)`**

Returns a previously loaded page template.

**`Page::option($name)`**

Loads and/or returns an option template. Can be used to retrieve an option template's attributes (`Page::option('footer')->copyright`).

### Dependency Injection

Alternatively, it's also possible to type-hint the current `Whitecube\NovaPage\Page\Template` in classes resolved by Laravel's [Service Container](https://laravel.com/docs/container), such as controllers. **The page needs to be loaded before** the `Page\Template` is requested, which can be easily achieved using the package's `LoadPageForCurrentRoute` middleware.

```php
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Template;

class HomepageController extends Controller
{

    public function show(Template $template, Manager $novapage)
    {
        // Load other pages or options using 
        // Manager::load(string $name, string $type = 'route' | 'option', bool $current = true)
        $novapage->load('contact', 'route', false);

        return view('pages.home', [
            // $template contains the loaded template for the current route.
            // If needed, manipulate $template's attribute before passing it to the view.
            'page' => $template,
            // Include other pages or option templates using NovaPage's Manager
            'contact' => $novapage->find('contact'),
            'footer' => $novapage->option('footer'),
        ]);
    }

}
```

And use it as a regular object in the `pages.home` template:

```php
@extends('layout')

@section('pageName', $page->getName())

@section('content')
    <h1>{{ $page->getTitle('Default title', 'My website: ', ' • Awesome appended string') }}</h1>
    <p>Edited on <time datetime="{{ $page->getDate('updated_at')->format('c') }}">{{ $page->getDate('updated_at')->toFormattedDateString() }}</time></p>
    <p>{{ $page->introduction }}</p>
    <a href="{!! $page->cta->href !!}">{{ $page->cta->label }}</a>
    <p>Copyright: {{ $footer->copyright }}</p>
@endsection
```

As you can see, for convenience regular attributes (= defined fields) can be directly retrieved as properties of the `Whitecube\NovaPage\Pages\Template` instances.

## Advanced features

### Working with JSON fields

Since MySQL has a JSON data type, some Nova fields take advantage of the nested behavior of these "JSON attributes". As you already know, NovaPage stores its data in the filesystem using JSON files (unless you change the `default_source` option, of course). Most Laravel Packages or Nova fields exploiting the JSON data type expect a raw JSON string as input, that's why NovaPage leaves attributes containing arrays and objects as strings.

If your application needs to access these attributes as parsed associative arrays, you can declare them using the `jsonAttributes` array in your Template class:

```php
namespace App\Nova\Templates;

use Whitecube\NovaPage\Pages\Template;

class Home extends Template
{
    /**
     * The JSON attributes that should be automatically decoded
     *
     * @var array
     */
    protected $jsonAttributes = ['my_json_attribute', 'another_json_attribute'];
}
```
