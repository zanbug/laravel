<?php

// legacy: bugban → zanbug (2026-10-07) — every ZANBUG_* key falls back to the old BUGBAN_* env name.
return array(
    // Public project key from the Zanbug panel (Projects -> your project).
    'api_key' => env('ZANBUG_API_KEY', env('BUGBAN_API_KEY', '')),

    // Your Zanbug platform host.
    'host' => env('ZANBUG_HOST', env('BUGBAN_HOST', 'https://zanbug.com')),

    // Shown in the Zanbug panel after the one-time install ping (defaults to config('app.name')).
    'app_name' => env('ZANBUG_APP_NAME', env('BUGBAN_APP_NAME', null)),

    'environment' => env('ZANBUG_ENV', env('BUGBAN_ENV', env('APP_ENV', 'production'))),
    'release' => env('ZANBUG_RELEASE', env('BUGBAN_RELEASE', null)),
    'enabled' => env('ZANBUG_ENABLED', env('BUGBAN_ENABLED', true)),

    // Signed-in user is looked up on every auth guard (web, admin, api, ...),
    // not only the default. List guards here (comma separated) to set the order
    // or to resolve a guard the request has not touched yet, e.g. "admin,web".
    'auth_guards' => env('ZANBUG_AUTH_GUARDS', env('BUGBAN_AUTH_GUARDS', '')),

    // 1.0 = send everything; 0.25 = sample 25% of events.
    'sample_rate' => env('ZANBUG_SAMPLE_RATE', env('BUGBAN_SAMPLE_RATE', 1.0)),

    // Also push per-request performance logs to /api/ingest/requests.
    'capture_requests' => env('ZANBUG_CAPTURE_REQUESTS', env('BUGBAN_CAPTURE_REQUESTS', false)),

    // Auto-forward log records (Log::error(), Log::critical(), caught-and-logged errors)
    // to Zanbug as handled events, so problems logged but never thrown aren't missed.
    // log_level is the minimum PSR level forwarded: debug|info|notice|warning|error|
    // critical|alert|emergency. Records that carry a Throwable in context are skipped
    // here (Laravel's reported exceptions are already captured), avoiding double-reports.
    'capture_logs' => env('ZANBUG_CAPTURE_LOGS', env('BUGBAN_CAPTURE_LOGS', false)),
    'log_level' => env('ZANBUG_LOG_LEVEL', env('BUGBAN_LOG_LEVEL', 'error')),

    // Slow-query (performance) monitoring: report DB queries slower than
    // slow_query_ms milliseconds to /api/ingest/queries. Works for every
    // Laravel DB connection (MySQL, PostgreSQL, SQLite, ...).
    'capture_queries' => env('ZANBUG_CAPTURE_QUERIES', env('BUGBAN_CAPTURE_QUERIES', true)),
    'slow_query_ms' => env('ZANBUG_SLOW_QUERY_MS', env('BUGBAN_SLOW_QUERY_MS', 1000)),

    // Background-process attribution: one record per artisan command, cron run,
    // scheduled task and queue JOB (duration, CPU, peak memory, query count and
    // DB time, exit code). The panel uses it to show which command loads the
    // server, which commands overlap in time, and which slow queries belong to
    // which cron. Web requests are never recorded here. Cheap: one POST per
    // process at shutdown (batched every 20 jobs for long-lived workers).
    'capture_runs' => env('ZANBUG_CAPTURE_RUNS', env('BUGBAN_CAPTURE_RUNS', true)),

    // Self-update. `php artisan zanbug:update` upgrades every zanbug/* package
    // through Composer (`composer require zanbug/*:^latest`; composer only
    // rewrites composer.lock when the whole resolution succeeds).
    // Set to true to let the scheduler run it once a day automatically.
    // Default OFF: unattended dependency upgrades are a deployment decision.
    'auto_update' => env('ZANBUG_AUTO_UPDATE', env('BUGBAN_AUTO_UPDATE', false)),

    // Let the Zanbug panel ask this app to re-run one of its own captured
    // SELECTs and report how long it took — so you can verify an index actually
    // helped without leaving the panel. Only a single SELECT runs, a LIMIT is
    // forced, it is rolled back, and ONLY the timing and row count are sent
    // back — never any row data. Set to false to switch the feature off.
    'allow_query_test' => env('ZANBUG_ALLOW_QUERY_TEST', env('BUGBAN_ALLOW_QUERY_TEST', true)),

    // When a slow SELECT is captured, also run EXPLAIN on the same connection
    // and report whether an index is used, so the panel can flag full-table
    // scans. Safe: SELECT-only, guarded against reentrancy, and never throws.
    'explain_queries' => env('ZANBUG_EXPLAIN_QUERIES', env('BUGBAN_EXPLAIN_QUERIES', true)),

    // Keys scrubbed from request/session/context before sending.
    'redact' => array('password', 'password_confirmation', 'token', 'secret', 'authorization', 'cookie', 'api_key'),
);
