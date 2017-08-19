<?php 

if (!function_exists('lumenpress_is_url')) {
    function lumenpress_is_url($value)
    {
        return preg_match('@^//@', $value) or filter_var($value, FILTER_VALIDATE_URL) !== false;
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

function _lumenpress_asset_uniqid($src)
{
    global $wpdb;
    return (int)$wpdb->get_var("select post_id from {$wpdb->postmeta} 
    where meta_key = '_lumenpress_asset_uniqid' and meta_value = '{$src}' ");
}

function lumenpress_insert_asset($src, $force = false)
{
    if (!$force and $id = _lumenpress_asset_uniqid($src)) {
        return $id;
    }

    // gives us access to the download_url() and wp_handle_sideload() functions
    if (!function_exists('media_handle_upload')) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    }

    // URL to the WordPress logo
    //$url = get_template_directory_uri() . '/' . $src;
    //$tmp = download_url( $url );
    $tmp = null;
    if (lumenpress_is_url($src)) {
        $tmp = download_url($src, 5000);
    } else {
        $url = file_exists($src) ? $src : lumenpress_asset_url($src);
        $tmp = wp_tempnam($url);
        @copy($url, $tmp);
    }
    // clearing the stat cache
    clearstatcache(true, $tmp);
    $file_array = array(
        'name' => basename($src),
        'tmp_name' => $tmp,
    );
    /**
     * Check for download errors
     * if there are error unlink the temp file name
     */
    if (is_wp_error($tmp)) {
        //@unlink( $file_array[ 'tmp_name' ] );
        return $tmp;
    }

    /**
     * now we can actually use media_handle_sideload
     * we pass it the file array of the file to handle
     * and the post id of the post to attach it to
     * $post_id can be set to '0' to not attach it to any particular post
     */
    $post_id = 0;

    $id = media_handle_sideload($file_array, $post_id);

    /**
     * We don't want to pass something to $id
     * if there were upload errors.
     * So this checks for errors
     */
    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return $id;
    }

    add_post_meta($id, '_lumenpress_asset_uniqid', $src, true);

    @unlink($file_array['tmp_name']);

    return $id;
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

if (function_exists('luemnpress_get_the_content')) {
    function luemnpress_get_the_content($value)
    {
        if (function_exists('apply_filters')) {
            return apply_filters('the_content', $value);
        }
        return $value;
    }
}