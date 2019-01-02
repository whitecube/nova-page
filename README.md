# Nova Page

Ever wanted to expose static content of an "About" page as editable fields in your app's administration without having to create specific models & migrations? Using this package, you'll be able to do so. By default, it will store the content in JSON files in the application's `resources/lang` directory, making them available for version control.

This package adds basic **flat-file CMS features** to Laravel Nova in a breeze using template configurations as it were administrable Laravel Models, meaning it allows the usage of all the available Laravel Nova fields and tools.

## Installation

In your terminal type : `composer require whitecube/nova-page` and provide "dev-master" as the version of the package. Or open up composer.json and add the following line under `require`:

```json
    {
        "require": {
            "whitecube/nova-page": "dev-master"
        }
    }
```

Register the package in the `providers` section of the app config file in `app/config/app.php`:

```php
    'providers' => [
        // ...
        
        /*
         * Package Service Providers...
         */
        Whitecube\NovaPage\NovaPageServiceProvider::class,
        // ...
    ],
```

Next, add the `Page` facade:

```php
    'aliases' => [
        // ...
        'Page' => Whitecube\NovaPage\NovaPageFacade::class,
        // ...
    ],
```

Now you can publish the package's configuration file with the `php artisan vendor:publish` command. This will add a `app/config/novapage.php` file containing the package's default configuration.

## Loading pages for display

### Middleware autoloading

It is possible to load the page's static content automatically using the `LoadPageFromRouteName` middleware. This way, the application will fetch the page's data using the current route's name as identifier. Of course, this means you'll need to name the routes in order to get it to work.

For instance, it is possible to autoload the page's static content on each _web_ request by adding `\Whitecube\NovaPage\Middleware\LoadPageFromRouteName::class` to the `web` middleware group array located in the `App\Http\Kernel` file:

```php
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // ...
            \Whitecube\NovaPage\Middleware\LoadPageFromRouteName::class,
            // ...
        ],

        // ...
    ];
```

### Manual loading

At any time, pages can be loaded using the package's Page Manager. Simply type-hint the `Whitecube\NovaPage\Page\Manager` dependency in a controller and call its `load($identifier, $locale = null, $current = true, $source = null)` method:

```php
use Whitecube\NovaPage\Page\Manager;

class AboutController extends Controller
{

    public function show(Manager $page)
    {
        $page->load('about');
        return view('pages.about');
    }

}
```

If no locale is provided, NovaPage will use the application's current locale (using `App::getLocale()`). By default, loading a page's content will define it as the current page, making its attributes accessible with the `Page` facade. If you just want to load content without setting it as the current page, you should call `load()` with `$current` set to `false`.

## Template usage

Retrieving the page's static values in your application's blade templates is made possible using the `Page` facade and its different methods:

```blade
@extends('layout')

@section('template', Page::id())

@section('content')
    <h1>{{ Page::title('Default title', 'My website: ', ' • Awesome appended string') }}</h1>
    <p>Last edited on <time datetime="{{ Page::date('updated_at')->format('c') }}">{{ Page::date('updated_at')->toFormattedDateString() }}</time></p>
    <p>{{ Page::get('introduction') }}</p>
    <a href="{!! Page::get('cta.href') !!}">{{ Page::get('cta.label') }}</a>
@endsection
```

### Useful Facade methods

#### `Page::id()`

Returns the page's identifier (usualy the request's route name).

#### `Page::title($default = null, $prepend = null, $append = null)`

Returns and optionally formats the page's title. The title is an automatic & required field and **is not** linked to or overwritten by an alternative `title` attribute you could add to the page's fields.

#### `Page::locale()`

Returns the locale code of the loaded page. Usually (and should be) the same as `App::getLocale()`.

#### `Page::date($timestamp = 'created_at')`

Returns a [Carbon](https://carbon.nesbot.com/) instance of the requested timestamp. Possible timestamps are:

- `created_at`
- `updated_at`

> **Note**: Since most UNIX systems do not have a creation date for their files, the `created_at` and `updated_at` timestamps are stored in the file's JSON attributes. Keep this in mind if you want to edit the files in a text editor.

#### `Page::get($attribute, $callback = null)`

Returns a defined field's value. Optionally, you can provide a callback `Closure` that will be applied to the returned value. 
