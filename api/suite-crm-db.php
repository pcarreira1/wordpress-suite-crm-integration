<?php

function getIntegrationByFormId($formId) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suite_crm_modules_integration';

    $integration = $wpdb->get_row("
            SELECT 
                id,
                moduleName, 
                formId 
            FROM $table_name
            WHERE formId=". $formId
    );

    return $integration;
}

function getIntegrationByModuleName($moduleName) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suite_crm_modules_integration';

    $integration = $wpdb->get_row("
            SELECT 
                id,
                moduleName, 
                formId 
            FROM $table_name
            WHERE moduleName='". $moduleName ."'"
    );

    if(!isset($integration)) {
        return;
    }

    return $integration->id;
}

function getIntegrationMappings($moduleName) {
    $integrationId = getIntegrationByModuleName($moduleName);
    if(!isset($integrationId)) {
        return;
    }

    global $wpdb;
    $table_name_mapping = $wpdb->prefix . 'suite_crm_modules_integration_mapping';
    $data = $wpdb->get_results(
        $wpdb->prepare( "
            SELECT *
            FROM $table_name_mapping
            WHERE integrationId=$integrationId
        ")
    );

    $results = array();
    foreach ($data as $row) {
        $results[$row->suiteCrmFieldName] = $row->contactFormFieldName;
    }

    return $results;
}