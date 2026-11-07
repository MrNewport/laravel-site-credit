# Site Credit feature index

| Surface / signature → return | Contract / errors | Guide |
| --- | --- | --- |
| `Credit::__construct(Config\Repository)`, `toArray(?string $theme = null): array` | Six explicit fields; absolute HTTP(S), theme inherit/light/dark; invalid → InvalidArgumentException. | [Payload](HANDBOOK.md#payload-and-validation) |
| `SiteCredit::__construct(Credit, ?string $theme = null)`, `render(): View`, `shouldRender(): bool`, public credit array | Blade component `<x-site-credit>`; escaped text, attribute merging, enabled switch. | [Blade](HANDBOOK.md#blade-and-css) |
| Vue SiteCredit credit/theme props | Vue3 source, escaped text; disabled/unsafe URLs omitted; invalid theme falls back inherit; root attributes. | [Vue](HANDBOOK.md#vue3-and-inertia) |
| enabled/label/name/url/title/theme configuration | Host wording and config cache; no translation namespace; no extra keys in payload. | [Install](HANDBOOK.md#fresh-installation), [localization](HANDBOOK.md#themes-accessibility-and-localization) |
| inherit/light/dark themes; six CSS custom properties | Wrapping, underline, focus-visible, host contrast policy. | [Themes](HANDBOOK.md#themes-accessibility-and-localization) |
| SiteCreditServiceProvider, site-credit-config, site-credit-assets | Discovery, view namespace/component/singleton, publish static CSS; no routes/tables. | [Install](HANDBOOK.md#fresh-installation), [deployment](HANDBOOK.md#deployment-upgrades-and-composition) |
| Vite CSS import / static publish / hydrated frontend placement | No Tailwind or JS for Blade; optional Vue only. | [CSS](HANDBOOK.md#blade-and-css), [recipes](RECIPES.md) |

Executable references: [PHP rendering/config/asset tests](../tests/CreditTest.php), [Vue tests](../tests/js/site-credit.test.mjs), [fresh consumer smoke](../tests/consumer.php). This table covers the complete public behavior; no analytics/network state is hidden behind rendering.
