<?php

namespace App\Support;

class GitHelper
{
    /**
     * Gets the current release tag.
     */
    public static function getRelease(): ?string
    {
        try {
            // Use the latest tag as the release version
            return trim(exec('git describe --abbrev=0 --tags'));
        } catch (\Exception) {
            return null;
        }
    }
}
