<?php

use FrameworkFactory\Application\Bootstrap\Autoloader;
use FrameworkFactory\Contracts;

describe('autoloader tests', function () {
    test('the autoloader can be built and register new namespaces', function () {
        $autoloader = new Autoloader();

        $autoloader->register();

        $autoloader->addNamespace('App', __DIR__ . '/../App');

        $subNamespaces = $autoloader->getSubNamespaces('App');
        $prefixes = $autoloader->prefixes();

        expect($autoloader)
            ->toBeInstanceOf(Contracts\Application\AutoloaderInstance::class)
            ->and($prefixes)
            ->toHaveKey('App\\')
            ->and($subNamespaces)
            ->toContain('Accessors', 'Contracts', 'Providers', 'Loggers', 'Services');

    });
})->group('autoloader');
