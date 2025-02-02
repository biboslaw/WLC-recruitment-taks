<?php

/**
 * Plugin Name:       Form
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-form
 *
 * @package CustomBlocks
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'inc/CustomFormDBHandler.php';
require_once plugin_dir_path(__FILE__) . 'inc/CustomAjaxHandler.php';

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function custom_blocks_custom_form_block_init()
{
    register_block_type(__DIR__ . '/build/custom-form');
    register_block_type(__DIR__ . '/build/custom-entries');
}
add_action('init', 'custom_blocks_custom_form_block_init');

// Initialize AJAX handlers 
new CustomAjaxHandler();

// Run database setup on plugin activation
register_activation_hook(__FILE__, ['CustomFormDBHandler', 'create_table']);

function custom_form_enqueue_scripts()
{
    if (has_block('custom-blocks/custom-form')) {
        wp_enqueue_script(
            'custom-form-js',
            plugin_dir_url(__FILE__) . 'assets/js/custom-form.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'custom-form-css',
            plugin_dir_url(__FILE__) . 'build/custom-form/style-index.css',
            [],
            '1.0.0'
        );

        // Sending user data (if logged in) to the frontned
        $current_user = wp_get_current_user();
        $user_data = [
            'ajax_url'   => admin_url('admin-ajax.php'),
            'security'   => wp_create_nonce('cfp_nonce'),
            'first_name' => is_user_logged_in() ? esc_html($current_user->first_name) : '',
            'last_name'  => is_user_logged_in() ? esc_html($current_user->last_name) : '',
            'email'      => is_user_logged_in() ? esc_html($current_user->user_email) : '',
        ];

        wp_localize_script('custom-form-js', 'cfp_user', $user_data);
    }

    if (has_block('custom-blocks/custom-entries')) {
        wp_enqueue_script(
            'custom-entries-js',
            plugin_dir_url(__FILE__) . 'assets/js/custom-entries.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('custom-entries-js', 'cfp_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('cfp_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'custom_form_enqueue_scripts');
