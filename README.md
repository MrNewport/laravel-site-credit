# Laravel Site Credit

One small attribution component shared across BWD-built Laravel sites. It preserves the wording and link used in DRD Trailers' footer:

> Designed, Developed and Hosted by Better Web Design and Development Services, LLC

The company name links to `https://www.bwd.io` in the same tab. Put this **after the site's own copyright and legal information**. This package supplies only a paragraph, not a replacement footer or copyright notice.

Supports Laravel 12 on PHP 8.2+ and Laravel 13 on PHP 8.3+. No tables, requests, analytics, scripts, fonts, jobs, or cloud resources. The Vue component is optional; Blade consumers need no JavaScript dependency.

## Install

```sh
composer config repositories.site-credit vcs https://github.com/MrNewport/laravel-site-credit
composer require mrnewport/laravel-site-credit:^1.0
```

The VCS source is only needed until the owner publishes the package on Packagist. Laravel discovers the provider automatically.

## Blade

```blade
<footer>
    <p>© {{ date('Y') }} Example Farm. All rights reserved.</p>
    <x-site-credit />
</footer>
```

Import the stylesheet **once, after your site's base/footer styles**, in the app's Vite entry. Adjust the relative path if your entry is elsewhere:

```js
// resources/js/app.js
import '../../vendor/mrnewport/laravel-site-credit/resources/css/site-credit.css';
```

Styles are prefixed with `bwd-site-credit`; they require no Tailwind scanning or build plugin. Without CSS, the attribution remains a readable, working link.

### Static CSS without Vite

```sh
php artisan vendor:publish --tag=site-credit-assets --force
```

```blade
<link rel="stylesheet" href="{{ asset('vendor/site-credit/site-credit.css') }}">
```

Publish this asset during each deployment build after Composer installs dependencies. Do not publish it at request time. `asset()` respects a configured CDN/Vapor asset host. Alternatively, concatenate the package CSS in an existing static asset build.

## Vue / Inertia

Use the same server-owned configuration and the supplied Vue 3 source component. Do not copy the company wording into a separate frontend configuration.

```php
// HandleInertiaRequests::share()
return [
    ...parent::share($request),
    'siteCredit' => fn () => app(\MrNewport\SiteCredit\Credit::class)->toArray(),
];
```

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
// Example path from resources/js/layouts/AppLayout.vue:
import SiteCredit from '../../../vendor/mrnewport/laravel-site-credit/resources/js/SiteCredit.vue';
const page = usePage();
</script>

<template>
    <footer>
        <p>© 2026 Example Company. All rights reserved.</p>
        <SiteCredit :credit="page.props.siteCredit" />
    </footer>
</template>
```

Import the shared CSS in the app entry as shown above. No new npm package is needed when the host already uses Vue 3 and its Vite plugin. Text is escaped by Vue; disabled credits and non-HTTP(S) destinations do not render. Normal classes/attributes can be passed to the component.

For prerendered React or other frontend islands in Laravel, render `<x-site-credit />` in the Blade document **after the hydrated root**, following the existing footer. Import the same stylesheet in that frontend's entry. Keeping the credit outside the hydrated root avoids duplicate markup or hydration mismatches and needs no React adapter.

## Appearance and configuration

The default `inherit` theme uses its parent's foreground/background. Host themes therefore remain authoritative. Links are underlined and have a visible keyboard focus outline. Set `theme="light"` or `theme="dark"` on the Blade/Vue component for an explicit foreground/background pair. CSS custom properties can match a site's palette:

```css
.site-footer .bwd-site-credit {
    --site-credit-align: center;
    --site-credit-margin: 1.25rem 0 0;
    --site-credit-padding: 0.75rem 1rem;
}
```

Other supported properties: `--site-credit-color`, `--site-credit-background`, and `--site-credit-font-size`. When overriding colors, verify contrast against the actual background. The component wraps long names on narrow screens and does not replace the host's layout.

Optionally publish configuration:

```sh
php artisan vendor:publish --tag=site-credit-config
```

`config/site-credit.php` contains `enabled`, `label`, `name`, `url`, `title`, and `theme`. Configuration is read through Laravel's config repository and works with `config:cache`. The PHP payload exposes only those fields. Text is escaped and URLs must be absolute HTTP(S) destinations. Configuration errors fail explicitly; no tracking parameters are added.

## Verification

```sh
composer install
composer test
npm ci
npm test
```

PHP coverage checks original wording/link, copyright preservation, attribute merging, escaping, explicit payload fields, disabled rendering, independent theme overrides, rejected destinations and publishable CSS. Vue server-render tests verify escaping, disabled/unsafe links and theme behavior. CI covers Laravel 12/PHP 8.2 and Laravel 13/PHP 8.3–8.5 plus a fresh Laravel 13 production install without development dependencies.

Browser checks belong in consuming sites, where actual footer layout, theme contrast, keyboard focus and narrow viewports can be verified.

## License

MIT. Attribution text is editable; this package's software license does not require inserting a BWD credit on unrelated sites.
