<?php

namespace App\Services\Svg;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class SvgAnalysisService
{
    /**
     * The SVG namespace URI.
     */
    protected const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';

    /**
     * Extracts the features used in the given SVG string.
     *
     * @param  string  $svgString  The SVG string to analyze.
     * @return array<string, mixed> An associative array of feature names and their counts or values.
     */
    public function extractFeatures(?string $svgString): array
    {
        if (is_null($svgString)) {
            return [];
        }

        $dom = $this->loadSvgDom($svgString);

        if (! $dom) {
            return [];
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('svg', self::SVG_NAMESPACE);

        // Check if the SVG is... an SVG
        if ($xpath->query('//svg:svg')->length === 0) {
            return [];
        }

        $dimensions = $this->getSvgDimensions($dom);

        $features = [
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,

            // Basic element counts
            'total_shapes' => $this->countTotalShapes($xpath),
            'rectangles' => $xpath->query('//svg:rect')->length,
            'circles' => $xpath->query('//svg:circle')->length,
            'ellipses' => $xpath->query('//svg:ellipse')->length,
            'lines' => $xpath->query('//svg:line')->length,
            'polygons' => $xpath->query('//svg:polygon')->length,
            'paths' => $xpath->query('//svg:path')->length,
            'groups' => $xpath->query('//svg:g')->length,

            // Advanced visual features
            'gradients' => $xpath->query('//svg:linearGradient | //svg:radialGradient')->length,
            'patterns' => $xpath->query('//svg:pattern')->length,
            'filters' => $xpath->query('//svg:filter')->length,
            'masks' => $xpath->query('//svg:mask')->length,
            'clip_paths' => $xpath->query('//svg:clipPath')->length,

            // Colors
            'unique_colors' => $this->countUniqueColors($xpath),

            // Animation and interactivity
            'animations' => $xpath->query('//svg:animate | //svg:animateMotion | //svg:animateTransform | //svg:set')->length,
            'elements_with_transform' => $xpath->query('//*[@transform]')->length,

            // Complexity metrics
            'path_commands' => $this->countPathCommands($xpath),
            'max_group_nesting' => $this->getMaxGroupNesting($xpath),
            'defs_elements' => $xpath->query('//svg:defs/*')->length,
            'use_elements' => $xpath->query('//svg:use')->length,

            // Special effects
            'elements_with_opacity' => $xpath->query('//*[@opacity]')->length,
            'elements_with_stroke' => $xpath->query('//*[@stroke]')->length,
            'text_elements' => $xpath->query('//svg:text')->length,
        ];

        return collect($features)
            ->reject(fn ($value) => is_null($value) || $value === 0)
            ->toArray();
    }

    /**
     * Returns english human readable names for the features with descriptions.
     */
    public function getFeatureDescriptions(?array $features): array
    {
        return $this->combineFeatures($features, [
            'width' => [
                'name' => 'Width',
                'category' => 'General',
                'description' => 'The width of the SVG viewBox or canvas.',
            ],
            'height' => [
                'name' => 'Height',
                'category' => 'General',
                'description' => 'The height of the SVG viewBox or canvas.',
            ],
            'total_shapes' => [
                'name' => 'Total Shapes',
                'category' => 'Shapes',
                'description' => 'The total number of shape elements (rect, circle, ellipse, line, polygon, polyline, path) in the SVG.',
            ],
            'rectangles' => [
                'name' => 'Rectangles',
                'category' => 'Shapes',
                'description' => 'The number of rectangle elements (rect) in the SVG.',
            ],
            'circles' => [
                'name' => 'Circles',
                'category' => 'Shapes',
                'description' => 'The number of circle elements (circle) in the SVG.',
            ],
            'ellipses' => [
                'name' => 'Ellipses',
                'category' => 'Shapes',
                'description' => 'The number of ellipse elements (ellipse) in the SVG.',
            ],
            'lines' => [
                'name' => 'Lines',
                'category' => 'Shapes',
                'description' => 'The number of line elements (line) in the SVG.',
            ],
            'polygons' => [
                'name' => 'Polygons',
                'category' => 'Shapes',
                'description' => 'The number of polygon elements (polygon) in the SVG.',
            ],
            'paths' => [
                'name' => 'Paths',
                'category' => 'Shapes',
                'description' => 'The number of path elements (path) in the SVG.',
            ],
            'groups' => [
                'name' => 'Groups',
                'category' => 'Shapes',
                'description' => 'The number of group elements (g) in the SVG.',
            ],
            'gradients' => [
                'name' => 'Gradients',
                'category' => 'Visual Effects',
                'description' => 'The number of gradient definitions (linearGradient, radialGradient) in the SVG.',
            ],
            'patterns' => [
                'name' => 'Patterns',
                'category' => 'Visual Effects',
                'description' => 'The number of pattern definitions (pattern) in the SVG.',
            ],
            'filters' => [
                'name' => 'Filters',
                'category' => 'Visual Effects',
                'description' => 'The number of filter definitions (filter) in the SVG.',
            ],
            'masks' => [
                'name' => 'Masks',
                'category' => 'Visual Effects',
                'description' => 'The number of mask definitions (mask) in the SVG.',
            ],
            'clip_paths' => [
                'name' => 'Clip Paths',
                'category' => 'Visual Effects',
                'description' => 'The number of clipping path definitions (clipPath) in the SVG.',
            ],
            'unique_colors' => [
                'name' => 'Unique Colors',
                'category' => 'Colors',
                'description' => 'The number of unique colors used in the SVG.',
            ],
            'animations' => [
                'name' => 'Animations',
                'category' => 'Interactivity',
                'description' => 'The number of animation elements (animate, animateMotion, animateTransform, set) in the SVG.',
            ],
            'elements_with_transform' => [
                'name' => 'Elements with Transform',
                'category' => 'Interactivity',
                'description' => 'The number of elements with transform attributes in the SVG.',
            ],
            'path_commands' => [
                'name' => 'Path Commands',
                'category' => 'Complexity',
                'description' => 'The total number of path commands in all path elements (M, L, C, Q, etc.) in the SVG.',
            ],
            'max_group_nesting' => [
                'name' => 'Max Group Nesting',
                'category' => 'Complexity',
                'description' => 'The maximum nesting level of group elements (g) in the SVG.',
            ],
            'defs_elements' => [
                'name' => 'Defs Elements',
                'category' => 'Complexity',
                'description' => 'The number of elements defined within the defs element in the SVG.',
            ],
            'use_elements' => [
                'name' => 'Use Elements',
                'category' => 'Complexity',
                'description' => 'The number of use elements (use) in the SVG.',
            ],
            'elements_with_opacity' => [
                'name' => 'Elements with Opacity',
                'category' => 'Visual Effects',
                'description' => 'The number of elements with opacity attributes in the SVG.',
            ],
            'elements_with_stroke' => [
                'name' => 'Elements with Stroke',
                'category' => 'Visual Effects',
                'description' => 'The number of elements with stroke attributes in the SVG.',
            ],
            'text_elements' => [
                'name' => 'Text Elements',
                'category' => 'Text',
                'description' => 'The number of text elements (text) in the SVG.',
            ],
        ]);
    }

    /**
     * Combines the features with their descriptions.
     *
     * @param  array<string, mixed>  $features  The features to combine.
     * @param  array<string, array<string, mixed>>  $descriptions  The descriptions for each feature.
     * @return array<string, array<string, mixed>> The combined features with descriptions.
     */
    protected function combineFeatures(?array $features, array $descriptions): array
    {
        if (is_null($features)) {
            return [];
        }

        return collect($descriptions)
            ->map(fn ($description, $key) => [
                'name' => $description['name'],
                'category' => $description['category'],
                'description' => $description['description'],
                'value' => $features[$key] ?? null,
            ])
            ->toArray();
    }

    /**
     * Loads the SVG string into a DOMDocument.
     *
     * @param  string  $svgString  The SVG string to load.
     * @return DOMDocument|null The loaded DOM document or null if invalid.
     */
    protected function loadSvgDom(string $svgString): ?DOMDocument
    {
        $dom = new DOMDocument;

        // Disable error output when loading potentially malformed XML
        libxml_use_internal_errors(true);
        $result = $dom->loadXML($svgString);
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        return $result ? $dom : null;
    }

    /**
     * Counts the total number of shape elements in the SVG.
     *
     * @param  DOMXPath  $xpath  The XPath object for the SVG.
     * @return int The total number of shapes.
     */
    protected function countTotalShapes(DOMXPath $xpath): int
    {
        // In SVG spec, shapes are rect, circle, ellipse, line, polygon, polyline, and path
        // Text elements are not considered shapes in our implementation
        $shapeElements = ['rect', 'circle', 'ellipse', 'line', 'polygon', 'polyline', 'path'];

        return collect($shapeElements)
            ->sum(fn ($element) => $xpath->query("//svg:$element")->length);
    }

    /**
     * Counts unique colors used in the SVG.
     *
     * @param  DOMXPath  $xpath  The XPath object for the SVG.
     * @return int The number of unique colors.
     */
    protected function countUniqueColors(DOMXPath $xpath): int
    {
        $colors = [];
        $colorMap = [
            'red' => '#ff0000',
            'green' => '#008000',
            'blue' => '#0000ff',
            'yellow' => '#ffff00',
            'purple' => '#800080',
            'cyan' => '#00ffff',
            'magenta' => '#ff00ff',
            'black' => '#000000',
            'white' => '#ffffff',
            'gray' => '#808080',
            'grey' => '#808080',
            'orange' => '#ffa500',
        ];

        // Extract fill colors
        foreach ($xpath->query('//*[@fill]') as $element) {
            $fill = $element->getAttribute('fill');
            if ($fill !== 'none' && $this->isValidColor($fill)) {
                $color = strtolower($fill);
                // Normalize named colors to hex
                if (isset($colorMap[$color])) {
                    $color = $colorMap[$color];
                }
                $colors[] = $color;
            }
        }

        // Extract stroke colors
        foreach ($xpath->query('//*[@stroke]') as $element) {
            $stroke = $element->getAttribute('stroke');
            if ($this->isValidColor($stroke)) {
                $color = strtolower($stroke);
                // Normalize named colors to hex
                if (isset($colorMap[$color])) {
                    $color = $colorMap[$color];
                }
                $colors[] = $color;
            }
        }

        // Extract stop colors in gradients
        foreach ($xpath->query('//svg:stop[@stop-color]') as $stop) {
            $stopColor = $stop->getAttribute('stop-color');
            if ($this->isValidColor($stopColor)) {
                $color = strtolower($stopColor);
                // Normalize named colors to hex
                if (isset($colorMap[$color])) {
                    $color = $colorMap[$color];
                }
                $colors[] = $color;
            }
        }

        return count(array_unique($colors));
    }

    /**
     * Checks if a string represents a valid color.
     *
     * @param  string  $color  The color string to check.
     * @return bool Whether the string is a valid color.
     */
    public function isValidColor(string $color): bool
    {
        // Skip URLs and references
        if (Str::startsWith($color, ['url(', '#reference'])) {
            return false;
        }

        // Check for named colors
        if (preg_match('/^[a-zA-Z]+$/', $color)) {
            return true;
        }

        // Check for hex colors
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color)) {
            return true;
        }

