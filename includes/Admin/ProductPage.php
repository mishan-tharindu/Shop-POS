<?php
namespace Inc\Admin;

class ProductPage {

    public function __construct() {
        // Constructor code if needed
    }

    public function display() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mt_products';
        $products = $wpdb->get_results("SELECT * FROM $table_name");

       // Check for the 'status' query parameter to show the success message
        if (isset($_GET['status']) && $_GET['status'] === 'success') {
            echo '<div class="updated notice"><p>Product successfully registered!</p></div>';
        }

         // Check if the action is 'edit' and an id is provided
         if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $product_id = intval($_GET['id']);
            $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $product_id));
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo $product ? 'Edit Product' : 'Product Registration'; ?></h1>

            <!-- Success message for updates -->
            <?php if (isset($_GET['status']) && $_GET['status'] === 'updated') : ?>
                <div class="notice notice-success is-dismissible"><p>Product updated successfully!</p></div>
            <?php endif; ?>

            <form id="productForm" method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                <?php wp_nonce_field('register_product_action', 'register_product_nonce'); ?>
                <input type="hidden" name="action" value="<?php echo $product ? 'update_product' : 'register_product'; ?>">
                <input type="hidden" name="product_id" value="<?php echo $product ? esc_attr($product->id) : ''; ?>">

                <div class="form-group">
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="productName" value="<?php echo $product ? esc_attr($product->name) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="sku">SKU:</label>
                    <input type="text" id="sku" name="sku" value="<?php echo $product ? esc_attr($product->sku) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="3"><?php echo $product ? esc_textarea($product->description) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="Clothing" <?php selected($product ? $product->category : '', 'Clothing'); ?>>Clothing</option>
                        <option value="Footwear" <?php selected($product ? $product->category : '', 'Footwear'); ?>>Footwear</option>
                        <option value="Accessories" <?php selected($product ? $product->category : '', 'Accessories'); ?>>Accessories</option>
                        <!-- Add more categories as needed -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo $product ? esc_attr($product->price) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="<?php echo $product ? esc_attr($product->quantity) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="supplier">Supplier:</label>
                    <input type="text" id="supplier" name="supplier" value="<?php echo $product ? esc_attr($product->supplier) : ''; ?>" required>
                </div>


                <div class="form-group">
                    <label for="productImages">Product Images:</label>
                    <input type="file" id="productImages" name="productImages[]" accept="image/*" multiple>
                    <div id="imagePreview">
                        <?php if ($product && $product->images) : ?>
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

                <button type="submit"><?php echo $product ? 'Update Product' : 'Register Product'; ?></button>
            </form>
        </div>
        <?php

        echo '<div class="wrap">';
        echo '<h1>Registered Products</h1>';

        // Success messages for delete and update
        if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>Product deleted successfully!</p></div>';
        } elseif (isset($_GET['status']) && $_GET['status'] === 'updated') {
            echo '<div class="notice notice-success is-dismissible"><p>Product updated successfully!</p></div>';
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>SKU</th><th>Name</th><th>Description</th><th>Category</th><th>Price</th><th>Quantity</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($products as $product) {
            echo '<tr>';
            echo '<td>' . esc_html($product->sku) . '</td>';
            echo '<td>' . esc_html($product->name) . '</td>';
            echo '<td>' . esc_html($product->description) . '</td>';
            echo '<td>' . esc_html($product->category) . '</td>';
            echo '<td>' . esc_html($product->price) . '</td>';
            echo '<td>' . esc_html($product->quantity) . '</td>';
            echo '<td>';
            echo '<a href="' . admin_url('admin.php?page=product-management&action=edit&id=' . esc_attr($product->id)) . '">Edit</a> | ';
            echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_product&id=' . esc_attr($product->id)), 'delete_product_action', 'delete_product_nonce') . '" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        }
}
