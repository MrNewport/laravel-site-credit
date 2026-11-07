# Site Credit recipes

## Static asset deployment

For a Laravel site with no frontend bundler, publish site-credit-assets during the deployment build, include asset('vendor/site-credit/site-credit.css') in the head and place `<x-site-credit />` after existing legal text. Configure the wording/destination in site-credit.php, then rebuild config cache. Verify the generated asset is deployed and the footer remains usable with CSS disabled. No JavaScript or Tailwind dependency is needed.

## Inertia application with one source of wording

Share `app(Credit::class)->toArray()` from middleware as a public explicit payload and render the supplied Vue component in the common layout. Import its CSS once at the application entry. Use page props to supply approved localized text; do not maintain a second hardcoded company label in each page. PHP validates config; Vue also refuses unsafe destinations from a malformed payload.

## Light and dark layout regions

```blade
<section class="bg-white text-slate-900"><x-site-credit theme="light" /></section>
<section class="bg-slate-950 text-white"><x-site-credit theme="dark" /></section>
```

Each override leaves the shared configured theme unchanged. Use distinct host CSS variables for spacing/alignment as needed, preserving underlined links/focus outlines. Test keyboard focus and actual background contrast; a class name or theme prop alone does not prove the final site's accessibility. The [PHP theme isolation test](../tests/CreditTest.php) is an executable example of independent overrides.
