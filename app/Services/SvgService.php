<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
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
        $cleaned = preg_replace('/<([a-zA-Z0-9]+)\/>/', '<$1 />', $cleaned);

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
     * Converts a file to a data URL string.
     */
    protected function toDataUrl(string $mime, string $contents): string
    {
        return "data:$mime;base64," . base64_encode($contents);
    }

    /**
     * Convert SVG to a PNG data URL.
     */
    public function svgToPngDataUrl(string $svgString, int $width, int $height): string
    {
        $svgString = $this->cleanupSvg($svgString);

        $pngBinary = $this->convertSvgToPng($svgString, $width, $height);

        return $this->toDataUrl('image/png', $pngBinary);
    }

    /**
     * Convert SVG to PNG binary data.
     */
    protected function convertSvgToPng(string $svgString, int $width, int $height): string
    {
        $path = config('playbench.svg2png_path')
            ?? base_path('node_modules/.bin/svg2png');

        $outputFile = storage_path('app/temp/' . Str::random(40) . '.png');
        $inputFile = storage_path('app/temp/' . Str::random(40) . '.svg');
        file_put_contents($inputFile, $svgString);

        try {
            Process::run([
                $path,
                $inputFile,
                "--output=$outputFile",
                "--width=$width",
                "--height=$height",
            ])->throw();

            $pngBinary = file_get_contents($outputFile);
            return $pngBinary;
        } finally {
            // Clean up temporary files
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }
            if (file_exists($inputFile)) {
                unlink($inputFile);
            }
        }
    }
}
