<?php

use App\Accessors\Message;

describe('accessor tests', function () {
    test('services loaded into the container can be accessed via accessors', function () {
        $message = Message::display('hello world');

        expect($message)->toBe('hello world');
    });
})->group('accessors');
