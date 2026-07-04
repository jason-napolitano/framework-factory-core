<?php

describe('cache tests', function () {
    test('the cache directory is successfully created', function () {
        expect(file_exists(Tests\TestState::$cachePath))->toBeTrue();
    });

    test('the cache file is successfully created and exists', function () {
        expect(file_exists(rtrim(Tests\TestState::$cachePath, '/') . '/app.php'))->toBeTrue();
    });

    test('the cache file includes the standard service providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $providers = $file['providers'];

        expect($providers)->toContain(
            \Tests\App\Providers\MessageServiceProvider::class,
            \Tests\App\Providers\ReportServiceProvider::class,
        );
    });

    test('the cache file includes the deferred service providers', function () {
        $file = require rtrim(Tests\TestState::$cachePath, '/') . '/app.php';
        $deferred = $file['deferred'];

        expect($deferred)
            ->toHaveKey('deferred_message')
            ->and($deferred['deferred_message'])
            ->toContain(\Tests\App\Providers\DeferredServiceProvider::class);
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
