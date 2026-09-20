<p {{ $attributes->class('bwd-site-credit')->merge(['data-theme' => $credit['theme']]) }}>
    <span class="bwd-site-credit__label">{{ $credit['label'] }}</span>
    <a class="bwd-site-credit__link" href="{{ $credit['url'] }}" title="{{ $credit['title'] }}">{{ $credit['name'] }}</a>
</p>
