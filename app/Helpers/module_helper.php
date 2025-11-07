<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: eprmembership
 * Date: 03/09/2024
 * Time: 02:02
 */

// File: app/Helpers/module_helper.php

if (!function_exists('module_view')) {
    /**
     * Load a view from a specific module.
     *
     * @param string $module The name of the module
     * @param string $view The view file within the module's Views directory
     * @param array $data Optional data array to pass to the view
     * @param array $options Optional options for the view loader
     *
     * @return string
     */
    function module_view($module, $view, $data = [], $options = [])
    {
        $path = "App\Modules\\$module\Views\\$view";
        return view($path, $data, $options);
    }
}
