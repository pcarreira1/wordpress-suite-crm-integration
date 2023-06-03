<?php

require_once plugin_dir_path(__FILE__) . 'general-settings/general-settings-page.php';
require_once plugin_dir_path(__FILE__) . 'list-modules/list-modules-page.php';
require_once plugin_dir_path(__FILE__) . 'add-module/add-module-page.php';
require_once plugin_dir_path(__FILE__) . '../api/suite-crm.php';
require_once plugin_dir_path(__FILE__) . '../api/suite-crm-db.php';

function suite_crm_general_settings_page() {
	add_menu_page(
        'Suite CRM', 
		'Suite CRM', 
		'manage_options', 
		'suite-crm-plugin', 
		'suite_crm_plugin_html',
        'dashicons-database',  
        30                     
    );

    add_submenu_page(
        'suite-crm-plugin',            
        'General Settings',                
        'General Settings',                  
        'manage_options',           
        'suite-crm-general-settings',        
        'suite_crm_general_settings_html'   
    );

    add_submenu_page(
        'suite-crm-plugin',            
        'All Modules',                
        'All Modules',                  
        'manage_options',           
        'suite-crm-modules-integration',        
        'list_modules_page'   
    );

    add_submenu_page(
        'suite-crm-plugin',            
        'Add/Edit Module',                
        'Add/Edit Module',                  
        'manage_options',           
        'suite-crm-add-module',        
        'suite_crm_register_add_module'   
    );

    remove_submenu_page('suite-crm-plugin', 'suite-crm-plugin');
}
add_action( 'admin_menu', 'suite_crm_general_settings_page', 99 );

function suite_crm_plugin_html() {
    ?>
    <div class="wrap">
    </div>
    <?php
}

