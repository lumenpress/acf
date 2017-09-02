<?php 

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Fields
    |--------------------------------------------------------------------------
    |
    | Field extension
    |
    */
    'fields' => [
        'clone' => Lumenpress\ACF\Fields\CloneField::class,
        'flexible' => Lumenpress\ACF\Fields\FlexibleContent::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | options pages
    |--------------------------------------------------------------------------
    |
    | @menu: acf_add_options_page
    | @sub_menu: acf_add_options_sub_page
    |
    | https://www.advancedcustomfields.com/resources/options-page/
    */
    'options_pages' => [

        'theme-settings' => [
            'page_title'    => 'Theme General Settings',
            'menu_title'    => 'Theme Settings',
            'capability'    => 'edit_posts',
            'redirect'  => false,
            'sub_menu' => [
                // 'social-settings' => array(
                //     'page_title'    => 'Submenu Settings',
                //     'menu_title'    => 'Submenu Settings',
                // )
            ]
        ],

    ],

];
