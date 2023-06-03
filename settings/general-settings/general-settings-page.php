<?php

function suite_crm_general_settings_html() {
    ?>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'suite_crm_general_settings_options' );
        do_settings_sections( 'suite_crm_general_settings' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function suite_crm_register_settings() {
    register_setting( 'suite_crm_general_settings_options', 'suite_crm_general_settings_options', '' );
    
	add_settings_section( 'suite_crm_settings', 'Suite CRM Settings', '', 'suite_crm_general_settings' );

    add_settings_field( 'suite_crm_general_settings_url', 'API URL', 'suite_crm_general_settings_url', 'suite_crm_general_settings', 'suite_crm_settings' );
    add_settings_field( 'suite_crm_general_settings_username', 'Username', 'suite_crm_general_settings_username', 'suite_crm_general_settings', 'suite_crm_settings' );
    add_settings_field( 'suite_crm_general_settings_password', 'Password', 'suite_crm_general_settings_password', 'suite_crm_general_settings', 'suite_crm_settings' );
}
add_action( 'admin_init', 'suite_crm_register_settings' );

function suite_crm_general_settings_url() {
    $options = get_option( 'suite_crm_general_settings_options' );
    echo "<input id='suite_crm_general_settings_url' name='suite_crm_general_settings_options[api_url]' type='text' value='" . esc_attr( $options['api_url'] ) . "' />";
}

function suite_crm_general_settings_username() {
    $options = get_option( 'suite_crm_general_settings_options' );
    echo "<input id='suite_crm_general_settings_username' name='suite_crm_general_settings_options[username]' type='text' value='" . esc_attr( $options['username'] ) . "' />";
}

function suite_crm_general_settings_password() {
    $options = get_option( 'suite_crm_general_settings_options' );
    echo "<input id='suite_crm_general_settings_password' name='suite_crm_general_settings_options[password]' type='password' value='" . esc_attr( $options['password'] ) . "' />";
}
