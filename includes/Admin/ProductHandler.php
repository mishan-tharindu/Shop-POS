<?php
namespace Inc\Admin;

// Hook into WordPress AJAX
// add_action('wp_ajax_search_product', [ProductHandler::class, 'search_product']);
// add_action('wp_ajax_nopriv_search_product', [ProductHandler::class, 'search_product']);

class ProductHandler {

    public function register_hooks() {
        add_action('wp_ajax_search_product', [ $this, 'search_product']);
        add_action('wp_ajax_nopriv_search_product', [ $this, 'search_product']);


        add_action('wp_ajax_save_invoice', [ $this, 'save_invoice']);
        add_action('wp_ajax_nopriv_save_invoice', [ $this, 'save_invoice']);

        // add_action('admin_post_registor_main_category', array($this, 'registor_main_category'));
    }

    public static function register_product() {
            // Security check
        if (!isset($_POST['register_product_nonce']) || !wp_verify_nonce($_POST['register_product_nonce'], 'register_product_action')) {
            wp_die('Security check failed');
        }

        global $wpdb;
        $table_products = $wpdb->prefix . 'mt_products';
        $table_product_stock = $wpdb->prefix . 'mt_product_stock';

        // Sanitize and prepare data
        $product_name = sanitize_text_field($_POST['productName']);
        $sku = sanitize_text_field($_POST['sku']);
        $size = sanitize_textarea_field($_POST['size']);
        $category_id = intval($_POST['category']);
        $price = sanitize_text_field($_POST['price']);
        $quantity = intval($_POST['quantity']);

        // Insert product into mt_products table
        $product_inserted = $wpdb->insert(
            $table_products,
            [
                'product_name' => $product_name,
                'discription' => $size,
                'idsub_category' => $category_id
            ],
            ['%s', '%s', '%d']
        );

        $product_id = $wpdb->insert_id;

        // Insert stock information into mt_product_stock table
        if ($product_inserted) {
            $wpdb->insert(
                $table_product_stock,
                [
                    'sku' => $sku,
                    'qty' => $quantity,
                    'selling_price' => $price,
                    'idproducts' => $product_id
                ],
                ['%s', '%d', '%s', '%d']
            );
        }

        // Handle file uploads for product images
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $uploaded_images = [];
        foreach ($_FILES['productImages']['name'] as $key => $value) {
            if ($_FILES['productImages']['name'][$key]) {
                $file = [
                    'name'     => $_FILES['productImages']['name'][$key],
                    'type'     => $_FILES['productImages']['type'][$key],
                    'tmp_name' => $_FILES['productImages']['tmp_name'][$key],
                    'error'    => $_FILES['productImages']['error'][$key],
                    'size'     => $_FILES['productImages']['size'][$key]
                ];
                $upload_overrides = ['test_form' => false];
                $movefile = wp_handle_upload($file, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $uploaded_images[] = $movefile['url'];
                } else {
                    // Handle error case
                }
            }
        }

        // Update the product record with images if applicable
        if (!empty($uploaded_images)) {
            $wpdb->update(
                $table_products,
                ['images' => implode(',', $uploaded_images)],
                ['idproducts' => $product_id],
                ['%s'],
                ['%d']
            );
        }

        if ($product_inserted) {
            wp_redirect(admin_url('admin.php?page=product-management&status=success'));
            exit;
        } else {
            wp_redirect(admin_url('admin.php?page=product-management&status=error'));
            exit;
        }
    }

