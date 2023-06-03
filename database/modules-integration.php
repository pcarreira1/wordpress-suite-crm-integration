<?php

function create_table_module_integration() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suite_crm_modules_integration';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        moduleName VARCHAR(100) NOT NULL,
        formId INT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    $table_name = $wpdb->prefix . 'suite_crm_modules_integration_mapping';
    $sql = "CREATE TABLE $table_name(
        id INT NOT NULL AUTO_INCREMENT,
        integrationId INT NOT NULL,
        suiteCrmFieldName VARCHAR(100) NOT NULL,
        contactFormFieldName VARCHAR(100) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql);
}

function delete_table_module_integration() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'suite_crm_modules_integration';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );

    $table_name = $wpdb->prefix . 'suite_crm_modules_integration_mapping';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );
}
