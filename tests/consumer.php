<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\TestCase;

require getcwd().'/vendor/autoload.php';
$app = require getcwd().'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$html = Blade::render('<footer>Host copyright<x-site-credit /></footer>');
if (! str_contains($html, 'https://www.bwd.io') || ! str_contains($html, 'Host copyright')) {
    throw new RuntimeException('Production discovery or rendering failed.');
}
if (class_exists(TestCase::class)) {
    throw new RuntimeException('Consumer unexpectedly contains test dependencies.');
}
echo "Production consumer renders the shared credit with cached configuration.\n";
