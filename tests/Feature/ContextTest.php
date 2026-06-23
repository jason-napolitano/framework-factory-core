<?php

use FrameworkFactory\Application;
use App\Services;

describe('context api tests', function () {

    test('the context api properly switches services out based on their contextual requirements', function () {
        $reportService = Application::get(Services\ReportService::class);
        $warmerService = Application::get(Services\CacheWarmer::class);

        expect($reportService->generate())
            ->toBe('[FILE] Generating report...')
            ->and($warmerService->warm())
            ->toBe('[NULL] Warming cache...');

    });

})->group('context_api');
