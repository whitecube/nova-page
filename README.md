# Nova Page

[![](https://img.shields.io/travis/com/whitecube/nova-page.svg?style=flat)](https://travis-ci.com/whitecube/nova-page)
![](https://img.shields.io/github/release/whitecube/nova-page.svg?style=flat)
[![Maintainability](https://api.codeclimate.com/v1/badges/67b809601a9d88bd2c14/maintainability)](https://codeclimate.com/github/whitecube/nova-page/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/67b809601a9d88bd2c14/test_coverage)](https://codeclimate.com/github/whitecube/nova-page/test_coverage)
[![](https://img.shields.io/packagist/dt/whitecube/nova-page.svg?colorB=green&style=flat)](https://packagist.org/packages/whitecube/nova-page)
[![](https://img.shields.io/github/license/whitecube/nova-page.svg?style=flat)](https://github.com/whitecube/nova-page/blob/master/LICENSE)

Ever wanted to expose static content of an "About" page as editable fields in your app's administration without having to create specific models & migrations? Using this package, you'll be able to do so. By default, it will store the content in JSON files in the application's `resources/lang` directory, making them available for version control. A database source is also available.

This package adds basic **flat-file CMS features** to Laravel Nova in a breeze using template configurations as if it were administrable Laravel Models, meaning it allows the usage of all the available Laravel Nova fields and tools.

## Quick start

Here's a very condensed guide to get you started asap. For more details, examples and advanced features, take a look at [the full docs](https://whitecube.github.io/nova-page).

### Install

```bash
composer require whitecube/nova-page
```

Then register the Nova tool in `app/Providers/NovaServiceProvider.php`:

```php
public function tools()
{
    return [
        \Whitecube\NovaPage\NovaPageTool::make(),
    ];
}
```

### Usage
In order to assign fields (and even cards!) to a page's edition form, we'll have to create a `Template` class and register this class on one or more routes. You'll see, it's quite easy.

#### Creating Templates

```bash 
php artisan make:template About
````

```php
namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
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
        return [
            Text::make('Title of the page', 'title')
        ];
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

```php
Route::get('/about-me', 'AboutController@show')
    ->template(\App\Nova\Templates\About::class)
    ->name('about');
```

Fields and cards definition is exactly the same as regular [Laravel Nova Resources](https://nova.laravel.com/docs/1.0/resources/fields.html#defining-fields).

#### Loading the data in your pages
The easiest way is to use middleware.

In the `App\Http\Kernel` file:

```php
protected $middlewareGroups = [
    'web' => [
        'loadNovaPage',
    ],
};

// ...

protected $routeMiddleware = [
    'loadNovaPage' => \Whitecube\NovaPage\Http\Middleware\LoadPageForCurrentRoute::class,
];
```


#### Accessing the data in your views

Retrieving the page's static values in your application's blade templates is possible with the `get` directive or using the `Page` facade.

```blade
<p>@get('title')</p>

// or

<p>{{ Page::get('title') }}</p>
```

Please note it is also possible to define [Option Templates](https://whitecube.github.io/nova-page/#/?id=option-templates) for repeated data, which can be accessed using:

```blade
<p>@option('footer.copyright')</p>

// or

<p>{{ Page::option('footer')->copyright }}</p>
```

## 💖 Sponsorships 

If you are reliant on this package in your production applications, consider [sponsoring us](https://github.com/sponsors/whitecube)! It is the best way to help us keep doing what we love to do: making great open source software.

## Contributing

Feel free to suggest changes, ask for new features or fix bugs yourself. We're sure there are still a lot of improvements that could be made and we would be very happy to merge useful pull requests.

Thanks!

## Made with ❤️ for open source
At [Whitecube](https://www.whitecube.be) we use a lot of open source software as part of our daily work.
So when we have an opportunity to give something back, we're super excited!  
We hope you will enjoy this small contribution from us and would love to [hear from you](https://twitter.com/whitecube_be) if you find it useful in your projects.
