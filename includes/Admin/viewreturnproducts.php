<?php

namespace Inc\Admin;

class ViewReturnProducts {

    public function __construct() {
        
    }

    public function returnproducts_hooks() {
        add_action('wp_ajax_get_returninvoice_products', [$this,'get_returninvoice_products']);
        add_action('wp_ajax_nopriv_get_returninvoice_products', [$this,'get_returninvoice_products']);
    }


    public function view_returnproducts() {
        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
        ?>
            <h1>View All Invoices</h1>

            <form method="get" action="" class="date-filter-form">
                <input type="hidden" name="page" value="view-return-product">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" value="">

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" value="">

                <input type="submit" value="Filter" class="button-primary">
            </form>

            <!-- <div class="wrap">
                <form method="get" action="">
                    <input type="hidden" name="page" value="invoice-management">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo esc_attr($start_date); ?>">

                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo esc_attr($end_date); ?>">

                    <input type="submit" value="Filter">
                </form>
            </div> -->

            <table class="wp-mt-invoice-table wp-list-table widefat fixed striped" id="returninvoiceTable">
                <thead>
                    <tr>
                        <th>Return Invoice Number</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Invoice Number</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        echo $this->invoices_tbody($start_date, $end_date);
                    ?>
                </tbody>

            </table>

            <!-- Products Table -->
            <h2>Invoice Products</h2>
            <table class="wp-list-table widefat fixed striped" id="returninvoiceProductsTable" style="display:none;">
                    <thead>
                            <th>Product Name</th>
                            <th>Return Issuse</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                    </thead>

                    <tbody>
                        <!-- Products will be dynamically loaded here -->
                    </tbody>
            </table>

        <?php
    }


    public function invoices_tbody($start_date = '', $end_date = '') {
        global $wpdb;
        $table_return_invoice = $wpdb->prefix . 'mt_return_invoice';

        // Initialize the SQL query
        $sql = "SELECT * FROM $table_return_invoice WHERE 1=1";

        // Add date filters if provided
        if (!empty($start_date)) {
            $sql .= $wpdb->prepare(" AND date >= %s", $start_date);
        }
        if (!empty($end_date)) {
            $sql .= $wpdb->prepare(" AND date <= %s", $end_date);
        }

        $return_invoices = $wpdb->get_results($sql);

        if ($return_invoices) {
            foreach ($return_invoices as $re_invoice) {

                
// idreturn_invoice	
// date	
// time	
// status	
// idinvoice
                echo '<tr data-return-invoice-id="'. esc_attr($re_invoice->idreturn_invoice) .'">';
                echo '<td>'. esc_html($re_invoice->idreturn_invoice) .'</td>';
                echo '<td>'. esc_html($re_invoice->date) .'</td>';
                echo '<td>'. esc_html($re_invoice->time) .'</td>';
                echo '<td>'. esc_html($re_invoice->idinvoice) .'</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">No invoices found for the selected date range.</td></tr>';
        }



    }

    function get_returninvoice_products() {
        check_ajax_referer('search_product_nonce', 'nonce');
        error_log("get_returninvoice_products");
        if (isset($_POST['re_invoiceId'])) {
            error_log($_POST['re_invoiceId']);
            global $wpdb;
            $re_invoice_id = sanitize_text_field($_POST['re_invoiceId']);
            $table_return_invoice_products = $wpdb->prefix . 'mt_return_inv_products';
            $table_return_invoice = $wpdb->prefix . 'mt_return_invoice';
            $table_products = $wpdb->prefix . 'mt_products';
            $table_product_stock = $wpdb->prefix . 'mt_product_stock';

            $query = $wpdb->prepare("SELECT rinp.*, rn.idinvoice, ps.sku, ps.selling_price, p.product_name, p.size FROM $table_return_invoice_products rinp JOIN $table_return_invoice rn ON rinp.idreturn_invoice = rn.idreturn_invoice JOIN $table_product_stock ps ON rinp.idproduct_stock = ps.idproduct_stock JOIN $table_products p ON ps.idproducts = p.idproducts WHERE rinp.idreturn_invoice = %s", $re_invoice_id);

            $products = $wpdb->get_results($query);

            if (!empty($products)) {
                wp_send_json_success($products);
                error_log("Products in click Table ::" . $products);
            } else {
                wp_send_json_error();
            }
        }

        wp_die();
    }



}

