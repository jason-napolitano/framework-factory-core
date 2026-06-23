<?php

use App\Modules\Providers as ManuallyLoadedProviders;
use App\Providers as AutoloadedProviders;
use FrameworkFactory\Application;

describe('provider tests', function () {
    test('manually loaded providers have been successfully added to the container', function () {
        expect(Application::providers())->toContain(
            ManuallyLoadedProviders\DeferredServiceProvider::class,
            ManuallyLoadedProviders\MessageServiceProvider::class,
            ManuallyLoadedProviders\ReportServiceProvider::class,
        );
    });

    test('auto-discovered providers have been successfully added to the container', function () {
        expect(Application::providers())->toContain(
            AutoloadedProviders\StandardServiceProvider::class,
            AutoloadedProviders\DeferredServiceProvider::class,
            AutoloadedProviders\DemoProvider::class,
        );
    });
})->group('providers');
