<?php

namespace LumenPress\ACF;

use LumenPress\ACF\Fields\Field;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    public function boot()
    {
        $this->loadConfiguration();
        $this->registerOptionsPages();
        $this->registerFields();
    }

    /**
     * Check if we are running Lumen or not.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        foreach (glob(__DIR__.'/../config/wp/*.php') as $file) {
            $this->mergeConfigFrom($file, 'wp/'.basename($file, '.php'));
        }
    }

    protected function registerOptionsPages()
    {
        if (! function_exists('add_action')) {
            return;
        }
        add_action('after_setup_theme', function () {
            if (wp_installing() and 'wp-activate.php' !== $GLOBALS['pagenow']) {
                return;
            }

            if (! function_exists('acf_add_options_page')) {
                return;
            }

            if (! class_exists('acf_pro_options_page')) {
                acf_include('pro/admin/options-page.php');
            }

            foreach ((array) config('wp/acf.options_pages') as $menu_slug => $settings) {
                $settings['menu_slug'] = $menu_slug;
                $sub_menu = array_pull($settings, 'sub_menu');
                $parent = acf_add_options_page($settings);
                if (is_array($sub_menu)) {
                    foreach ($sub_menu as $sub_menu_slug => $sub_settings) {
                        $sub_settings['menu_slug'] = $sub_menu_slug;
                        $sub_settings['parent_slug'] = $parent['menu_slug'];
                        acf_add_options_sub_page($sub_settings);
                    }
                }
            }
        });
    }

    protected function registerFields()
    {
        foreach (config('wp/acf.fields') as $type => $field) {
            Field::register($type, $field);
        }
    }
}
