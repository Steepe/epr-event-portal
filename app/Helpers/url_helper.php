<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 18:39
 */


if (!function_exists('asset_url')) {
    /**
     * Returns the full URL to an asset file in the public/assets directory
     *
     * Example:
     *   echo asset_url('images/logo.png');
     *   // outputs: https://example.com/assets/images/logo.png
     */
    function asset_url(string $path = ''): string
    {
        // Ensure path doesn’t start with a slash
        $path = ltrim($path, '/');

        // Use base_url() which CI4 provides automatically
        return base_url('assets/' . $path);
    }
}
