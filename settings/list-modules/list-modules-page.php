<?php

require_once plugin_dir_path(__FILE__) . 'modules-table.php';

// Display the plugin settings page
function list_modules_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page.' );
    }

    // Create an instance of the custom table class
    $my_list_table = new Modules_Integration_Table();
    $my_list_table->process_bulk_action();
    $my_list_table->prepare_items();

    ?>
    <div class="wrap">
        <h1>All Modules</h1>

        <form method="post">
            <?php $my_list_table->display(); ?>
        </form>
    </div>
    <?php
}
