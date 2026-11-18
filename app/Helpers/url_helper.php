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

        // Use a file version so browsers may cache assets safely, but always
        // request a fresh copy immediately after a deployment changes a file.
        $url = base_url('assets/' . $path);
        $asset = FCPATH . 'assets/' . $path;

        return is_file($asset) ? $url . '?v=' . filemtime($asset) : $url;
    }
}

if (!function_exists('mobile_asset_url')) {
    /** Load mobile visual assets from the current host, including staging previews. */
    function mobile_asset_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $url = '/assets/' . $path;
        $asset = FCPATH . 'assets/' . $path;

        return is_file($asset) ? $url . '?v=' . filemtime($asset) : $url;
    }
}
