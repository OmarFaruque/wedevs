<?php
/**
 * Applications lists in admin page
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WD_Application_Lists')) {
    /**
     * Admin use only
     * 
     * @author ronym <ronymaha@gmail.com>
     */
    class WD_Application_Lists extends WP_List_Table
    {

        private $_table_data;
        /**
         * Initial callback
         */
        public function __construct()
        {
            parent::__construct(
                array(
                    'singular' => 'wp_list_text_link',
                    //Singular label

                    'plural' => 'wp_list_test_links',
                    //plural label, also this well be one of the table css class

                    'ajax' => false
                )
            );
        }



        /**
         * Get table data from applicaton DB
         * 
         * @access private
         */
        private function get_table_data($search = false)
        {
            global $wpdb;
            $query = 'SELECT * FROM `' . WD_Features::$table_name . '`';
            if ($search)
                $query .= $wpdb->prepare(' WHERE `first_name` LIKE %s OR `last_name` LIKE %s OR `present_address` LIKE %s OR `email` LIKE %s OR `mobile` LIKE %s OR `post_name` LIKE %s', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%');


            return apply_filters('wedevs_data', $wpdb->get_results($query, OBJECT));
        }


        /**
         * Sorting function
         */
        public function usort_reorder($a, $b)
        {
            // If no sort, default to submission_date
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'submission_date';

            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

            // Determine sort order     
            $result = strcmp($a->$orderby, $b->$orderby);

            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
        }


        /**
         * Delete application using id
         * 
         * @parameter init
         * @access private
         */
        private function _wdApplicationDelete($id = 0)
        {
            global $wpdb;
            $delete = $wpdb->delete(
                WD_Features::$table_name,
                array('id' => $id),
                array('%d')
            );

            if ($delete)
                wp_redirect(admin_url('admin.php?page=wedevs-opplications'));
        }


        /**
         * Prepare the table with different parameters, pagination, columns and table elements
         * 
         */
        public function prepare_items()
        {
            //Delete action
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
                $this->_wdApplicationDelete($_REQUEST['id']);

            $search = isset($_POST['s']) && !empty($_POST['s']) ? $_POST['s'] : false;

            //data
            $this->_table_data = $this->get_table_data($search);
            /* -- Register the Columns -- */

            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

            usort($this->_table_data, array(&$this, 'usort_reorder'));

            /* -- Fetch the items -- */
            $this->items = $this->_table_data;
        }



        /**
         * Define the columns that are going to be used in the table
         * 
         * @return array $columns, the array of columns to use with the table
         */
        public function get_columns()
        {
            $columns = array(
                'col_first_name' => __('Name', 'wedevs'),
                'col_present_address' => __('Address', 'wedevs'),
                'col_email' => __('Email', 'wedevs'),
                'col_mobile' => __('Mobile', 'wedevs'),
                'col_post_name' => __('Post Name', 'wedevs'),
                'col_date' => __('Date', 'wedevs'),
                'col_cv' => __('CV', 'wedevs')
            );

            return apply_filters('wedevs_admin_lists_headers', $columns);
        }


        /**
         * Adding action links to column
         * 
         */
        public function columnFirstName($item)
        {
            $item = (array) $item;
            $actions = array(
                'delete' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('Delete', 'supporthost-admin-table') . '</a>', $_REQUEST['page'], 'delete', $item['ID']),
            );

            return apply_filters('wedevs_list_action', $this->row_actions($actions), $item);
        }



        /**
         * Decide which columns to activate the sorting functionality on
         * 
         * @return array $sortable, the array of columns that can be sorted by the user
         */
        public function get_sortable_columns()
        {
            $sortable = array(
                'col_date' => array('submission_date', true)
            );

            return apply_filters('wedevs_shortable_columns', $sortable);
        }


        /**
         * Display the rows of records in the table
         * 
         * @return string, echo the markup of the rows
         */
        public function display_rows()
        {


            //Get the records registered in the prepare_items method
            $records = $this->items;
            list($columns, $hidden) = $this->get_column_info();

            //Loop for each record
            if (!empty($records)) {
                $output = '';
                foreach ($records as $rec) {

                    //Open the line
                    $output .= sprintf('<tr id="record_%s">', $rec->ID);
                    foreach ($columns as $column_name => $column_display_name) {

                        //Style attributes for each col
                        $attributes = "class='$column_name column-$column_name'";

                        //Display the cell
                        switch ($column_name) {
                            case "col_first_name":
                                $output .= sprintf('<td %s>%s %s %s</td>', $attributes, esc_attr($rec->first_name), esc_attr($rec->last_name), $this->columnFirstName($rec));
                                break;
                            case "col_present_address":
                                $output .= sprintf('<td %s>%s</td>', $attributes, esc_attr($rec->present_address));
                                break;
                            case "col_email":
                                $output .= sprintf('<td %s>%s</td>', $attributes, esc_attr($rec->email));
                                break;
                            case "col_mobile":
                                $output .= sprintf('<td %s>%s</td>', $attributes, esc_attr($rec->mobile));
                                break;
                            case "col_post_name":
                                $output .= sprintf('<td %s>%s</td>', $attributes, esc_attr($rec->post_name));
                                break;
                            case "col_cv":
                                $output .= sprintf('<td %s><a download href="%s"><span class="dashicons dashicons-pdf"></span></a></td>', $attributes, esc_url($rec->cv));
                                break;
                            case 'col_date':
                                $output .= sprintf('<td %s>%s</td>', $attributes, esc_attr(date('Y/m/d \a\t H:i a', strtotime($rec->submission_date))));

                        }
                    }

                    //Close the line
                    $output .= '</tr>';
                }
            }
            echo $output;
        }
    }
}