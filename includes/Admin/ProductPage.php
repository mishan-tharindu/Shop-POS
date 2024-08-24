<?php
namespace Inc\Admin;

class ProductPage {

    public function __construct() {
        // Constructor code if needed
    }

    public function display() {
        global $wpdb;
        $table_products = $wpdb->prefix . 'mt_products';
        $table_product_stock = $wpdb->prefix . 'mt_product_stock';
        $table_sub_category = $wpdb->prefix . 'mt_sub_category';
        $table_main_category = $wpdb->prefix . 'wp_mt_main_category';

        // Fetch products
        $products = $wpdb->get_results("
SELECT p.idproducts, p.product_name, p.discription, p.size, ps.sku, ps.qty, ps.selling_price, ps.idproduct_stock, sc.sub_cat_name, mc.main_cat_name 
            FROM wp_mt_products p
            JOIN wp_mt_product_stock ps ON p.idproducts = ps.idproducts
            LEFT JOIN wp_mt_sub_category sc ON p.idsub_category = sc.idsub_category
            LEFT JOIN wp_mt_main_category mc ON sc.idsub_category = mc.idmain_category
        ");

        // Fetch categories for the dropdown
        $categories = $wpdb->get_results("SELECT idsub_category, sub_cat_name, main_cat_name FROM wp_mt_sub_category sc JOIN wp_mt_main_category mc ON sc.idmain_category = mc.idmain_category");

        // Check for the 'status' query parameter to show the success message
        if (isset($_GET['status']) && $_GET['status'] === 'success') {
            echo '<div class="updated notice"><p>Product successfully registered!</p></div>';
        }

        // Check if the action is 'edit' and an id is provided
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $product_id = intval($_GET['id']);
            $product = $wpdb->get_row($wpdb->prepare("
                SELECT p.*, ps.* 
                FROM $table_products p 
                JOIN $table_product_stock ps ON p.idproducts = ps.idproducts 
                WHERE ps.idproduct_stock = %d", $product_id));
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo isset($product) ? 'Edit Product' : 'Product Registration'; ?></h1>

            <!-- Success message for updates -->
            <?php if (isset($_GET['status']) && $_GET['status'] === 'updated') : ?>
            <div class="notice notice-success is-dismissible">
                <p>Product updated successfully!</p>
            </div>
            <?php endif; ?>

            <form id="productForm" method="post" action="<?php echo admin_url('admin-post.php'); ?>"
                enctype="multipart/form-data">
                <?php wp_nonce_field('register_product_action', 'register_product_nonce'); ?>
                <input type="hidden" name="action" value="<?php echo isset($product) ? 'update_product' : 'register_product'; ?>">
                <input type="hidden" name="product_id" value="<?php echo isset($product) ? esc_attr($product->idproducts) : ''; ?>">
                <input type="hidden" name="product_stock_id" value="<?php echo isset($product) ? esc_attr($product->idproduct_stock) : ''; ?>">

                <div class="form-group">
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="productName"
                        value="<?php echo isset($product) ? esc_attr($product->product_name) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="sku">SKU:</label>
                    <input type="text" id="sku" name="sku" value="<?php echo isset($product) ? esc_attr($product->sku) : ''; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="size">Size:</label>
                    <textarea id="size" name="size"
                        rows="3"><?php echo isset($product) ? esc_textarea($product->size) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->idsub_category); ?>"
                                <?php selected(isset($product) ? $product->idsub_category : '', $category->idsub_category); ?>>
                                <?php echo  esc_html($category->main_cat_name) . " - " . esc_html($category->sub_cat_name);?> 
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01"
                        value="<?php echo isset($product) ? esc_attr($product->selling_price) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity"
                        value="<?php echo isset($product) ? esc_attr($product->qty) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="supplier">Supplier:</label>
                    <input type="text" id="supplier" name="supplier"
                        value="<?php echo isset($product) ? esc_attr($product->supplier) : ''; ?>" required>
                </div>


                <div class="form-group">
                    <label for="productImages">Product Images:</label>
                    <input type="file" id="productImages" name="productImages[]" accept="image/*" multiple>
                    <div id="imagePreview">
                        <?php if (isset($product) && $product->images) : ?>
                        <?php $images = explode(',', $product->images); ?>
                        <?php foreach ($images as $index => $image) : ?>
                        <div class="image-item" data-image-url="<?php echo esc_url($image); ?>">
                            <img src="<?php echo esc_url($image); ?>" alt="Product Image" width="100">
                            <span class="remove-image" style="cursor: pointer;">&times;</span>
                            <input type="hidden" name="existing_images[]" value="<?php echo esc_attr($image); ?>">
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit"><?php echo isset($product) ? 'Update Product' : 'Register Product'; ?></button>
            </form>
        </div>
        <?php

        // Display the registered products
        echo '<div class="wrap">';
        echo '<h1>Registered Products</h1>';

        // Success messages for delete and update
        if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>Product deleted successfully!</p></div>';
        } elseif (isset($_GET['status']) && $_GET['status'] === 'updated') {
            echo '<div class="notice notice-success is-dismissible"><p>Product updated successfully!</p></div>';
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>SKU</th><th>Name</th><th>Size</th><th>Category</th><th>Price</th><th>Quantity</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($products as $product) {
            echo '<tr>';
            echo '<td>' . esc_html($product->sku) . '</td>';
            echo '<td>' . esc_html($product->product_name) . '</td>';
            echo '<td>' . esc_html($product->size) . '</td>';
            echo '<td>' . esc_html($product->sub_cat_name) . '</td>';
            echo '<td>' . esc_html($product->selling_price) . '</td>';
            echo '<td>' . esc_html($product->qty) . '</td>';
            echo '<td>';
            echo '<a href="' . admin_url('admin.php?page=product-management&action=edit&id=' . esc_attr($product->idproduct_stock)) . '">Edit</a> | ';
            echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_product&id=' . esc_attr($product->idproduct_stock)), 'delete_product_action', 'delete_product_nonce') . '" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}
