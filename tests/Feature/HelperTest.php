<?php

use FrameworkFactory\Application;

describe('helper tests', function () {
    test('Application::get() calls services that have been bound to the container', function () {
        /** @var \App\Services\MessageService $message */
        $message = Application::get('message')->display('hello world');

        expect($message)->toBe('hello world');
    });
})->group('helpers');