    public static function delete_product() {
        if (!isset($_GET['delete_product_nonce']) || !wp_verify_nonce($_GET['delete_product_nonce'], 'delete_product_action')) {
            wp_die('Security check failed');
        }
    
        if (isset($_GET['id'])) {
            global $wpdb;
            $product_id = intval($_GET['id']);
            
            $table_products = $wpdb->prefix . 'mt_products';
            $table_product_stock = $wpdb->prefix . 'mt_product_stock';
    
            // Delete product stock entries first to maintain referential integrity
            $wpdb->delete($table_product_stock, ['idproducts' => $product_id]);  // Assuming 'idproducts' is the foreign key in mt_product_stock table
            
            // Then delete the product entry
            $wpdb->delete($table_products, ['idproducts' => $product_id]);
            
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
            $table_products = $wpdb->prefix . 'mt_products';
            $table_product_stock = $wpdb->prefix . 'mt_product_stock';
    
            // Product and Stock IDs
            $product_id = intval($_POST['product_id']);
            $product_stock_id = intval($_POST['product_stock_id']);
    
            // Update mt_products table
            $product_data = [
                'product_name' => sanitize_text_field($_POST['productName']),
                'size' => sanitize_textarea_field($_POST['size']),
                'idsub_category' => intval($_POST['category'])
            ];
            
            $wpdb->update($table_products, $product_data, ['idproducts' => $product_id]);
    
            // Update mt_product_stock table
            $stock_data = [
                'sku' => sanitize_text_field($_POST['sku']),
                'qty' => intval($_POST['quantity']),
                'selling_price' => sanitize_text_field($_POST['price'])
            ];
    
            $wpdb->update($table_product_stock, $stock_data, ['idproduct_stock' => $product_stock_id]);
    
            // Handle existing images
            $existing_images = isset($_POST['existing_images']) ? $_POST['existing_images'] : [];
            $removed_images = isset($_POST['remove_images']) ? $_POST['remove_images'] : [];
            $images_array = array_diff($existing_images, $removed_images);
    
            // Handle new uploads (append to existing images)
            if (!empty($_FILES['productImages']['name'][0])) {
                $new_images = self::upload_images($_FILES['productImages']);
                $images_array = array_merge($images_array, explode(',', $new_images));
            }
    
            // Update images in mt_products
            $images_data = ['images' => implode(',', $images_array)];
            $wpdb->update($table_products, $images_data, ['idproducts' => $product_id]);
    
            wp_redirect(add_query_arg('status', 'updated', admin_url('admin.php?page=product-management')));
            exit;
        }
    }
    
    private static function upload_images($files) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
    
        $uploaded_images = [];
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                $file = [
                    'name'     => $files['name'][$key],
                    'type'     => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error'    => $files['error'][$key],
                    'size'     => $files['size'][$key]
                ];
                $upload_overrides = ['test_form' => false];
                $movefile = wp_handle_upload($file, $upload_overrides);
    
