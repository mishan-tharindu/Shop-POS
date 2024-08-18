<?php
namespace Inc\Admin;

class ProductHandler {

    public static function register_product() {
        error_log('Form submitted.'); // Check if this gets logged
        if (!isset($_POST['register_product_nonce']) || !wp_verify_nonce($_POST['register_product_nonce'], 'register_product_action')) {
            wp_die('Security check failed');
        }

        error_log('Nonce passed.'); // Check if nonce checking passes
    
        if (isset($_POST['productName'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mt_products';
                
            // Sanitize and prepare data
            $data = [
                'sku' => sanitize_text_field($_POST['sku']),
                'name' => sanitize_text_field($_POST['productName']),
                'description' => sanitize_textarea_field($_POST['description']),
                'category' => sanitize_text_field($_POST['category']),
                'price' => sanitize_text_field($_POST['price']),
                'quantity' => intval($_POST['quantity']),
                'supplier' => sanitize_text_field($_POST['supplier']),
                // Add other fields as necessary
            ];

                    // Handle file uploads
            if (!empty($_FILES['productImages']['name'][0])) {
                $data['images'] = self::upload_images($_FILES['productImages']);
            }
    
            // Insert product data into the database
            $inserted = $wpdb->insert($table_name, $data);
            if ($inserted) {
                // echo '<div id="successMessage">Product "' . esc_html($data['name']) . '" registered successfully!</div>';
                // Redirect back to the same page with a success message
                $redirect_url = add_query_arg(array(
                    'page' => 'product-management', // Make sure this matches the actual slug of your product registration page
                    'status' => 'success' // This query parameter is used to show the success message
                ), admin_url('admin.php'));

                wp_redirect($redirect_url);
                exit;

            } else {
                echo '<div id="errorMessage">Failed to register product.</div>';
            }

        }
    }

    public static function delete_product() {
        if (!isset($_GET['delete_product_nonce']) || !wp_verify_nonce($_GET['delete_product_nonce'], 'delete_product_action')) {
            wp_die('Security check failed');
        }

        if (isset($_GET['id'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mt_products';
            $wpdb->delete($table_name, array('id' => intval($_GET['id'])));
            wp_redirect(add_query_arg('status', 'deleted', admin_url('admin.php?page=product-management')));
            exit;
        }
    }

    public static function update_product() {
        if (!isset($_POST['register_product_nonce']) || !wp_verify_nonce($_POST['register_product_nonce'], 'register_product_action')) {
            wp_die('Security check failed');
        }

        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mt_products';

            // Prepare updated data
            $data = [
                'sku' => sanitize_text_field($_POST['sku']),
                'name' => sanitize_text_field($_POST['productName']),
                'description' => sanitize_textarea_field($_POST['description']),
                'category' => sanitize_text_field($_POST['category']),
                'price' => sanitize_text_field($_POST['price']),
                'quantity' => intval($_POST['quantity']),
                'supplier' => sanitize_text_field($_POST['supplier']),
            ];

            // Handle existing images
            $existing_images = isset($_POST['existing_images']) ? $_POST['existing_images'] : [];
            $removed_images = isset($_POST['remove_images']) ? $_POST['remove_images'] : [];
            $images_array = array_diff($existing_images, $removed_images);

            // Handle new uploads (append to existing images)
            if (!empty($_FILES['productImages']['name'][0])) {
                $new_images = self::upload_images($_FILES['productImages']);
                $images_array = array_merge($images_array, explode(',', $new_images));
            }

            // Update images field
            $data['images'] = implode(',', $images_array);

            // Update the product in the database
            $wpdb->update($table_name, $data, ['id' => intval($_POST['product_id'])]);

            wp_redirect(add_query_arg('status', 'updated', admin_url('admin.php?page=product-management')));
            exit;
        }
    }


        // Image Upload Helper Function
        private static function upload_images($files) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
    
            $uploaded_urls = [];
    
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );
    
                    $_FILES = array("product_image" => $file);
    
                    $attachment_id = media_handle_upload("product_image", 0);
    
                    if (is_wp_error($attachment_id)) {
                        continue;
                    } else {
                        $uploaded_urls[] = wp_get_attachment_url($attachment_id);
                    }
                }
            }
    
            return implode(',', $uploaded_urls);
        }

}
