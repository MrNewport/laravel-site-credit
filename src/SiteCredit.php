<?php

namespace MrNewport\SiteCredit;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class SiteCredit extends Component
{
    /** @var array{enabled: bool, label: string, name: string, url: string, title: string, theme: string} */
    public array $credit;

    public function __construct(Credit $credit, ?string $theme = null)
    {
        $this->credit = $credit->toArray($theme);
    }

    public function render(): View
    {
        return view('site-credit::credit');
    }

    public function shouldRender(): bool
    {
        return $this->credit['enabled'];
    }
}
