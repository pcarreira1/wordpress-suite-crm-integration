<?php

$sessionId = "";
function suiteCrmLogin() {
    if(!empty($sessionId)) {
        return $sessionId;
    }

    $settings = get_option( 'suite_crm_general_settings_options' );

    $url = $settings['api_url'] . '/service/v4_1/rest.php?method=login&input_type=JSON&response_type=JSON&rest_data={ "user_auth" : {  "user_name" : "'. $settings['username'] .'", "password" : "'. md5($settings['password']) .'"}, "application_name":"crm"}';
    $method = 'GET';
    $args = array('method' => $method);
    $response = wp_remote_request($url, $args);
    $response_body = wp_remote_retrieve_body($response);
    $json = json_decode($response_body);
    $sessionId = $json->id;
    return $sessionId;
}

function suiteCrmModules() {
    $sessionId = suiteCrmLogin();
    $url = 'https://crm.carnessabandeira.pt/service/v4_1/rest.php?method=get_available_modules&input_type=JSON&response_type=JSON&rest_data={ "session" : "'.$sessionId.'" } ';
    $method = 'GET';
    $args = array('method' => $method);
    $response = wp_remote_request($url, $args);
    $response_body = wp_remote_retrieve_body($response);
    $json = json_decode($response_body);

    $modules = $json->modules;
    return $modules;
}

function suiteCrmModuleFields($moduleName) {
    $sessionId = suiteCrmLogin();
    $url = 'https://crm.carnessabandeira.pt/service/v4_1/rest.php?method=get_module_fields&input_type=JSON&response_type=JSON&rest_data={ "session" : "'.$sessionId.'", "module_name": "'.$moduleName.'" } ';
    $method = 'GET';
    $args = array('method' => $method);
    $response = wp_remote_request($url, $args);
    $response_body = wp_remote_retrieve_body($response);
    $json = json_decode($response_body);

    $fields = $json->module_fields;
    return $fields;
}
