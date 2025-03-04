Functions.php

// LANDING PAGE POST TYPE
function register_landing_pages_cpt() {
    register_post_type('landing_page', array(
        'labels' => array(
            'name' => 'Landing Pages',
            'singular_name' => 'Landing Page',
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'landing-page', 'with_front' => false),
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
    ));
}
add_action('init', 'register_landing_pages_cpt');

include_once get_template_directory() . '/inc/class-remove-slug-from-cpt.php';
