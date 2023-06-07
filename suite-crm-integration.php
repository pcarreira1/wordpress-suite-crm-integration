<?php
/**
 * Plugin Name: Suite CRM Integration
 * Plugin URI: https://github.com/pcarreira1/wordpress-suite-crm-integration
 * Description: Worpress plugin to integrate Suite CRM with Contact Form 7.
 * Version: 1.0.0
 * Author: Pedro Carreira
 * Author URI: https://github.com/pcarreira1/wordpress-suite-crm-integration
 * License: GPL v2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function app_log($message) {
    $log_file = WP_CONTENT_DIR . '/suite-crm-integration-log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    error_log($log_message, 3, $log_file);
}

require_once plugin_dir_path(__FILE__) . 'settings/suite-crm-integration-settings.php';
require_once plugin_dir_path(__FILE__) . 'database/modules-integration.php';
register_activation_hook( __FILE__, 'create_table_module_integration' );
register_deactivation_hook( __FILE__, 'delete_table_module_integration' );

$LOGFILE = plugin_dir_path(__FILE__).'debug.log';

function suite_crm_integration_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('contact-form-7');
}
add_action('wp_enqueue_scripts', 'suite_crm_integration_enqueue_scripts');

function getFormValue($variable) {
    if (is_array($variable)) {
        $firstPosition = reset($variable);
        return $firstPosition;
    } else {
        return $variable;
    }
}

function log_cf7_form_content( $contact_form ) {
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $form_data = $submission->get_posted_data();
        app_log("Form received: ".json_encode($form_data));

        $formId = $contact_form->id();
        $sessionId = suiteCrmLogin();

        $integration = getIntegrationByFormId($formId);
        if(!isset($integration)) {
            app_log("Integration not found for form id: ".$formId);
            return;
        }

        $mappings = getIntegrationMappings($integration->moduleName);
        if(!isset($mappings)) {
            app_log("Integration mappings not found for module name: ".$integration->moduleName);
            return;
        }

        $crmValues = array();
        foreach ($mappings as $key=>$value) {
            array_push($crmValues, array(
                "name" => $key,
                "value" => getFormValue($form_data[$value])
            ));
        }

        $restData = array(
            "session" => $sessionId,
            "module_name" => $integration->moduleName,
            "name_value_lists" => $crmValues
        );

        $settings = get_option( 'suite_crm_general_settings_options' );
        $url = $settings['api_url'] . '/service/v4_1/rest.php?method=set_entry&input_type=JSON&response_type=JSON&rest_data='.json_encode($restData);
        $method = 'GET';
        $body = json_encode($data);
        $args = array('method' => $method);
        $response = wp_remote_request($url, $args);
        $response_body = wp_remote_retrieve_body($response);
        app_log("Form sent to Suite CRM: ".$url);
    }
}
add_action( 'wpcf7_submit', 'log_cf7_form_content' );