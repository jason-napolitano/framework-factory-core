<?php

describe('cache tests', function () {
    test('the cache directory is successfully created', function () {
        expect(file_exists(Tests\TestState::$cachePath))->toBeTrue();
    });

    test('the cache file is successfully created and exists', function () {
        expect(file_exists(rtrim(Tests\TestState::$cachePath, '/') . '/app.php'))->toBeTrue();
    });

    test('the cache file includes the manually added standard service providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $providers = $file['providers'];

        expect($providers)->toContain(
            \Tests\Providers\MessageServiceProvider::class,
            \Tests\Providers\ReportServiceProvider::class,
        );
    });

    test('the cache file includes the manually added deferred service providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $deferred = $file['deferred'];

        expect($deferred)
            ->toHaveKey('deferred_message')
            ->and($deferred['deferred_message'])
            ->toContain(\Tests\Providers\DeferredServiceProvider::class);
    });

    test('the cache file includes the auto-discovered standard service providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $providers = $file['providers'];

        expect($providers)->toContain(
            \App\Providers\AutoStandardServiceProvider::class,
        );
    });

    test('the cache file includes the auto-discovered deferred service providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $deferred = $file['deferred'];

        expect($deferred)
            ->toHaveKey('auto_deferred_provider')
            ->and($deferred['auto_deferred_provider'])
            ->toContain(\App\Providers\AutoDeferredServiceProvider::class);
    });

    test('the cache file includes the correct aliases for deferred providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $aliases = $file['aliases'];

        expect($aliases)
            ->toHaveKey('deferred_message')
            ->and($aliases['deferred_message'])
            ->toEqual('deferred_message');
    });
})->group('cache');