        // Check for RGB/RGBA colors
        if (preg_match('/^rgb(a)?\(\s*(\d{1,3}\s*,\s*){2}\d{1,3}(\s*,\s*\d*\.?\d+)?\s*\)$/', $color)) {
            return true;
        }

        return false;
    }

    /**
     * Counts the number of path commands in all paths.
     *
     * @param  DOMXPath  $xpath  The XPath object for the SVG.
     * @return int The total number of path commands.
     */
    protected function countPathCommands(DOMXPath $xpath): int
    {
        $totalCommands = 0;

        foreach ($xpath->query('//svg:path[@d]') as $path) {
            $d = $path->getAttribute('d');
            // Count commands (M, L, C, Q, etc.)
            preg_match_all('/[A-Za-z]/', $d, $matches);
            $totalCommands += count($matches[0]);
        }

        return $totalCommands;
    }

    /**
     * Determines the maximum nesting level of groups.
     *
     * @param  DOMXPath  $xpath  The XPath object for the SVG.
     * @return int The maximum nesting level.
     */
    protected function getMaxGroupNesting(DOMXPath $xpath): int
    {
        $maxDepth = 0;

        foreach ($xpath->query('//svg:g') as $group) {
            $depth = 0;
            $parent = $group;

            while (($parent = $parent->parentNode) && $parent->nodeName !== 'svg') {
                if ($parent->nodeName === 'g') {
                    $depth++;
                }
            }

            $maxDepth = max($maxDepth, $depth);
        }

        return $maxDepth;
    }

    /**
     * Gets the width and height from the SVG's viewBox or width/height attributes.
     *
     * @param  DOMDocument  $dom  The SVG DOM document.
     * @return array<int|null> The width and height, or null if not available.
     */
    protected function getSvgDimensions(DOMDocument $dom): array
    {
        $svg = $dom->documentElement;
        $width = null;
        $height = null;

        if ($svg) {
            // Try viewBox first
            if ($svg->hasAttribute('viewBox')) {
                $viewBox = $svg->getAttribute('viewBox');
                $parts = preg_split('/[\s,]+/', trim($viewBox));

                if (count($parts) === 4) {
                    $width = (int) $parts[2];
                    $height = (int) $parts[3];
                }
            }
            // Fall back to width/height attributes
            elseif ($svg->hasAttribute('width') && $svg->hasAttribute('height')) {
                $width = (int) $svg->getAttribute('width');
                $height = (int) $svg->getAttribute('height');
            }
        }

        return [$width, $height];
    }
}
