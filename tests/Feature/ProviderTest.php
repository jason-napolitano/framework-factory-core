<?php

use FrameworkFactory\Application;
use Tests\App\Providers;

describe('provider tests', function () {
    test('providers have been successfully added to the container', function () {
        expect(Application::providers())->toContain(
            Providers\DeferredServiceProvider::class,
            Providers\MessageServiceProvider::class,
            Providers\ReportServiceProvider::class,
        );
    });
})->group('providers');
