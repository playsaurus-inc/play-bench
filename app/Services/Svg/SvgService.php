<?php

namespace App\Services\Svg;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class SvgService
{
    /**
     * Clean up SVG content to ensure it's valid.
     */
    public function cleanupSvg(string $svgString): string
    {
        return Str::of($svgString)
            ->trim()
            ->replaceMatches('/```svg\n?|```\n?/', '') // Remove markdown code block syntax (```svg ... ```)
            ->replaceMatches('/< ([a-zA-Z])/', '<$1') // Fix spaces in tag names (e.g., "< defs>" to "<defs>")
            ->replaceMatches('/<([a-zA-Z0-9]+)\/>/', '<$1 />') // Fix self-closing tags that might be malformed
            ->pipe(fn ($string) => $this->ensureSvgNamespace($string))
            ->pipe(fn ($string) => $this->ensureViewBox($string)) // Ensure a viewBox is present
            // string concatenation to avoid IDE highlighting issues
            ->replace('xmlns:'.'link="http://www.w3.org/1999/xlink"', '') // Remove deprecated xlink namespace if declared
            ->replace('xlink:'.'href=', 'href=') // Replace xlink:href with href for modern compatibility
            ->toString();
    }

    /**
     * Ensure the SVG namespace is present.
     */
    protected function ensureSvgNamespace(Stringable $string): Stringable
    {
        if (
            $string->contains('<svg', ignoreCase: true)
            && ! $string->contains('xmlns="http://www.w3.org/2000/svg"', ignoreCase: true)
        ) {
            return $string->replaceMatches('/<svg/', '<svg xmlns="http://www.w3.org/2000/svg"', 1);
        }

        return $string;
    }

    /**
     * Ensure a viewBox is present in the SVG.
     */
    protected function ensureViewBox(Stringable $string): Stringable
    {
        if (
            $string->contains('<svg', ignoreCase: true)
            && ! $string->contains('viewBox', ignoreCase: true)
        ) {
            return $string->replaceMatches('/<svg/', '<svg viewBox="0 0 300 300"', 1);
        }

        return $string;
    }

    /**
     * Converts a file to a data URL string.
     */
    protected function toDataUrl(string $mime, string $contents): string
    {
        return "data:$mime;base64,".base64_encode($contents);
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
        $resvgJs = config('playbench.resvg_js_cli_path')
            ?? base_path('node_modules/.bin/resvg-js-cli');

        $inputFile = storage_path('app/temp/' . Str::random(40) . '.svg');
        $outputFile = storage_path('app/temp/' . Str::random(40) . '.png');

        file_put_contents($inputFile, $svgString);

        try {
            Process::run([
                $resvgJs,
                '--fit-width', $width,
                '--fit-height', $height,
                '--background', 'transparent',
                $inputFile,
                $outputFile
            ])->throw();

            return file_get_contents($outputFile);
        } finally {
            if (file_exists($inputFile)) unlink($inputFile);
            if (file_exists($outputFile)) unlink($outputFile);
        }
    }
}
