<?php

use FrameworkFactory\Application;

describe('context api tests', function () {

    test('the context api properly switches services out based on their contextual requirements', function () {
        $reportService = Application::get(\Tests\App\Services\ReportService::class);
        $warmerService = Application::get(\Tests\App\Services\CacheWarmer::class);

        expect($reportService->generate())
            ->toBe('[FILE] Generating report...')
            ->and($warmerService->warm())
            ->toBe('[NULL] Warming cache...');

    });

})->group('context_api');
