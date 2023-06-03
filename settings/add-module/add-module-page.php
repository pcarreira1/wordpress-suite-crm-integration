<?php

function createOrUpdateModuleIntegration() {
    $moduleName = sanitize_text_field( $_POST['moduleName'] );
    $formID = $_POST['formID'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'suite_crm_modules_integration';

    $results = $wpdb->get_results(
        $wpdb->prepare( "
            SELECT 
                id,
                moduleName, 
                formId 
            FROM $table_name
            WHERE moduleName='$moduleName'"
        )
    );

    if(count($results) === 0) {
        $wpdb->insert(
            $table_name,
            array(
                'moduleName' => $moduleName,
                'formID' => $formID,
            )
        );
    } else {
        $wpdb->update(
            $table_name,
            array(
                'formID' => $formID,
            ),
            array(
                'moduleName' => $moduleName
            )
        );
    }
}

function createOrUpdateModuleMappings() {
    createOrUpdateModuleIntegration();
    global $wpdb;
    $table_name_mapping = $wpdb->prefix . 'suite_crm_modules_integration_mapping';

    $integrationId = getIntegrationByModuleName($_POST['moduleName']);
    if(!isset($integrationId)) {
        return;
    }

    // Delete previous mappings
    $wpdb->delete(
        $table_name_mapping,
        array( 'integrationId' => $integrationId ),
        array( '%d' )
    );

    foreach ($_POST as $key=>$value) {
        if(!empty($value) && $value <> $_POST['add_module_integration'] && $value <> $_POST['moduleName'] && $value <> $_POST['formID']) {
            $wpdb->insert(
                $table_name_mapping,
                array(
                    'integrationId' => $integrationId,
                    'suiteCrmFieldName' => $key,
                    'contactFormFieldName' => $value,
                )
            );
        }
    }
}

function suite_crm_register_add_module() {
    register_setting( 'suite_crm_add_module_options', 'suite_crm_add_module_options', '' );
    
	add_settings_section( 'suite_crm_add_module_section', 'Suite CRM Add/Edit Module', '', 'suite_crm_add_module' );

    if (isset($_POST['add_module_integration'])) {
        createOrUpdateModuleMappings();
    }

    $suiteCrmSession = suiteCrmLogin();
    $suiteCrmModules = suiteCrmModules();
    $modules_options = array();
    foreach ($suiteCrmModules as $module) {
        if(!empty($module->module_label)) {
            $modules_options[$module->module_key] = array(
                'title' => $module->module_label
            );
        }
    }

    $suiteCrmModuleFields = array();
    $fields_options = array();
    if(isset($_POST['moduleName'])) {
        $suiteCrmModuleFields = suiteCrmModuleFields($_POST['moduleName']);
        foreach ($suiteCrmModuleFields as $field) {
            $fields_options[$field->name] = array(
                'id' => $field->name,
                'title' => $field->label
            );
        }
    }

    $forms = get_posts(array(
        'post_type'      => 'wpcf7_contact_form',
        'posts_per_page' => -1,
    ));
    $form_options = array();
    foreach ($forms as $form) {
        $form_fields = wpcf7_contact_form($form->ID)->scan_form_tags();
        $form_options[$form->ID] = array(
            'title' => $form->post_title,
            'fields' => $form_fields,
        );
    }

    $mappings = getIntegrationMappings($_POST['moduleName']);

    ?>
    <div class="wrap">
        <h1>Add/Edit module integration</h1>
        <form method="post" action="">
            <label for="moduleName">Module name:</label>
            <select id='moduleName' name='moduleName' value="<?php echo $_POST['moduleName']; ?>" onchange="this.form.submit();">
                <?php
                    foreach ($modules_options as $value => $label) {
                        if($value === $_POST['moduleName']) {
                            echo '<option value="' . esc_attr($value) . '" selected>' . esc_html($label['title']) . '</option>';
                        } else {
                            echo '<option value="' . esc_attr($value) . '">' . esc_html($label['title']) . '</option>';
                        }
                    }
                ?>
            </select><br>

            <label for="formID">Form:</label>
            <select id='formID' name='formID' value="<?php echo $_POST['formID']; ?>" onchange="this.form.submit();">
                <?php
                    foreach ($form_options as $value => $label) {
                        if($value === $_POST['formID']) {
                            echo '<option value="' . esc_attr($value) . '" selected>' . esc_html($label['title']) . '</option>';
                        } else {
                            echo '<option value="' . esc_attr($value) . '">' . esc_html($label['title']) . '</option>';
                        }
                    }
                ?>
            </select><br>

            <div>
                    <h3>Fields Mapping</h3>

                    <?php
                        if(count($fields_options) === 0) {
                            echo "<p>Suite CRM Fields not available</p>";
                        }

                        foreach ($fields_options as $field) {
                            echo "<label for='".$field['id']."'>" . $field['title'] . ":</label>";
                            echo "<select id='".$field['id']."' name='".$field['id']."' value='".$mappings[$field['id']]."'>";

                                echo '<option value=""></option>';
                                foreach ($form_options[$_POST['formID']]['fields'] as $formField) {
                                    if($mappings[$field['id']] === $formField['name']) {
                                        echo '<option value="' . esc_attr($formField['name']) . '" selected>' . esc_html($formField['name']) . '</option>';
                                    } else {
                                        echo '<option value="' . esc_attr($formField['name']) . '">' . esc_html($formField['name']) . '</option>';
                                    }
                                }

                            echo "</select><br>";
                        }
                    ?>
            </div>

            <input style="margin-top: 1em;" class="button-primary" type="submit" name="add_module_integration" value="Save Module Mapping" <?php 
                if(count($fields_options) === 0) {
                    echo "disabled";
                }
            ?>>
        </form>
    </div>
    <?php
}
