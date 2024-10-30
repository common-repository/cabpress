<?php

class Cpress_orders_Table extends WP_List_Table
{
    // Here we will add our code

    // define $table_data property
    private $table_data;

    // Get table data
    private function get_table_data( $search = '' ) {
        global $wpdb;

        $table = $wpdb->prefix . 'orders';

        if ( !empty($search) ) {
            return $wpdb->get_results(
                $wpdb->prepare("SELECT * from {$table} WHERE lastname Like %s OR firstname Like %s OR price Like %d", 
                [ $search, $search, $search]),
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * from {$table}",
                ARRAY_A
            );
        }
    }

    // Define table columns
    function get_columns()
    {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'lastname'          => __('lastname', 'cpress'),
                'firstname'   => __('firstname', 'cpress'),
                'mail'   => __('mail', 'cpress'),
                'origin_input'   => __('origin_input', 'cpress'),
                'destination_input'   => __('destination_input', 'cpress'),
                'mail'   => __('mail', 'cpress'),
                'price'        => __('Price', 'cpress')
        );
        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        //data
        if ( isset($_POST['s']) ) {
            $this->table_data = $this->get_table_data(sanitize_text_field($_POST['s']));
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = ( is_array(get_user_meta( get_current_user_id(), 'managetoplevel_page_supporthost_list_tablecolumnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'managetoplevel_page_supporthost_list_tablecolumnshidden', true) : array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'brand';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = $this->get_items_per_page('elements_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items, // total number of items
                'per_page'    => $per_page, // items to show on a page
                'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));
        
        $this->items = $this->table_data;
    }

    // set value for each column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'lastname':
            case 'firstname':
            case 'phone':
            case 'mail':
            case 'order_date':
            case 'order_time':
            case 'origin_input':
            case 'destination_input':
            default:
            return $item[$column_name];
        }
    }

    // Add a checkbox in the first column
    function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s" />',
                $item['id']
        );
    }

    // Define sortable column
    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'brand'  => array('brand', false),
            'color' => array('color', false),
            'price_km'   => array('price_km', true)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? sanitize_text_field($_GET['orderby']) : 'user_login';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? sanitize_text_field($_GET['order']) : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Adding action links to column
    function column_name($item)
    {
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Edit', 'cpress') . '</a>', sanitize_text_field($_REQUEST['page']), 'edit', $item['ID']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Delete', 'cpress') . '</a>', sanitize_text_field($_REQUEST['page']), 'delete', $item['ID']),
        );

        return sprintf('%1$s %2$s', $item['brand'], $this->row_actions($actions));
    }

    // To show bulk action dropdown
    function get_bulk_actions()
    {
            $actions = array(
                    'delete_all'    => __('Delete', 'cpress'),
                    'draft_all' => __('Move to Draft', 'cpress')
            );
            return $actions;
    }

}
