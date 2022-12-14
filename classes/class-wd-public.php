<?php
/**
 * For forntend assets and methods
 * 
 * @author ronym <ronymaha@gmail.com>
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WD_Public')) {
    /**
     * Public use
     * 
     * @author ronym <ronymaha@gmail.com>
     */
    class WD_Public
    {
        protected $token;
        /**
         * Initial callback
         */
        public function __construct()
        {
            $this->token = WD_TOKEN;
            add_shortcode('applicant_form', array($this, 'wdApplicationFormCallback'));
            add_action('wp_enqueue_scripts', array($this, 'wdAplicationCSS'));
        }

        /**
         * Register css for frontend
         * 
         * @access public
         */
        public function wdAplicationCSS()
        {
            wp_enqueue_style($this->token . 'application_css', esc_url(WD_Features::$assets_url) . 'css/application.css', array(), time(), 'all');
        }


        /**
         * Application form validation after form submission
         * 
         * @return string
         * 
         * @access public
         */
        public function wdFormValidation($posts)
        {
            $error = new WP_Error();
            if (!wp_verify_nonce($posts['wd_nonce'], 'wdapplication')) {
                $error->add('wd_error', __('Something wrong, please try again.', 'wedevs'));
            }


            // First Name Validateion
            if (!ctype_alpha($posts['first_name'])) {
                $error->add('invalid_first_name', __('Invalid first name entered', 'wedevs'));
            }

            //Email Validateion
            if (!is_email($posts['email'])) {
                $error->add('invalid_email', __('Email is not valid', 'wedevs'));
            }


            // if phone number isn't numeric, throw an error
            if (!is_numeric($posts['mobile'])) {
                $error->add('invalid_mobile', __('Mobile is not numbers', 'wedevs'));
            }

            return apply_filters('wedevs_validation_error', $error, $posts);
        }


        /**
         * Form data process and store to DB
         * 
         * @param $posts array
         * @param $files array
         * 
         * @access public
         */
        public function wdFormProcess($posts, $files)
        {
            global $wderror, $wpdb;
            $validateion = $this->wdFormValidation($posts);

            if (count($validateion->get_error_messages()) > 0) {
                $wderror = $validateion->get_error_messages();
                return;
            }


            $_forInsert = array(
                'first_name' => $posts['first_name'],
                'last_name' => $posts['last_name'],
                'present_address' => $posts['present_address'],
                'email' => $posts['email'],
                'mobile' => $posts['mobile'],
                'post_name' => $posts['post_name']
            );

            //File upload to upload directory
            if (isset($files['cv']) && isset($files['cv']['name'])) {
                $file = wp_upload_bits($files['cv']['name'], null, @file_get_contents($files['cv']['tmp_name']));
                if ($file && isset($file['error']) && empty($file['error'])) {
                    $_forInsert['cv'] = $file['url'];
                }
            }

            //Insert data to DB
            $insert = $wpdb->insert(
                WD_Features::$table_name,
                $_forInsert
            );

            if ($insert) {
                $_POST = array();
                return true;
            }
        }

        /**
         * Shortcode callback 
         * 
         * @access public
         * @return html
         */
        public function wdApplicationFormCallback()
        {
            if (is_admin())
                return;


            $formProcess = false;
            if (isset($_REQUEST['application_submit']))
                $formProcess = $this->wdFormProcess($_REQUEST, $_FILES);

            //Before Application Form
            do_action('wedevs_before_application_form');

            include_once plugin_dir_path(WD_FILE) . '/view/form.php';

            // After Application Form
            do_action('wedevs_after_application_form');
        }
    }
}