<?php

if (!defined('ABSPATH')) {
    exit;
}

class CustomAjaxHandler
{
    public function __construct()
    {
        add_action('wp_ajax_nopriv_submit_feedback', [$this, 'handle_feedback_submission']);
        add_action('wp_ajax_submit_feedback', [$this, 'handle_feedback_submission']);
        add_action('wp_ajax_get_entries', [$this, 'fetch_entries']);
        add_action('wp_ajax_get_entry_details', [$this, 'fetch_entry_details']);
    }

    public function handle_feedback_submission()
    {
        check_ajax_referer('cfp_nonce', 'security');

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_feedback_entries';

        $data = [
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name'  => sanitize_text_field($_POST['last_name']),
            'email'      => sanitize_email($_POST['email']),
            'subject'    => sanitize_text_field($_POST['subject']),
            'message'    => sanitize_textarea_field($_POST['message']),
        ];

        $wpdb->insert($table_name, $data);

        wp_send_json_success(['message' => __('Thank you for your feedback!', 'custom-form')]);
    }

    public function fetch_entries()
    {
        check_ajax_referer('cfp_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('You are not authorized to view the content of this page.', 'custom-form')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_feedback_entries';

        // Get the requested page number from AJAX
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = 10; // Number of entries per page
        $offset = ($page - 1) * $limit;

        // Get the total number of entries
        $total_entries = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $total_pages = ceil($total_entries / $limit);

        // Fetch the paginated entries
        $entries = $wpdb->get_results(
            $wpdb->prepare("SELECT id, first_name, last_name, email, subject FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d", $limit, $offset)
        );

        wp_send_json_success([
            'entries' => $entries,
            'total_pages' => $total_pages,
            'current_page' => $page
        ]);
    }

    public function fetch_entry_details()
    {
        check_ajax_referer('cfp_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('You are not authorized to view the content of this page.', 'custom-form')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_feedback_entries';
        $entry_id = intval($_POST['id']);

        $entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $entry_id));

        if ($entry) {
            wp_send_json_success($entry);
        } else {
            wp_send_json_error(['message' => __('Entry not found', 'custom-form')]);
        }
    }
}

new CustomAjaxHandler();
