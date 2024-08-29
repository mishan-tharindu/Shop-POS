<?php
namespace Inc\Admin;

class ReturnProduct {

    public function display_return_product_page() {
        ?>
        <div class="wrap">
            <h2>Return Product</h2>
            <form id="returnProductForm" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('return_product_action', 'return_product_nonce'); ?>
                <input type="hidden" name="action" value="process_return_product">

                <div class="form-group">
                    <label for="invoiceID">Invoice ID:</label>
                    <input type="text" id="invoiceID" name="invoiceID" required>
                    <button type="button" id="fetchInvoiceDetails">Fetch Invoice Details</button>
                </div>

                <div id="invoiceDetails" style="display:none;">
                    <h3>Invoice Products</h3>
                    <table id="invoiceProductsTable" class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Return Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Invoice products will be dynamically loaded here -->
                        </tbody>
                    </table>

                    <div class="form-group">
                        <label for="note">Note:</label>
                        <textarea id="note" name="note" rows="4" cols="50"></textarea>
                    </div>

                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Process Return">
                    </p>
                </div>
            </form>
        </div>
        <?php
    }
}
