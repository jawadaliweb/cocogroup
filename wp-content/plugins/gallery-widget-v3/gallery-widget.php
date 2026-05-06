<?php
/**
 * Plugin Name: ACF Filterable Gallery Widget
 * Description: Elementor widget for ACF Repeater-based filterable gallery with lightbox, masonry, and animations
 * Version: 3.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

function acf_gallery_assets() {
    // Use wp_enqueue_script (not wp_register_script) so scripts
    // are actually output on the page — not just registered.
    wp_enqueue_script('isotope-js',
        'https://unpkg.com/isotope-layout@3.0.6/dist/isotope.pkgd.min.js',
        ['jquery'], null, true);

    wp_enqueue_script('glightbox-js',
        'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/js/glightbox.min.js',
        [], null, true);

    wp_enqueue_style('glightbox-css',
        'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.min.css');

    wp_enqueue_style('acf-gallery-css',
        plugin_dir_url(__FILE__) . 'gallery.css');
}
// wp_enqueue_scripts                        — standard frontend pages
// elementor/frontend/after_enqueue_scripts  — Elementor preview / editor
add_action('wp_enqueue_scripts', 'acf_gallery_assets');
add_action('elementor/frontend/after_enqueue_scripts', 'acf_gallery_assets');

function register_gallery_widget($widgets_manager) {
    require_once(__DIR__ . '/widget.php');
    $widgets_manager->register(new \ACF_Gallery_Widget());
}
add_action('elementor/widgets/register', 'register_gallery_widget');
