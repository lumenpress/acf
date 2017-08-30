<?php 

if (!function_exists('lumenpress_is_url')) {
    function lumenpress_is_url($value)
    {
        return preg_match('@^//@', $value) or filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('lumenpress_url')) {
    function lumenpress_url($value)
    {
        if (lumenpress_is_url($value)) {
            return $value;
        }
        return function_exists('home_url') ? home_url($value) : url($value);
    }
}

if (!function_exists('lumenpress_asset_url')) {
    function lumenpress_asset_url($value)
    {
        if (lumenpress_is_url($value)) {
            return $value;
        }
        return config('wordpress.assets.base_url').$value;
    }
}

if (!function_exists('lumenpress_asset_path')) {
    function lumenpress_asset_path($value)
    {
        if (file_exists($value)) {
            return $value;
        }
        return config('wordpress.assets.base_path').$value;
    }
}

if (function_exists('lumenpress_get_attachment_url')) {
    function lumenpress_get_attachment_url($id = 0)
    {
        if (function_exists('wp_get_attachment_url')) {
            return wp_get_attachment_url($id);
        }
        return '';
    }
}

if (!function_exists('luemnpress_get_the_content')) {
    function luemnpress_get_the_content($value)
    {
        if (function_exists('apply_filters')) {
            return apply_filters('the_content', $value);
        }
        return $value;
    }
}