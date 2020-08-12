This is a Composer-based installer for the [Lightning](https://www.drupal.org/project/lightning) Drupal distribution. Welcome to the future!

## Get Started With Lando
```
$ cd MY_PROJECT or bebbinburg 
$ lando start (ensure you have lando previously installed)
$ composer install
```

This will create a functioning Lightning site, open a web browser, and log you into the site using Drupal's built-in Quick Start command. If you'd rather use your own database and web server, you can skip the second step above and install Lightning like you would any other Drupal site.

Normally, Composer will install all dependencies into a `vendor` directory that is *next* to `docroot`, not inside it. This may create problems in certain hosting environments, so if you need to, you can tell Composer to install dependencies into `docroot/vendor` instead:


Either way, remember to keep the `composer.json` and `composer.lock` files that exist above `docroot` -- they are controlling your dependencies.

## How do I update Drupal core?
It's counterintuitive, but **don't add `drupal/core` to your project's composer.json!** Lightning manages Drupal core for you, so adding a direct dependency on Drupal core is likely to cause problems for you in the future.

Lightning's minor versions correspond to Drupal core's. So, for example, `acquia/lightning:~3.3.0` will require Drupal core 8.7.x. `acquia/lightning:~3.2.0` requires Drupal core 8.6.x, and `~3.1.0` requires Drupal core 8.5.x. If you wanted to update Drupal core from (for instance) 8.6.x to 8.7.x, you would do this by changing your requirement for `acquia/lightning`, like so:

```
composer require --no-update acquia/lightning:~3.3.0
composer update
```

## Compatibility table
| `acquia/lightning` version | Drupal core version | Drush version |
|----------------------------|---------------------|---------------|
| `~4.0.0` | 8.7.x | `>=9.4` |
| `~3.3.0` | 8.7.x | `>=9.4` |
| `~3.2.0` | 8.6.x | `>=9.4` |
| `~3.1.0` | 8.5.x | `>=9.4` |
