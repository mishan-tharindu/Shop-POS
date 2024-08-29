<?php

namespace Inc\Admin;

class Invoice {

    public function __construct() {
        
    }

    public function invoice_hooks() {
        add_action('wp_ajax_get_invoice_products', [$this,'get_invoice_products']);
        add_action('wp_ajax_nopriv_get_invoice_products', [$this,'get_invoice_products']);
    }


    public function view_invoices() {
        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
        ?>
            <h1>View All Invoices</h1>

            <form method="get" action="" class="date-filter-form">
                <input type="hidden" name="page" value="invoice-management">
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

            <table class="wp-mt-invoice-table wp-list-table widefat fixed striped" id="invoiceTable">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Payment Type</th>
                        <th>Discount</th>
                        <th>Total Bill</th>
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
            <table class="wp-list-table widefat fixed striped" id="invoiceProductsTable" style="display:none;">
                    <thead>
                            <th>Product Name</th>
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
        $table_invoice = $wpdb->prefix . 'mt_invoice';

        // Initialize the SQL query
        $sql = "SELECT * FROM $table_invoice WHERE 1=1";

        // Add date filters if provided
        if (!empty($start_date)) {
            $sql .= $wpdb->prepare(" AND date >= %s", $start_date);
        }
        if (!empty($end_date)) {
            $sql .= $wpdb->prepare(" AND date <= %s", $end_date);
        }

        $invoices = $wpdb->get_results($sql);

        if ($invoices) {
            foreach ($invoices as $invoice) {
                echo '<tr data-invoice-id="'. esc_attr($invoice->idinvoice) .'">';
                echo '<td>'. esc_html($invoice->idinvoice) .'</td>';
                echo '<td>'. esc_html($invoice->idcustomers) .'</td>';
                echo '<td>'. esc_html($invoice->date) .'</td>';
                echo '<td>'. esc_html($invoice->time) .'</td>';
                echo '<td>'. esc_html($invoice->payment_type) .'</td>';
                echo '<td>'. esc_html($invoice->discount) .'</td>';
                echo '<td>'. esc_html($invoice->payment) .'</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">No invoices found for the selected date range.</td></tr>';
        }



    }

    function get_invoice_products() {
        check_ajax_referer('search_product_nonce', 'nonce');

        if (isset($_POST['invoice_id'])) {
            global $wpdb;
            $invoice_id = sanitize_text_field($_POST['invoice_id']);
            $table_invoice_products = $wpdb->prefix . 'mt_invoice_products';
            $table_product_stock = $wpdb->prefix . 'mt_product_stock';

            $query = $wpdb->prepare("
                SELECT ip.*, ps.selling_price, p.product_name FROM $table_invoice_products ip JOIN $table_product_stock ps ON ip.sku = ps.sku JOIN wp_mt_products p ON ps.idproducts = p.idproducts WHERE ip.idinvoice = %s", $invoice_id);

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

