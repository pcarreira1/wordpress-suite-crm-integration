<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Custom table class that extends WP_List_Table
class Modules_Integration_Table extends WP_List_Table {
    function __construct() {
        parent::__construct( array(
            'singular' => 'my_plugin_row',
            'plural'   => 'my_plugin_rows',
            'ajax'     => false
        ) );
    }

    // Prepare the items for the table
    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suite_crm_modules_integration';
        $table_name_mapping = $wpdb->prefix . 'suite_crm_modules_integration_mapping';

        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns()
        );

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" );
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $this->items = $wpdb->get_results(
            $wpdb->prepare( "
                SELECT 
                    $table_name.id,
                    $table_name.moduleName, 
                    $table_name.formId,
                    COUNT(1) as mappings
                FROM $table_name
                LEFT JOIN $table_name_mapping ON $table_name_mapping.integrationId=$table_name.id
                GROUP BY $table_name.id, $table_name.moduleName, $table_name.formId 
                ORDER BY $table_name.id DESC 
                LIMIT %d, %d", ( $current_page - 1 ) * $per_page, $per_page )
        );
    }

    // Define the columns to be displayed in the table
    function get_columns() {
        $columns = array(
            'cb'    => '<input type="checkbox" />',
            'id'    => 'ID',
            'moduleName'  => 'Module Name',
            'formId' => 'Form ID',
            'mappings' => 'Mappings'
        );
        return $columns;
    }

    // Define sortable columns
    function get_sortable_columns() {
        $sortable_columns = array(
            'id'    => array( 'id', false ),
            'moduleName'  => array( 'moduleName', false ),
            'formId' => array( 'formId', false ),
            'mappings' => array( 'mappings', false )
        );
        return $sortable_columns;
    }

    // Render the checkbox column
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item->id
        );
    }

    // Render the delete column
    function column_delete( $item ) {
        return sprintf(
            '<a href="?page=suite-crm-modules-integration&action=delete&id=%s">Delete</a>',
            $item->id
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    // Handle the bulk actions
    function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suite_crm_modules_integration';
        $table_name_mapping = $wpdb->prefix . 'suite_crm_modules_integration_mapping';

        if ( 'delete' === $this->current_action() ) {
            $ids = isset( $_REQUEST['bulk-delete'] ) ? $_REQUEST['bulk-delete'] : array();
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                foreach ( $ids as $id ) {
                    $wpdb->delete(
                        $table_name,
                        array( 'id' => $id ),
                        array( '%d' )
                    );
                }
                $wpdb->delete(
                    $table_name_mapping,
                    array( 'integrationId' => $id ),
                    array( '%d' )
                );
            }
        }
    }

    // Render the column content
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'cb':
                return sprintf(
                    '<input type="checkbox" name="bulk-delete[]" value="%s" />',
                    $item->id
                );
            case 'delete':
                return sprintf(
                    '<a href="?page=my-plugin-settings&action=delete&id=%s">Delete</a>',
                    $item->id
                );
            default:
                return $item->$column_name;
        }
    }
}
