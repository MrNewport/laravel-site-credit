<?php

namespace MrNewport\SiteCredit\Tests;

use Illuminate\Support\Facades\Blade;
use InvalidArgumentException;
use MrNewport\SiteCredit\Credit;
use MrNewport\SiteCredit\SiteCreditServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreditTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SiteCreditServiceProvider::class];
    }

    public function test_discovery_renders_the_original_credit_after_the_hosts_copyright(): void
    {
        $html = Blade::render('<footer><p>© 2026 Example Farm</p><x-site-credit class="extra-class" /></footer>');

        $this->assertStringContainsString('© 2026 Example Farm', $html);
        $this->assertStringContainsString('Designed, Developed and Hosted by', $html);
        $this->assertStringContainsString('Better Web Design and Development Services, LLC', $html);
        $this->assertStringContainsString('href="https://www.bwd.io"', $html);
        $this->assertStringContainsString('extra-class', $html);
        $this->assertGreaterThan(strpos($html, 'Example Farm'), strpos($html, 'Designed, Developed'));
        $this->assertStringNotContainsString('target=', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('<footer', substr($html, 8));
    }

    public function test_all_custom_text_is_escaped_and_payload_is_explicit(): void
    {
        config()->set('site-credit.name', '<img src=x onerror=alert(1)>');
        config()->set('site-credit.label', '<b>Made by</b>');
        config()->set('site-credit.title', '" onmouseover="alert(1)');
        config()->set('site-credit.secret', 'never expose');

        $html = Blade::render('<x-site-credit />');
        $credit = app(Credit::class)->toArray();

        $this->assertStringContainsString('&lt;img', $html);
        $this->assertStringContainsString('&lt;b&gt;Made by&lt;/b&gt;', $html);
        $this->assertStringContainsString('&quot; onmouseover=&quot;alert(1)', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('title="" onmouseover=', $html);
        $this->assertSame(['enabled', 'label', 'name', 'url', 'title', 'theme'], array_keys($credit));
        $this->assertSame(config('site-credit.name'), $credit['name']);
    }

    public function test_config_can_disable_both_renderers_without_removing_copyright(): void
    {
        config()->set('site-credit.enabled', false);

        $this->assertSame('<footer>Copyright remains</footer>', Blade::render('<footer>Copyright remains<x-site-credit /></footer>'));
        $this->assertFalse(app(Credit::class)->toArray()['enabled']);
    }

    public function test_theme_override_does_not_mutate_the_shared_default(): void
    {
        config()->set('site-credit.theme', 'light');

        $this->assertStringContainsString('data-theme="dark"', Blade::render('<x-site-credit theme="dark" />'));
        $this->assertSame('light', app(Credit::class)->toArray()['theme']);
        $this->assertStringContainsString('data-theme="light"', Blade::render('<x-site-credit />'));
    }

    #[DataProvider('invalidUrls')]
    public function test_non_web_destinations_are_rejected(string $url): void
    {
        config()->set('site-credit.url', $url);
        $this->expectException(InvalidArgumentException::class);
        app(Credit::class)->toArray();
    }

    public static function invalidUrls(): array
    {
        return [['javascript:alert(1)'], ['data:text/html,bad'], ['//example.com'], ['/relative'], ['https://'], ['https://example.com/" onclick="bad']];
    }

    public function test_unknown_themes_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        app(Credit::class)->toArray('unknown');
    }

    public function test_static_assets_can_be_published_without_a_frontend_build(): void
    {
        $target = public_path('vendor/site-credit/site-credit.css');
        $this->artisan('vendor:publish', ['--tag' => 'site-credit-assets', '--force' => true])->assertSuccessful();

        $this->assertFileEquals(__DIR__.'/../resources/css/site-credit.css', $target);
        $this->assertStringContainsString(':focus-visible', file_get_contents($target));
        $this->assertStringNotContainsString('@apply', file_get_contents($target));
    }
}
