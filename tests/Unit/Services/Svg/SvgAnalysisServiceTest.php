<?php

namespace Tests\Unit\Services\Svg;

use App\Services\Svg\SvgAnalysisService;
use PHPUnit\Framework\TestCase;

class SvgAnalysisServiceTest extends TestCase
{
    protected SvgAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SvgAnalysisService;
    }

    public function test_extract_features_with_basic_rectangle()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect x="10" y="10" width="80" height="80" fill="blue" />
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        $this->assertEquals(1, $features['rectangles']);
        $this->assertEquals(1, $features['total_shapes']);
        $this->assertEquals(1, $features['unique_colors']);
        $this->assertEquals(100, $features['width']);
        $this->assertEquals(100, $features['height']);
    }

    public function test_extract_features_with_complex_svg()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 200">
            <defs>
                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="red" />
                    <stop offset="100%" stop-color="blue" />
                </linearGradient>
                <pattern id="pattern" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                    <circle cx="5" cy="5" r="2" fill="green" />
                </pattern>
                <filter id="blur">
                    <feGaussianBlur stdDeviation="3" />
                </filter>
            </defs>
            <g>
                <rect x="10" y="10" width="100" height="50" fill="url(#grad)" />
                <circle cx="150" cy="50" r="30" fill="url(#pattern)" />
                <g>
                    <ellipse cx="220" cy="50" rx="40" ry="20" fill="yellow" filter="url(#blur)" opacity="0.7" />
                </g>
            </g>
            <path d="M10,100 C30,120 50,80 70,100" stroke="purple" fill="none" />
            <text x="100" y="150">SVG Text</text>
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        $this->assertEquals(1, $features['rectangles']);
        $this->assertEquals(2, $features['circles']);
        $this->assertEquals(1, $features['ellipses']);
        $this->assertEquals(1, $features['paths']);
        $this->assertEquals(5, $features['total_shapes']);
        $this->assertEquals(2, $features['groups']);
        $this->assertEquals(1, $features['max_group_nesting']);
        $this->assertEquals(1, $features['gradients']);
        $this->assertEquals(1, $features['patterns']);
        $this->assertEquals(1, $features['filters']);
        $this->assertEquals(3, $features['defs_elements']);
        $this->assertEquals(1, $features['elements_with_opacity']);
        $this->assertEquals(1, $features['elements_with_stroke']);
        $this->assertEquals(1, $features['text_elements']);
        $this->assertGreaterThanOrEqual(1, $features['path_commands']);
        $this->assertEquals(300, $features['width']);
        $this->assertEquals(200, $features['height']);
    }

    public function test_extract_features_with_invalid_svg()
    {
        $svg = '<not-a-svg>Invalid content</not-a-svg>';

        $features = $this->service->extractFeatures($svg);

        $this->assertEmpty($features);
    }

    public function test_extract_features_with_animations()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect x="10" y="10" width="80" height="80" fill="red">
                <animate attributeName="width" from="80" to="10" dur="3s" repeatCount="indefinite" />
            </rect>
            <circle cx="50" cy="50" r="20">
                <animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="5s" repeatCount="indefinite" />
            </circle>
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        $this->assertEquals(2, $features['animations']);
        $this->assertEquals(1, $features['rectangles']);
        $this->assertEquals(1, $features['circles']);
    }

    public function test_extract_features_with_transforms()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <g transform="translate(20, 20)">
                <rect x="0" y="0" width="60" height="60" fill="green" transform="rotate(45)" />
            </g>
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        $this->assertEquals(2, $features['elements_with_transform']);
    }

    public function test_counts_unique_colors_correctly()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect x="10" y="10" width="20" height="20" fill="red" />
            <rect x="40" y="10" width="20" height="20" fill="#FF0000" /> <!-- Same as red -->
            <rect x="70" y="10" width="20" height="20" fill="blue" />
            <circle cx="20" cy="50" r="10" fill="green" />
            <circle cx="50" cy="50" r="10" fill="green" /> <!-- Duplicate -->
            <circle cx="80" cy="50" r="10" stroke="purple" fill="none" />
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        // Should count red, blue, green, purple = 4 unique colors
        $this->assertEquals(4, $features['unique_colors']);
    }

    public function test_ignores_url_references_in_color_counting()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <defs>
                <linearGradient id="grad">
                    <stop offset="0%" stop-color="yellow" />
                    <stop offset="100%" stop-color="orange" />
                </linearGradient>
            </defs>
            <rect x="10" y="10" width="80" height="80" fill="url(#grad)" />
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        // Should count yellow and orange = 2 unique colors
        $this->assertEquals(2, $features['unique_colors']);
    }

    public function test_is_valid_color_method()
    {
        // Test with valid colors
        $this->assertTrue($this->service->isValidColor('red'));
        $this->assertTrue($this->service->isValidColor('#ff0000'));
        $this->assertTrue($this->service->isValidColor('#f00'));
        $this->assertTrue($this->service->isValidColor('rgb(255,0,0)'));
        $this->assertTrue($this->service->isValidColor('rgba(255,0,0,0.5)'));

        // Test with invalid colors
        $this->assertFalse($this->service->isValidColor('url(#gradient)'));
        $this->assertFalse($this->service->isValidColor('#reference'));
        $this->assertFalse($this->service->isValidColor('not-a-color'));
    }

    public function test_path_commands_are_counted_correctly()
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M10,10 L20,20 Z" />
            <path d="M30,30 C40,40 50,50 60,60 Z" />
        </svg>
        SVG;

        $features = $this->service->extractFeatures($svg);

        // M, L, Z + M, C, Z = 6 commands
        $this->assertEquals(6, $features['path_commands']);
    }
}
