# Laravel Site Credit handbook

Site Credit renders one configured attribution paragraph and link in Blade or optional Vue3. Place it after the site's own copyright/legal information. It does not replace a footer, assert copyright ownership, load analytics or fetch remote content.

## Fresh installation

```sh
composer create-project laravel/laravel website '^12.0'
cd website
composer require mrnewport/laravel-site-credit:^1.0
php artisan vendor:publish --tag=site-credit-config
```

Use `'^13.0'` for Laravel 13 and its framework-supported PHP runtime; Laravel 12 / PHP 8.2+ is supported. If your registry has not indexed the tag, add `composer config repositories.site-credit vcs https://github.com/MrNewport/laravel-site-credit` and repeat the requirement. Laravel discovers SiteCreditServiceProvider, merges config, binds singleton Credit and registers `<x-site-credit />`. No migrations, routes, jobs, fonts or external requests are added. Blade consumers need no JavaScript package.

Configure your own authorized attribution in config/site-credit.php:

```php
return [
    'enabled' => true,
    'label' => 'Designed and built by',
    'name' => 'Example Studio',
    'url' => 'https://example.com',
    'title' => 'Visit Example Studio',
    'theme' => 'inherit',
];
```

The shipped config has a ready default attribution; publishing lets the host review its actual wording/link. These are ordinary Laravel config values and work with config:cache. Content/ownership policy belongs to the host.

## Blade and CSS

```blade
<footer class="site-footer">
    <p>© {{ date('Y') }} Example Company. All rights reserved.</p>
    <x-site-credit class="footer-credit" />
</footer>
```

The component renders a paragraph with escaped label and link text/title. Ordinary attributes/classes merge into its root; link uses the same tab. enabled=false omits the paragraph without removing the surrounding copyright. URL/theme validation still runs while building the payload, so disabled config is not a way to hide invalid configuration.

Import package CSS once after base/footer styles, from resources/js/app.js:

```js
import '../../vendor/mrnewport/laravel-site-credit/resources/css/site-credit.css';
```

No Tailwind scanning or plugin is required. Without CSS the text/link still work. Alternatively publish a static asset during the deployment build:

```sh
php artisan vendor:publish --tag=site-credit-assets --force
```

```blade
<link rel="stylesheet" href="{{ asset('vendor/site-credit/site-credit.css') }}">
```

Republish after dependency upgrades; do not run publishing per request. asset() respects your CDN/asset-host configuration. Add the asset to the deployment artifact and verify the final URL. Blade view overrides live at resources/views/vendor/site-credit/credit.blade.php; keep escaped data and root attributes when customizing.

## Payload and validation

`Credit::__construct(Illuminate\Contracts\Config\Repository)` receives configuration. `toArray(?string $theme = null): array` returns exactly enabled/label/name/url/title/theme. It validates an absolute HTTP(S) URL and theme inherit/light/dark; invalid configuration throws InvalidArgumentException. It does not expose arbitrary config keys or mutate the default when a per-component theme override is supplied.

```php
use MrNewport\SiteCredit\Credit;

$payload = app(Credit::class)->toArray();
$darkPayload = app(Credit::class)->toArray('dark');
```

Use that explicit payload for frontend sharing. The server treats the configured URL as a deliberate destination; it does not add tracking parameters, open a remote connection or verify the target's ownership. Configuration is not a user-editable HTML slot. Keep text escaped and avoid passing secrets through public attribution settings.

## Vue3 and Inertia

The optional source component needs the host's existing Vue3/Vite setup. Share server configuration rather than duplicating wording in JavaScript:

```php
// In HandleInertiaRequests::share():
return [
    ...parent::share($request),
    'siteCredit' => fn () => app(\MrNewport\SiteCredit\Credit::class)->toArray(),
];
```

From resources/js/layouts/AppLayout.vue:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
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

Import the same CSS in the application entry. credit is a required object prop and theme an optional string. Vue escapes text, omits disabled/unsafe URL payloads, accepts only HTTP(S) destinations and falls back to inherit for an unknown theme. This frontend defensive fallback differs deliberately from PHP's explicit InvalidArgumentException on invalid config. Classes/attributes fall through to the root paragraph. No separate npm package is needed when Vue3 is already installed.

For other hydrated frontends, render Blade's component in the document after the hydrated root/footer and import shared CSS in the normal asset entry. Avoid server/client rendering the same paragraph twice or placing outside markup inside a root the client replaces. A React adapter is not required for static attribution.

## Themes, accessibility and localization

Default inherit respects the host foreground/background. Blade `<x-site-credit theme="dark" />` or Vue `<SiteCredit ... theme="light" />` chooses explicit foreground/background pairs without changing the shared default. The CSS root is `.bwd-site-credit`, label `.bwd-site-credit__label`, link `.bwd-site-credit__link`; those are stable implementation class names even when custom wording is used.

Supported custom properties: --site-credit-align, --site-credit-margin, --site-credit-padding, --site-credit-color, --site-credit-background and --site-credit-font-size. Example:

```css
.site-footer .bwd-site-credit {
    --site-credit-align: center;
    --site-credit-margin: 1.25rem 0 0;
    --site-credit-padding: .75rem 1rem;
}
```

Links stay underlined and have visible keyboard focus; long names wrap on narrow screens. Verify contrast on the **actual** host background when overriding colors, plus footer spacing/mobile widths. Use a localized host config/payload for translated attribution; the package has no translation namespace or locale switcher. Keep final wording approved for the site.

## Deployment, upgrades and composition

After Composer install rebuild/import or publish the CSS, rebuild config/assets as appropriate and verify the deployed footer, asset URL, keyboard focus, mobile wrap and theme. Custom published config/views/assets are host copies; compare them to upstream on upgrades. There is no data conversion, migration rollback or background delivery state.

Run `composer test` and `npm ci && npm test` in a package development checkout for PHP and Vue server-render tests. [CreditTest](../tests/CreditTest.php) exercises rendering, escaping, config validation, theme isolation and asset publishing. [Vue tests](../tests/js/site-credit.test.mjs) exercise props/unsafe URLs/disabled state. Browser layout belongs to consuming-site tests because only the host supplies its footer background and navigation context.

[RECIPES.md](RECIPES.md) gives Blade/static deployment, Inertia shared configuration and multiple-theme patterns; [FEATURES.md](FEATURES.md) indexes the full API. Site Credit is independent of other packages: it can appear on a Fields form or Workflow application, but it does not read or operate their state.
