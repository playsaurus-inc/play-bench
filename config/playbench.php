<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PlayBench configuration
    |--------------------------------------------------------------------------
    |
    | This file is for PlayBench configuration.
    |
    */

    'github_repo_url' => env('PLAYBENCH_GITHUB_URL', 'https://github.com/playsaurus-inc/play-bench'),

    /*
    |--------------------------------------------------------------------------
    | Resvg-cli JS CLI Path
    |--------------------------------------------------------------------------
    |
    | The SVG Benchmark uses `@resvg/resvg-js-cli` to convert SVG to PNG.
    | This is a wrapper around the `resvg` library, which is a high-performance
    | SVG rendering library written in Rust.
    | By default, the local installation inside the `node_modules`
    | folder is used.
    |
    */

    'resvg_js_cli_path' => env('PLAYBENCH_RESVG_JS_CLI_PATH'),
];
