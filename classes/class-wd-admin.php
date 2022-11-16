<?php
/**
 * Backend methods for wedevs plugins
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WD_Admin')) {
    /**
     * For admin use only
     * 
     * @author ronym <ronymaha@gmail.com>
     */
    class WD_Admin
    {
        protected $_token;

        public $hook_suffix = array();

        /**
         * Initial callback
         */
        public function __construct()
        {
            array_push($this->hook_suffix, 'dashboard');
            $this->_token = WD_TOKEN;
            add_action('admin_enqueue_scripts', array($this, 'adminEnqueueStyles'), 10, 1);

            //Register Admin menu
            add_action('admin_menu', array($this, 'wdAdminMenuPage'));

            //Register Dashboard widget
            add_action('wp_dashboard_setup', array($this, 'wdDashboardWidgetsRegister'), 2, 11 );
        }


        /**
         * Register dashboard widget 
         */
        public function wdDashboardWidgetsRegister()
        {
            wp_add_dashboard_widget('dashboard_widget', __('WeDevs Applications', 'wedevs'), array($this, 'wdDashboardWidgetComponent'));
        }


        /**
         * Get Application Data
         * 
         * @access private
         */
        protected function getApplicationData()
        {
            global $wpdb;
            $query = $wpdb->prepare('SELECT * FROM `' . WD_Features::$table_name . '` ORDER BY `submission_date` ASC LIMIT 5');
            $results = $wpdb->get_results($query, OBJECT);
            return apply_filters('wd_widget_data', $results);
        }   


        /**
         * Dashboard html output
         * 
         * @access public
         */
        public function wdDashboardWidgetComponent()
        {
                $db_applications = $this->getApplicationData();
                include plugin_dir_path(WD_FILE) . '/view/widget-list-table.php';
        }


        /**
         * Admin menu for display application lists
         * 
         * @access public
         * 
         */

        public function wdAdminMenuPage()
        {
            $this->hook_suffix[] = add_menu_page( 
                __('WeDevs Applications', 'wedevs'), 
                __('Applications', 'wedevs'), 
                'manage_options',
                'wedevs-opplications', 
                array($this, 'wdAdminPagesComponenet'), 
                'dashicons-list-view', 
                52 
            );
        }


        /**
         * Admin page content
         * 
         * @access public
         * 
         */
        public function wdAdminPagesComponenet()
        {
            $wd_application_lists = new WD_Application_Lists();

            echo sprintf('<div class="wrap"><h2>%s</h2>', __('Wedevs Applicatin Lists', 'wedevs'));
            echo sprintf('<form method="post">');
            $wd_application_lists->prepare_items();
            
            //Search Form 
            $wd_application_lists->search_box('search', 'search_id');

            // Display Lists
            $wd_application_lists->display();
            echo sprintf('</form></div>');
        }


        /**
         * Load admin CSS.
         *
         * @access public
         * @return css
         */
        public function adminEnqueueStyles()
        {            
            $screen = get_current_screen();

            if (!isset($this->hook_suffix) || empty($this->hook_suffix)) {    
                return;
            }
            
            if (in_array($screen->id, $this->hook_suffix, true)) {
                wp_register_style($this->_token . '-admin', esc_url(WD_Features::$assets_url) . 'css/backend.css', array(), time());
                wp_enqueue_style($this->_token . '-admin');
            }
        }
    }
}