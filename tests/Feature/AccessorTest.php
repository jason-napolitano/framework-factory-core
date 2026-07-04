<?php

use Tests\App\Accessors\Message;

describe('accessor tests', function () {
    test('manually loaded services loaded into the container can be accessed via accessors', function () {
        $message = Message::display('hello world');

        expect($message)->toBe('hello world');
    });
})->group('accessors');
