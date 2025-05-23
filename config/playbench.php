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

    /*
    |--------------------------------------------------------------------------
    | Matchup configuration
    |--------------------------------------------------------------------------
    |
    | The algorithm used to determine the participants in a matchup.
    | Values can be: `random`, `less_matches`
    |
    */

    'matchup_algorithm' => env('PLAYBENCH_MATCHUP_ALGORITHM', 'random'),

    'auto_run' => [
        'matches' => env('AUTO_RUN_MATCHES', 10),
        'notify_to' => explode(',', env('AUTO_RUN_NOTIFY_TO', '')),

        'svg' => env('AUTO_RUN_SVG', false),
        'rps' => env('AUTO_RUN_RPS', false),
        'chess' => env('AUTO_RUN_CHESS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Models
    |--------------------------------------------------------------------------
    |
    | This is a list of AI models that can be used in the PlayBench.
    | The key is the model slug used in URLs and to identify the model
    | inside PlayBench. The available providers are: `openai`, `anthropic`,
    | `groq`, and `redpill`.
    |
    */

    'svg_jury' => 'gpt-4o-2024-11-20',

    'timeout' => 200 * 60, // Thinking models can take a long time to respond

    'models' => [
        'random' => [
            'provider' => null,
            'name' => 'Random Move',
            'games' => ['chess'],
        ],

        // OpenAI models
        'gpt-4o-2024-11-20' => [
            'provider' => 'openai',
            'model' => 'gpt-4o-2024-11-20',
            'name' => 'GPT-4o (2024-11-20)',
            'games' => '*',
        ],
        'gpt-4o-mini-2024-07-18' => [
            'provider' => 'openai',
            'model' => 'gpt-4o-mini-2024-07-18',
            'name' => 'GPT-4o mini (2024-07-18)',
            'games' => '*',
        ],
        'gpt-35-turbo-0125' => [
            'provider' => 'openai',
            'model' => 'gpt-3.5-turbo-0125',
            'name' => 'GPT-3.5 turbo (0125)',
            'games' => '*',
        ],
        'o1-mini-2024-09-12' => [
            'provider' => 'openai',
            'model' => 'o1-mini-2024-09-12',
            'name' => 'o1-mini (2024-09-12)',
            'direct' => true,
            'games' => '*',
        ],
        'o3-mini-2025-01-31-low' => [
            'provider' => 'openai',
            'model' => 'o3-mini-2025-01-31',
            'name' => 'o3-mini low (2025-01-31)',
            'reasoning_effort' => 'low',
            'games' => '*',
        ],
        'o3-mini-2025-01-31-high' => [
            'provider' => 'openai',
            'model' => 'o3-mini-2025-01-31',
            'name' => 'o3-mini high (2025-01-31)',
            'reasoning_effort' => 'high',
            'games' => '*',
        ],
        'gpt-41-2025-04-14' => [
            'provider' => 'openai',
            'model' => 'gpt-4.1-2025-04-14',
            'name' => 'GPT-4.1 (2025-04-14)',
            'games' => '*',
        ],
        'gpt-41-mini-2025-04-14' => [
            'provider' => 'openai',
            'model' => 'gpt-4.1-mini-2025-04-14',
            'name' => 'GPT-4.1 mini (2025-04-14)',
            'games' => '*',
        ],
        'gpt-41-nano-2025-04-14' => [
            'provider' => 'openai',
            'model' => 'gpt-4.1-nano-2025-04-14',
            'name' => 'GPT-4.1 nano (2025-04-14)',
            'games' => '*',
        ],
        'o4-mini-2025-04-16-high' => [
            'provider' => 'openai',
            'model' => 'o4-mini-2025-04-16',
            'name' => 'o4-mini high (2025-04-16)',
            'reasoning_effort' => 'high',
            'games' => '*',
        ],
        'o4-mini-2025-04-16-medium' => [
            'provider' => 'openai',
            'model' => 'o4-mini-2025-04-16',
            'name' => 'o4-mini medium (2025-04-16)',
            'reasoning_effort' => 'medium',
            'games' => '*',
        ],
        'o4-mini-2025-04-16-low' => [
            'provider' => 'openai',
            'model' => 'o4-mini-2025-04-16',
            'name' => 'o4-mini low (2025-04-16)',
            'reasoning_effort' => 'low',
            'games' => '*',
        ],
        'o3-2025-04-16-high' => [
            'provider' => 'openai',
            'model' => 'o3-2025-04-16',
            'name' => 'o3 high (2025-04-16)',
            'reasoning_effort' => 'high',
            'games' => '*',
        ],

        // Anthropic models
        'claude-37-sonnet-20250219-nothink' => [
            'provider' => 'anthropic',
            'model' => 'claude-3-7-sonnet-20250219',
            'name' => 'Claude 3.7 Sonnet (2025-02-19)',
            'max_tokens' => 16000,
            'games' => '*',
        ],
        'claude-37-sonnet-20250219-thinking' => [
            'provider' => 'anthropic',
            'model' => 'claude-3-7-sonnet-20250219',
            'name' => 'Claude 3.7 Sonnet Thinking (2025-02-19)',
            'max_tokens' => 32000,
            'temperature' => 1.0, // Must be 1.0 for thinking
            'thinking' => [
                'type' => 'enabled',
                'budget_tokens' => 20000,
            ],
            'games' => '*',
        ],
        'claude-35-sonnet-20241022' => [
            'provider' => 'anthropic',
            'model' => 'claude-3-5-sonnet-20241022',
            'name' => 'Claude 3.5 Sonnet (2024-10-22)',
            'max_tokens' => 8192,
            'games' => '*',
        ],
        'claude-opus-4-20250514-nothink' => [
            'provider' => 'anthropic',
            'model' => 'claude-opus-4-20250514',
            'name' => 'Claude Opus 4 (2025-05-14)',
            'max_tokens' => 32000,
            'games' => '*',
        ],
        'claude-opus-4-20250514-thinking' => [
            'provider' => 'anthropic',
            'model' => 'claude-opus-4-20250514',
            'name' => 'Claude Opus 4 Thinking (2025-05-14)',
            'temperature' => 1.0, // Must be 1.0 for thinking
            'max_tokens' => 32000,
            'thinking' => [
                'type' => 'enabled',
                'budget_tokens' => 20000,
            ],
            'games' => '*',
        ],
        'claude-sonnet-4-20250514-nothink' => [
            'provider' => 'anthropic',
            'model' => 'claude-sonnet-4-20250514',
            'name' => 'Claude Sonnet 4 (2025-05-14)',
            'max_tokens' => 32000,
            'games' => '*',
        ],
        'claude-sonnet-4-20250514-thinking' => [
            'provider' => 'anthropic',
            'model' => 'claude-sonnet-4-20250514',
            'name' => 'Claude Sonnet 4 Thinking (2025-05-14)',
            'temperature' => 1.0, // Must be 1.0 for thinking
            'max_tokens' => 32000,
            'thinking' => [
                'type' => 'enabled',
                'budget_tokens' => 20000,
            ],
            'games' => '*',
        ],

        // Groq models
        'llama3-70b-8192' => [ // Developer=Meta
            'provider' => 'groq',
            'model' => 'llama3-70b-8192',
            'name' => 'Llama 3.0 70B (8192)',
            'games' => '*',

        ],
        'qwen-25-32b' => [ // Developer=Alibaba Cloud
            'provider' => 'groq',
            'model' => 'qwen-2.5-32b',
            'name' => 'Qwen-2.5-32B',
            'games' => '*',
        ],
        'deepseek-r1-distill-llama-70b' => [ // Developer=DeepSeek
            'provider' => 'groq',
            'model' => 'deepseek-r1-distill-llama-70b',
            'name' => 'DeepSeek-R1-Distill-Llama-70B',
            'games' => '*',
        ],
        'deepseek-r1-distill-qwen-32b' => [ // Developer=DeepSeek
            'enabled' => false, // deepseek-r1-distill-qwen-32b` has been decommissioned and is no longer supported. Please refer to https://console.groq.com/docs/deprecations for a recommendation on which model to use instead.
            'provider' => 'groq',
            'model' => 'deepseek-r1-distill-qwen-32b',
            'name' => 'DeepSeek-R1-Distill-Qwen-32B',
            'games' => '*',
        ],

        // RedPill hosted models
        'deepseek-r1' => [
            'provider' => 'redpill',
            'model' => 'deepseek/deepseek-r1',
            'name' => 'DeepSeek R1',
            'games' => '*',
        ],
        'llama-31-405b-instruct' => [
            'provider' => 'redpill',
            'model' => 'meta-llama/llama-3.1-405b-instruct',
            'name' => 'Llama 3.1 405B Instruct',
            'games' => '*',
        ],
        'deepseek-v3' => [
            'provider' => 'redpill',
            'model' => 'deepseek/deepseek-chat',
            'name' => 'DeepSeek V3',
            'games' => '*',
        ],
        'gemini-pro-15' => [
            'provider' => 'redpill',
            'model' => 'google/gemini-pro-1.5',
            'name' => 'Gemini Pro 1.5',
            'games' => '*',
        ],

        // Gemini models
        'gemini-25-pro-preview-05-06-high' => [
            'provider' => 'gemini',
            'model' => 'gemini-2.5-pro-preview-05-06',
            'name' => 'Gemini 2.5 Pro Preview 05-06',
            'reasoning_effort' => 'high',
            'games' => '*',
        ],
        'gemini-25-flash-preview-04-17-nothink' => [
            'provider' => 'gemini',
            'model' => 'gemini-2.5-flash-preview-04-17',
            'name' => 'Gemini 2.5 Flash Preview High 04-17',
            'reasoning_effort' => 'none',
            'games' => '*',
        ],
    ],
];
