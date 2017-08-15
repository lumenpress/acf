<?php 

return [

    'fields' => [
        'clone' => Lumenpress\Acf\Fields\CloneField::class,
        'flexible' => Lumenpress\Acf\Fields\FlexibleContent::class,
    ],

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
