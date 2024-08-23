# Filament Two Factor Authentication (2FA) plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/filament-two-factor-auth.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/filament-two-factor-auth)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/vormkracht10/filament-two-factor-auth/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/vormkracht10/filament-two-factor-auth/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/filament-two-factor-auth.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/filament-two-factor-auth)


This package adds Two Factor Authentication for your Laravel Filament app, using the first party package Laravel Fortify. We provide the views and logic to enable Two Factor Authentication (2FA) in your Filament app. Possible authentication methods are:

- Email
- SMS
- Authenticator app

## Features and screenshots

### Enable Two Factor Authentication (2FA)
![Enable Two Factor Authentication (2FA)](./docs/two-factor-page.png)

### Using authenticator app as two factor method
![Authenticator app](./docs/authenticator-app.png)

### Using email or SMS as two factor method
![Email or SMS](./docs/email-or-sms.png)

### Recovery codes
![Recovery codes](./docs/recovery-codes.png)

### Two Factor authentication challenge
![Two Factor challenge](./docs/code-challenge.png)


## Installation

You can install the package via composer:

```bash
composer require vormkracht10/filament-two-factor-auth
```

If you don't have [Laravel Fortify](https://laravel.com/docs/11.x/fortify) installed yet, you can install it by running the following commands:

```bash
composer require laravel/fortify
```

```bash
php artisan fortify:install
```

```bash
php artisan migrate
```

You can then easily install the plugin by running the following command:

```bash
php artisan filament-two-factor-auth:install
```

Then add the plugin to your `PanelProvider`:

```php
->plugin(TwoFactorAuthPlugin::make())
```

Make sure your user uses the `TwoFactorAuthenticatable` trait:

```php 
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    // ...
}
```

Also define the `two_factor_type` cast on your user model:

```php
use Vormkracht10\TwoFactorAuth\Enums\TwoFactorType;

// ...

protected function casts(): array
{
    return [
        'two_factor_type' => TwoFactorType::class,
    ];
}
```

> ❗ When using `fillable` instead of `guarded` on your model, make sure to add `two_factor_type` to the `$fillable` array.

In case you're not using Laravel 11 yet, you will probably need to manually register the event listener in your `EventServiceProvider`:

```php
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Vormkracht10\TwoFactorAuth\Listeners\SendTwoFactorCodeListener;

// ...

protected $listen = [
    TwoFactorAuthenticationChallenged::class => [
        SendTwoFactorCodeListener::class,
    ],
    TwoFactorAuthenticationEnabled::class => [
        SendTwoFactorCodeListener::class,
    ],
];
```
## Usage

### Configuration

The authentication methods can be configured in the `config/filament-two-factor-auth.php` file (which is published during the install command). 

You can simply add or remove (comment) the methods you want to use:

```php
return [
    'options' => [
        TwoFactorType::email,
        TwoFactorType::phone,
        TwoFactorType::authenticator,
    ],

    'sms_service' => null, // For example: MessageBird::class
];
```

If you want to use the SMS method, you need to provide an SMS service. You can check the [Laravel Notifications documentation](https://laravel-notification-channels.com/about/) for ready-to-use services. 

**Also make sure your user model has a `phone` attribute.**

### Customization

If you want to fully customize the pages, you can override the classes in the `config/filament-two-factor-auth.php` file:

```php
return [
    // ...

    'login' => Login::class,
    'challenge' => LoginTwoFactor::class,
    'two_factor_settings' => TwoFactor::class,
    'password_reset' => PasswordReset::class,
    'password_confirmation' => PasswordConfirmation::class,
    'request_password_reset' => RequestPasswordReset::class,
];
```

Make sure you extend the original classes from the package.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Baspa](https://github.com/vormkracht10)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
