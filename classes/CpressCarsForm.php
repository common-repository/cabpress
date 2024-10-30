<?php

function cars_handler_add_form() {

    global $wpdb;
    $table = $wpdb->prefix . 'cars';

    $item = array();

    $default = array(
        'brand' => '',
        'color' => '',
        'passagers_number' => null,
        'description' => null,
        'car_image' => null,
        'price_km' => null
    );

    if (isset($_GET['action']) && isset($_GET['element']) && $_GET['action'] == "edit") {

        $element = sanitize_text_field($_GET['element']);
        //get data by id
        $data = $wpdb->get_row( $wpdb->prepare("SELECT * from {$table} WHERE id = %d", $element) );
        $item = $data[0];

        //edit
        if (isset($_REQUEST) && isset($_REQUEST['nonce'])) {

            if ( wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['nonce'])), basename(__FILE__))) {

                $data = array(
                    sanitize_text_field($_REQUEST['brand']),
                    sanitize_text_field($_REQUEST['color']),
                    filter_var($_REQUEST['passagers_number'], FILTER_SANITIZE_NUMBER_INT),
                    sanitize_text_field($_REQUEST['description']),
                    sanitize_file_name($_REQUEST['car_image'],),
                    filter_var($_REQUEST['price_km'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION )
                );

                $itemEdit = shortcode_atts($default, $data);
                $result = $wpdb->update(
                    $table, 
                    $itemEdit,
                    array('id' => $element)
                );
                if ($result) {
                    $message = __('Item was successfully updated', 'cpress');
                    $data = $wpdb->get_row( $wpdb->prepare("SELECT * from {$table} WHERE id = %d", $element) );
                    $item = $data[0];
            
                } else {
                    $notice = __('There was an error while updating the item', 'cpress');
                }
    
            }
        }

    }else{
        //insert
        if (isset($_REQUEST) && isset($_REQUEST['nonce'])) {

            if (wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['nonce'])), basename(__FILE__))) {
                $item = shortcode_atts($default, $data);
                $result = $wpdb->insert($table, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'cpress');
                } else {
                    $notice = __('There was an error while saving item', 'cpress');
                }
            }
        }
    }

    add_meta_box('cars_form_meta_box', 'Cars data', 'cpress_cars_form_meta_box_handler', 'cpress', 'normal', 'default');

    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php esc_html_e('Cars', 'cpress')?> <a class="add-new-h2"
                                    href="<?php echo esc_html(get_admin_url(get_current_blog_id(), 'admin.php?page=cars_listing'));?>"><?php esc_html_e('back to list', 'cpress')?></a>
        </h2>

        <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo esc_html($message) ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo esc_html(wp_create_nonce(basename(__FILE__)))?>"/>
            <input type="hidden" name="id" value="<?php echo esc_html($item['id']) ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('cpress', 'normal', $item); ?>
                        <input type="submit" value="<?php esc_html_e('Save', 'cpress')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

function cpress_cars_form_meta_box_handler($item) {
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="brand"><?php esc_html_e('Brand', 'cpress')?></label>
            </th>
            <td>
                <input id="brand" name="brand" type="text" style="width: 95%" value="<?php echo (isset($_GET['action']) && $_GET['action'] == "edit") ? esc_attr($item['brand']) :'' ;?>"
                        size="50" class="code" placeholder="<?php esc_html_e('Your car brand', 'cpress')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="car_image"><?php esc_html_e('Image', 'cpress')?></label>
            </th>
            <td>
                <input id="car_image" name="car_image" type="hidden" style="width: 95%" value="<?php echo (isset($_GET['action']) && $_GET['action'] == "edit") ? esc_attr($item['car_image']) :'' ;?>"
                        size="50" class="code" placeholder="<?php esc_html_e('Upload a car image', 'cpress')?>" >
                <input id="upload_image_button" class="button" type="button" value="Upload Image" />
                <p></p>
                <img id="image_upload" style="width: 300px" src="<?php echo (isset($_GET['action']) && $_GET['action'] == "edit") ? esc_attr($item['car_image']) :'' ;?>" >
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="color"><?php esc_html_e('Color', 'cpress')?></label>
            </th>
            <td>
                <input id="color" name="color" type="text" style="width: 95%" value="<?php echo (isset($_GET['action']) && $_GET['action'] == "edit") ? esc_attr($item['color']) :'' ;?>"
                        size="50" class="code" placeholder="<?php esc_html_e('The car color', 'cpress')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="passagers_number"><?php esc_html_e('Number of passenger', 'cpress')?></label>
            </th>
            <td>
                <input id="passagers_number" name="passagers_number" type="number" style="width: 95%" value="<?php echo esc_attr($item['passagers_number'])?>"
                        size="50" class="code" placeholder="<?php esc_html_e('The number of passenger', 'cpress')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="price_km"><?php esc_html_e('Price per km', 'cpress')?></label>
            </th>
            <td>
                <input id="price_km" name="price_km" type="number" step="0.01" style="width: 95%" value="<?php echo esc_attr($item['price_km'])?>"
                        size="50" class="code" placeholder="<?php esc_html_e('The price per KM', 'cpress')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="description"><?php esc_html_e('Car description', 'cpress')?></label>
            </th>
            <td>
                <textarea id="description" name="description"  style="width: 95%" 
                        size="50" rows="8" class="code" ><?php echo (isset($_GET['action']) && $_GET['action'] == "edit") ? esc_attr($item['description']):'' ?>
                </textarea>
            </td>
        </tr>
        </tbody>
    </table>
<?php
}