                if ($movefile && !isset($movefile['error'])) {
                    $uploaded_images[] = $movefile['url'];
                } else {
                    // Handle error case
                    // Optionally log the error or handle it as needed
                }
            }
        }
        return implode(',', $uploaded_images);
    }

    public function search_product() {

        // Log incoming data for debugging
        // error_log('AJAX request received');
        // error_log(print_r($_POST, true));
        
            global $wpdb;

            // Check for nonce first.
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'search_product_nonce')) {
                wp_send_json_error('Nonce verification failed');
                wp_die();
            }

            // Validate presence of the query.
            if (empty($_POST['query'])) {
                wp_send_json_error('No query provided');
                wp_die();
            }


            $search_query = sanitize_text_field($_POST['query']);
            $table_products = $wpdb->prefix . 'mt_products';
            $table_products_stock = $wpdb->prefix . 'mt_product_stock'; 

            $products = $wpdb->get_results($wpdb->prepare("SELECT mtp.product_name as product_name, mtp.size as size, mtps.sku as sku, mtps.qty as qty, mtps.selling_price as selling_price, mtps.discount as discount FROM `wp_mt_products` mtp JOIN wp_mt_product_stock mtps ON mtp.idproducts = mtps.idproducts WHERE mtps.sku LIKE %s OR mtp.product_name LIKE %s",
                '%' . $wpdb->esc_like($search_query) . '%',
                '%' . $wpdb->esc_like($search_query) . '%'
            ));
        
            if ($products) {
                wp_send_json_success($products);
            } else {
                wp_send_json_error('No products found');
            }
        
            wp_die();

    }

    public static function register_main_category() {
        if (!isset($_POST['register_category_nonce']) || !wp_verify_nonce($_POST['register_category_nonce'], 'register_category_action')) {
            wp_die('Security check failed');
            error_log('Nonce Failed.');
        }

        error_log('Nonce passed.');

        global $wpdb;
        $parent_id = sanitize_text_field($_POST['parent']);
        $category_name = sanitize_text_field($_POST['tag-name']);
        $category_slug = sanitize_title($category_name); // Generates a URL-friendly slug

        if ($parent_id == '-1') {
            error_log('Main Category section !!!');
            // It's a main category
            $table = $wpdb->prefix . 'mt_main_category';
            $inserted = $wpdb->insert(
                $table,
                [
                    'main_cat_name' => $category_name,
                    'main_cat_slug' => $category_slug
                ],
                ['%s', '%s']
            );
        } else {
            // It's a sub category
            error_log('Sub Category section !!!');
            $table = $wpdb->prefix . 'mt_sub_category';
            $inserted = $wpdb->insert(
                $table,
                [
                    'sub_cat_name' => $category_name,
                    'sub_cat_slug' => $category_slug,
                    'idmain_category' => $parent_id
                ],
                ['%s', '%s', '%d']
            );
        }

        if ($inserted) {
            // Redirect back to the same page with a success message
            $redirect_url = add_query_arg(array(
                'page' => 'category-menu', // Ensure this matches your menu slug
                'status' => 'success'
            ), admin_url('admin.php'));

            wp_redirect($redirect_url);
            exit;

        } else {
            $redirect_url = add_query_arg(array(
                'page' => 'category-menu', // Ensure this matches your menu slug
                'status' => 'error'
            ), admin_url('admin.php'));

            wp_redirect($redirect_url);
            exit;
            
        }
    }

    public function edit_category() {
        if (!isset($_GET['id']) || !isset($_GET['type'])) {
            wp_die('Category not specified');
        }

        $id = intval($_GET['id']);
        $type = sanitize_text_field($_GET['type']);
        global $wpdb;
        if ($type == 'main') {
            $table = $wpdb->prefix . 'mt_main_category';
            $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE idmain_category = %d", $id));
        } else {
            $table = $wpdb->prefix . 'mt_sub_category';
            $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE idsub_category = %d", $id));
        }

        // // Assume we are in an admin form where `admin_url('admin-post.php')` is the action
        // echo "<form method='post' action='".admin_url("admin-post.php")."'>
        //     <input type='hidden' name='action' value='update_category'>
        //     <input type='hidden' name='category_id' value='{$id}'>
        //     <input type='hidden' name='category_type' value='{$type}'>
        //     <input type='text' name='name' value='{$category->main_cat_name ?? $category->sub_cat_name}'>
        //     <input type='text' name='slug' value='{$category->main_cat_slug ?? $category->sub_cat_slug}'>
        //     <input type='submit' value='Update Category'>
        // </form>";
    }

    function save_invoice() {
        // check_ajax_referer('your_nonce_action', 'nonce');
    
        if (isset($_POST['invoiceData'])) {
            global $wpdb;
    
            $invoiceData = json_decode(stripslashes($_POST['invoiceData']), true);
            $invoiceNumber = generate_invoice_number(); // Function to generate unique invoice number
    
            // Get the current date and time
            $date = current_time('Y-m-d');
            $time = current_time('H:i:s');
    
            // Insert into the invoice table
            $table_invoice = $wpdb->prefix . 'mt_invoice';
            $inserted = $wpdb->insert(
                $table_invoice,
                array(
                    'idinvoice' => $invoiceData['invoiceId'],
                    'date' => $date,
                    'time' => $time,
                    'qty' => $invoiceData['qty'],
                    'discount' => $invoiceData['discount'],
                    'status' => 1, // Assuming 1 means 'paid' or 'active'
                    'payment' => $invoiceData['paymentMethod'],
                    'idproduct_stock' => '', // Replace this with the actual product stock ID
                    'idcustomers' => '' // Replace this with the actual customer ID if needed
                ),
                array('%s', '%s', '%s', '%d', '%f', '%d', '%s', '%d', '%d')
            );
    
            if ($inserted) {
                wp_send_json_success('Invoice saved successfully.');
            } else {
                wp_send_json_error('Failed to save invoice.');
            }
        } else {
            wp_send_json_error('No invoice data received.');
        }
    }

    
    function generate_invoice_number() {
        return 'INC' . date('YmdHis') . wp_rand(1000, 9999); // Example: INC202308241230159999
    }
    

}