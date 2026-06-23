<?php

use FrameworkFactory\Contracts\Application\AutoloaderInstance;
use FrameworkFactory\Application;

describe('autoloader tests', function () {
    test('the autoloader has been successfully registered upon the creation of the application', function () {
        expect(Application::autoloader())->toBeInstanceOf(AutoloaderInstance::class);
    });

    test('the autoloader can register a namespace and the classes within that namespace', function () {
        $classList = Application::autoloader()->getClasses();
        $classname = \App\TestClass::class;

        expect($classList)->toContain($classname);
    });
})->group('autoloader');
