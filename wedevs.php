<?php
/**
 * Plugin Name: Wedevs tests
 * Plugin URI: #
 * Description: Short description for wedevs test
 * Version: 1.0.0
 * Author: Omar Faruque
 * text-domain: wedevs
 *
 */


if (!defined('ABSPATH')) {
    exit;
}

define('WD_TOKEN', 'wd');
define('WD_PATH', plugin_dir_url(__FILE__));
define('WD_FILE', __FILE__);

require plugin_dir_path(__FILE__) . 'functions/autoloader.php';

// Load and set up the Autoloader
$wd_autoloader = new WD_Autoloader(dirname(__FILE__));
$wd_autoloader->register();


if (!class_exists('WD_Features')) {
    /**
     * Final class
     * Class for testing perpose 
     * 
     * @author Omar <ronymaha@gmail.com>
     */
    final class WD_Features
    {

        /* @var RM_Autoloader */
        protected static $autoloader;

        protected $token = WD_TOKEN;

        public static $assets_url = '';

        public static $table_name;


        /**
         * Initial callback funciton 
         * 
         * @access public
         */
        public function __construct()
        {
            global $wpdb;
            self::$table_name = $wpdb->prefix . 'applicant_submissions';
            self::$assets_url = esc_url(trailingslashit(plugins_url('/assets/', WD_FILE)));

            add_action('plugins_loaded', array($this, 'wdLoadAllDependences'));

            // Register Database Table
            add_action('init', array($this, 'wdRegisterDatabaseTable'));
        }


        /**
         * Register Database table for application form 
         */
        public function wdRegisterDatabaseTable()
        {
            global $wpdb;
            $_tablename = self::$table_name;

            // we need this to access the maybe_create_table function
            include_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $charset_collate = $wpdb->get_charset_collate();

            $columns_tasks = "
            (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `first_name` varchar(250) NOT NULL,
            `last_name` varchar(250) NOT NULL,
            `present_address` text NOT NULL,
            `email` varchar(250) NOT NULL,
            `mobile` varchar(250) NOT NULL,
            `post_name` varchar(250) NOT NULL,
            `cv` varchar(250) NOT NULL,
            `submission_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`ID`),
            KEY `email` (`email`)
            )
            ";

            //we are using this function so that if the table already exists, there are no complications
            maybe_create_table(self::$table_name, "CREATE TABLE {$_tablename} {$columns_tasks} {$charset_collate};");
        }


        /**
         * Load all necessar dependences 
         */
        public function wdLoadAllDependences()
        {
            new WD_Public();
            if (is_admin())
                new WD_Admin();
        }
    }
    new WD_Features();
}