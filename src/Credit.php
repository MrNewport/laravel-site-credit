<?php

namespace MrNewport\SiteCredit;

use Illuminate\Contracts\Config\Repository;
use InvalidArgumentException;

final class Credit
{
    public function __construct(private readonly Repository $config) {}

    /** @return array{enabled: bool, label: string, name: string, url: string, title: string, theme: string} */
    public function toArray(?string $theme = null): array
    {
        $url = (string) $this->config->get('site-credit.url');
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('The site credit URL must be an absolute HTTP or HTTPS URL.');
        }

        $theme ??= (string) $this->config->get('site-credit.theme', 'inherit');

        if (! in_array($theme, ['inherit', 'light', 'dark'], true)) {
            throw new InvalidArgumentException('The site credit theme must be inherit, light, or dark.');
        }

        return [
            'enabled' => (bool) $this->config->get('site-credit.enabled', true),
            'label' => (string) $this->config->get('site-credit.label'),
            'name' => (string) $this->config->get('site-credit.name'),
            'url' => $url,
            'title' => (string) $this->config->get('site-credit.title'),
            'theme' => $theme,
        ];
    }
}
