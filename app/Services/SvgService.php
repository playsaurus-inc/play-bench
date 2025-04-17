<?php

namespace App\Services;

use Illuminate\Support\Str;

class SvgService
{
    /**
     * Clean up SVG content to ensure it's valid.
     */
    public function cleanupSvg(string $svgString): string
    {
        // Remove markdown code block syntax
        $cleaned = preg_replace('/```svg\n?|```\n?/', '', $svgString);

        // Fix spaces in tag names (e.g., "< defs>" to "<defs>")
        $cleaned = preg_replace('/< ([a-zA-Z])/', '<$1', $cleaned);

        // Fix self-closing tags that might be malformed
        $cleaned = preg_replace('/([a-zA-Z])\/>//', '$1 />', $cleaned);

        // Ensure proper XML structure
        if (!Str::contains($cleaned, 'xmlns="http://www.w3.org/2000/svg"') && Str::contains($cleaned, '<svg')) {
            $cleaned = preg_replace('/<svg/', '<svg xmlns="http://www.w3.org/2000/svg"', $cleaned);
        }

        // Make sure viewBox is properly defined
        if (!Str::contains($cleaned, 'viewBox') && Str::contains($cleaned, '<svg')) {
            $cleaned = preg_replace('/<svg/', '<svg viewBox="0 0 300 300"', $cleaned);
        }

        return $cleaned;
    }

    /**
     * Convert SVG to a data URL for use in image APIs.
     */
    public function svgToDataUrl(string $svgString): string
    {
        $cleanSvg = $this->cleanupSvg($svgString);
        return 'data:image/svg+xml;base64,' . base64_encode($cleanSvg);
    }

    /**
     * Convert SVG to a PNG data URL.
     * This is a placeholder for the actual implementation.
     */
    public function svgToPngDataUrl(string $svgString): string
    {
        // This is where you'll implement the SVG to PNG conversion with your third-party package
        // For now, we return the SVG as a data URL directly
        return $this->svgToDataUrl($svgString);
    }
}
