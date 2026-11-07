# Laravel Site Credit

An escaped configurable attribution paragraph/link for Blade and optional Vue3, with shared framework-independent CSS. Place it after the site’s own copyright and legal information.

```sh
composer require mrnewport/laravel-site-credit:^1.0
```

If your Composer registry has not indexed the GitHub tag, add its VCS source and repeat the requirement:

```sh
composer config repositories.laravel-site-credit vcs https://github.com/MrNewport/laravel-site-credit
composer require mrnewport/laravel-site-credit:^1.0
```

Render `<x-site-credit />` and import the supplied CSS once, or publish `site-credit-assets` for static deployment. No tables, routes, analytics or JavaScript dependency are added for Blade.

Start with the [handbook](docs/HANDBOOK.md) for fresh Laravel 12/13 installation and the complete feature/operational guide. The [feature index](docs/FEATURES.md) links every public API, return contract, failure mode and executable reference. The [recipes](docs/RECIPES.md) show serious standalone and optional cross-package application use.

Laravel's own PHP runtime requirements apply. Package dependency constraints and current test coverage are described in the handbook. Run `composer test` in a development checkout; optional browser/renderer checks are documented with their requirements. Installing this package does not automatically integrate another package or authorize application records.
