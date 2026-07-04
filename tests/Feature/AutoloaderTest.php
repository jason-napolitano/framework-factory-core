<?php

use FrameworkFactory\Application;
use FrameworkFactory\Contracts;

describe('autoloader tests', function () {
    test('the autoloader has been successfully registered upon the creation of the application', function () {
        expect(Application::autoloader())->toBeInstanceOf(Contracts\Application\AutoloaderInstance::class);
    });
})->group('autoloader');
