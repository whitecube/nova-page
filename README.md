# Nova-page for Laravel

Static pages content management system for Laravel Nova

## Installation:

In your terminal type : `composer require whitecube/nova-page` and provide "dev-master" as the version of the package. Or open up composer.json and add the following line under `require`:

```json
    {
        "require": {
            "whitecube/nova-page": "dev-master"
        }
    }
```

Now, add this line to `providers` section of the app config file in `app/config/app.php`:

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
