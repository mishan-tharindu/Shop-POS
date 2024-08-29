<?php
namespace Inc\Admin;

class ReturnProductHandler {

    public function __construct() {

    }

    public function returnproduct_hooks() {
        add_action('wp_ajax_fetch_invoice_details', [ $this, 'fetch_invoice_details']);
        add_action('wp_ajax_nopriv_fetch_invoice_details', [ $this, 'fetch_invoice_details']);

        add_action('admin_post_process_return_product', [ $this, 'process_return_product']);
    }

    public function fetch_invoice_details() {
        check_ajax_referer('search_product_nonce', 'nonce');

        global $wpdb;
        $invoiceID = sanitize_text_field($_POST['invoiceID']);
        
        // Fetch invoice details from the database
        $table_invoice_products = $wpdb->prefix . 'mt_invoice_products';
        $table_products_stock = $wpdb->prefix . 'mt_product_stock';
        $table_products = $wpdb->prefix . 'mt_products';

        $products = $wpdb->get_results($wpdb->prepare(
                                "SELECT
                        ip.*,
                        ps.idproduct_stock,
                        ps.idproducts,
                        ps.selling_price,
                        p.product_name,
                        p.size
                    FROM
                        $table_invoice_products ip
                    JOIN $table_products_stock ps ON
                        ps.sku = ip.sku
                    JOIN $table_products p ON
                        ps.idproducts = p.idproducts
                    WHERE
                        ip.idinvoice = %s", 
             $invoiceID
        ));

        if ($products) {
            wp_send_json_success($products);
        } else {
            wp_send_json_error('No products found.');
        }
    }

    public function process_return_product() {
        if (!isset($_POST['return_product_nonce']) || !wp_verify_nonce($_POST['return_product_nonce'], 'return_product_action')) {
            wp_die('Security check failed');
        }

        global $wpdb;
        $invoiceID = sanitize_text_field($_POST['invoiceID']);
        $selected_products = $_POST['selected_products'];
        $return_qty = $_POST['return_qty'];
        $note = sanitize_textarea_field($_POST['note']);

        // Generate a unique return invoice ID
        $return_invoice_id = 'RIN' . date('YmdHis');

        // Insert into return invoice table
        $wpdb->insert(
            $wpdb->prefix . 'mt_return_invoice',
            [
                'idreturn_invoice' => $return_invoice_id,
                'date' => current_time('Y-m-d'),
                'time' => current_time('H:i:s'),
                'status' => 1,
                'idinvoice' => $invoiceID
            ],
            ['%s', '%s', '%s', '%d', '%s']
        );

        foreach ($selected_products as $product_id) {
            $qty = intval($return_qty[$product_id]);

            // Insert into return invoice products table
            $wpdb->insert(
                $wpdb->prefix . 'mt_return_inv_products',
                [
                    'qty' => $qty,
                    'note' => $note,
                    'status' => 1,
                    'idreturn_invoice' => $return_invoice_id,
                    'idproduct_stock' => $product_id
                ],
                ['%d', '%s', '%d', '%s', '%d']
            );

            // Update the stock quantity
            // $wpdb->query(
            //     $wpdb->prepare(
            //         "UPDATE {$wpdb->prefix}mt_product_stock 
            //          SET qty = qty + %d 
            //          WHERE idproduct_stock = %d", 
            //          $qty, $product_id
            //     )
            // );
        }

        // Redirect with success message
        wp_redirect(admin_url('admin.php?page=return-product&status=success'));
        exit;
    }
}
