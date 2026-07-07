<?php

use FrameworkFactory\Application;

describe('lifecycle hook tests', function () {

    it('a container instance can execute lifecycle hooks, and executes them in the correct order', function () {
        $container = new Application\Container(\Tests\TestState::$cachePath);

        $events = [];

        $container->beforeResolving('service', function () use (&$events) {
            $events[] = 'before';
        });

        $container->bind('service', function () use (&$events) {
            $events[] = 'resolve';
            return new stdClass();
        });

        $container->afterResolving('service', function () use (&$events) {
            $events[] = 'after';
        });

        $service = $container->get('service');

        expect($service)
            ->toBeInstanceOf(stdClass::class)
            ->and($events)
            ->toBe([
                'before',
                'resolve',
                'after',
            ]);
    });

})->group('lifecycle');
