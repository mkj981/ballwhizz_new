<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */
    'title' => 'Ballwhizz Admin Panel',
    'title_prefix' => 'Ballwhizz',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    */
    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    */
    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    */
    'logo' => '<b>Ball</b>whizz',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    */
    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    */
    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    */
    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    */
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    */
    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    */
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    */
    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    */
    'menu' => [
        ['type' => 'navbar-search', 'text' => 'search', 'topnav_right' => true],
        ['type' => 'fullscreen-widget', 'topnav_right' => true],
        ['type' => 'sidebar-menu-search', 'text' => 'search'],

        // 🏠 Dashboard
        ['text' => 'Dashboard', 'url' => 'admin/dashboard', 'icon' => 'fas fa-tachometer-alt'],

        // 👥 USERS SECTION (collapsible)
        [
            'text' => 'Users Management',
            'icon' => 'fas fa-users',
            'submenu' => [
                ['text' => 'Roles Management', 'url' => 'admin/roles', 'icon' => 'fas fa-user-shield'],
                ['text' => 'Admins', 'url' => 'admin/admins', 'icon' => 'fas fa-users-cog'],
                ['text' => 'Users', 'url' => 'admin/users', 'icon' => 'fas fa-user'],
            ],
        ],

        // ⚙️ APP MANAGEMENT SECTION
        [
            'text' => 'App Management',
            'icon' => 'fas fa-cogs',
            'submenu' => [
                ['text' => 'API Types', 'url' => 'admin/api-types', 'icon' => 'fas fa-code'],
                ['text' => 'Positions', 'url' => 'admin/positions', 'icon' => 'fas fa-user-shield'],
                ['text' => 'Continents', 'url' => 'admin/continents', 'icon' => 'fas fa-globe'],
                ['text' => 'Countries', 'url' => 'admin/countries', 'icon' => 'fas fa-flag'],
                ['text' => 'Leagues', 'url' => 'admin/leagues', 'icon' => 'fas fa-trophy'],
                ['text' => 'Seasons', 'url' => 'admin/seasons', 'icon' => 'fas fa-calendar-alt'],
                ['text' => 'Season Teams', 'url' => 'admin/season-teams', 'icon' => 'fas fa-people-arrows'],
                ['text' => 'Teams', 'url' => 'admin/teams', 'icon' => 'fas fa-futbol'],
                ['text' => 'Venues', 'url' => 'admin/venues', 'icon' => 'fas fa-landmark'],
                ['text' => 'TV Stations', 'url' => 'admin/tv-stations', 'icon' => 'fas fa-tv'],
                ['text' => 'News', 'url' => 'admin/news', 'icon' => 'fas fa-newspaper'],
            ],
        ],

        // 🎴 CARDS MANAGEMENT SECTION
        [
            'text' => 'Cards & Predictions',
            'icon' => 'fas fa-id-card',
            'submenu' => [
                ['text' => 'Boxes Types', 'url' => 'admin/boxes-types', 'icon' => 'fas fa-gift'],
                ['text' => 'Card Types', 'url' => 'admin/card-types', 'icon' => 'fas fa-clone'],
                ['text' => 'Ballwhizz Weeks', 'url' => 'admin/weekmonths', 'icon' => 'fas fa-calendar-week'],
                ['text' => 'Cards Weeks', 'url' => 'admin/cards-weeks', 'icon' => 'fas fa-calendar-alt'],
                ['text' => 'Prediction Matches', 'url' => 'admin/prediction-matches', 'icon' => 'fas fa-futbol'],
                ['text' => 'Players', 'url' => 'admin/players', 'icon' => 'fas fa-user-friends'],
                ['text' => 'Players Cards', 'url' => 'admin/players-cards', 'icon' => 'fas fa-id-card-alt'],
                ['text' => 'User Cards', 'url' => 'admin/user-cards', 'icon' => 'fas fa-address-card'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    */
    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    */
    'plugins' => [

        'Datatables' => [
            'active' => false,
            'files' => [
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'],
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css'],
            ],
        ],

        // ✅ Modern Select2 plugin with Bootstrap 5 theme
        'Select2' => [
            'active' => true,
            'files' => [
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css'],
            ],
        ],

        'Chartjs' => [
            'active' => false,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js'],
            ],
        ],

        'Sweetalert2' => [
            'active' => false,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8'],
            ],
        ],

        'Pace' => [
            'active' => false,
            'files' => [
                ['type' => 'css', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css'],
                ['type' => 'js',  'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame Mode
    |--------------------------------------------------------------------------
    */
    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire Support
    |--------------------------------------------------------------------------
    */
    'livewire' => true,
];
