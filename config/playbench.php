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
    | SVG2PNG Path
    |--------------------------------------------------------------------------
    |
    | Svg2png is a Node.js package that converts SVG to PNG. It is used to
    | convert SVG images to PNG format when running the SVG drawing
    | competition. By default, the local installation of svg2png is used.
    | Make sure you have run `npm install` in the root of the project before
    | running the benchmark command.
    |
    */

    'svg2png_path' => env('SVG2PNG_PATH'), // By default, the `node_modules` folder is used
];
