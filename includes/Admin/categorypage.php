<?php

namespace Inc\Admin;

class CategoryPage
{
    public function view_category_page() {
        global $wpdb;
        $table_main_category = $wpdb->prefix . 'mt_main_category';
        $table_sub_category = $wpdb->prefix . 'mt_sub_category';

        $main_categories = $wpdb->get_results("SELECT * FROM $table_main_category");

        // Query that fetches both main and sub-categories in a structured manner
        $query = "
            SELECT mc.idmain_category, mc.main_cat_name, mc.main_cat_slug, 
                   sc.idsub_category, sc.sub_cat_name, sc.sub_cat_slug
            FROM $table_main_category mc
            LEFT JOIN $table_sub_category sc ON sc.idmain_category = mc.idmain_category
            ORDER BY mc.idmain_category, sc.idsub_category ASC
        ";
        

        $categories = $wpdb->get_results($query);
        
        if (isset($_GET['status']) && $_GET['status'] === 'success') {
            echo '<div class="updated notice"><p>Category successfully registered!</p></div>';
        } else if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
            echo '<div class="updated notice"><p>Category successfully deleted!</p></div>';
        } else if (isset($_GET['status']) && $_GET['status'] === 'error' && isset($_GET['message'])) {
            echo '<div class="error notice"><p>' . esc_html($_GET['message']) . '</p></div>';
        }

        $categoryType = $_GET['type'];

        if (isset($_GET['action']) && $_GET['action'] === 'edit' && $_GET['type'] === 'main' && isset($_GET['id'])) {
            error_log("Category Edit !!!");

            $category_id = intval($_GET['id']);
            $main_category_query = $wpdb->get_row($wpdb->prepare("
                                SELECT
                                mc.idmain_category,
                                mc.main_cat_name,
                                mc.main_cat_slug
                            FROM
                                wp_mt_main_category mc 
                            WHERE
                                mc.idmain_category = %d", $category_id));

        } else if (isset($_GET['action']) && $_GET['action'] === 'edit' && $_GET['type'] === 'sub' && isset($_GET['id'])) {
            
            $category_id = intval($_GET['id']);
            $sub_category_query = $wpdb->get_row($wpdb->prepare("
                                SELECT
                                mc.idmain_category,
                                mc.main_cat_name,
                                mc.main_cat_slug,
                                sc.idsub_category,
                                sc.sub_cat_name,
                                sc.sub_cat_slug
                            FROM
                                wp_mt_main_category mc
                            LEFT JOIN wp_mt_sub_category sc ON
                                sc.idmain_category = mc.idmain_category
                            WHERE
                                sc.idsub_category = %d", $category_id));
        }


        ?>


        <div class="wrap">
            <div class="form-wrap">
                <h2><?php echo isset($categoryType) ? 'Update Category' : 'Category Registration'; ?></h2>
                <form id="categoryForm" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('register_category_action', 'register_category_nonce'); ?>
                    <!-- <input type="hidden" name="action" value="save_category"> -->
                    <input type="hidden" name="action" value="<?php echo isset($categoryType) ? 'update_category' : 'save_category'; ?>">
                    <input type="hidden" name="maincategory_id" value="<?php echo isset($main_category_query) ? esc_attr($main_category_query->idmain_category) : ''; ?>">
                    <input type="hidden" name="subcategory_id" value="<?php echo isset($sub_category_query) ? esc_attr($sub_category_query->idsub_category) : ''; ?>">

                    <div class="form-field form-required term-name-wrap">
                        <label for="tag-name">Name</label>
                        <input name="tag-name" id="tag-name" type="text" value="<?php echo ($_GET['type'] == "main") ? esc_attr($main_category_query->main_cat_name) : esc_attr($sub_category_query->sub_cat_name);?>" size="40" aria-required="true"
                            aria-describedby="name-description">
                        <p id="name-description">The name is how it appears on your site.</p>
                    </div>
                    <div class="form-field term-slug-wrap">
                        <label for="tag-slug">Slug</label>
                        <input name="slug" id="tag-slug" type="text" value="<?php echo ($_GET['type'] == "main") ? esc_attr($main_category_query->main_cat_slug) : esc_attr($sub_category_query->sub_cat_slug);?>" size="40" aria-describedby="slug-description">
                        <p id="slug-description">The “slug” is the URL-friendly version of the name. It is usually all
                            lowercase and contains only letters, numbers, and hyphens.</p>
                    </div>
                    <div class="form-field term-parent-wrap">
                        <label for="parent">Parent Category</label>
                        <select name="parent" id="parent" class="postform">
                            <option value="-1" <?php echo ($_GET['type'] == "main") ? 'selected' : ''; ?>>None</option>
                            <?php foreach ($main_categories as $category) { ?>
                                <option value="<?php echo esc_attr($category->idmain_category); ?>" 
                                    <?php echo ($_GET['type'] != "main" && $sub_category_query->idmain_category == $category->idmain_category) ? 'selected' : ''; ?>>
                                    <?php echo esc_html($category->main_cat_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-field term-description-wrap">
                        <label for="tag-description">Description</label>
                        <textarea name="description" id="tag-description" rows="5" cols="40"
                            aria-describedby="description-description"></textarea>
                        <p id="description-description">The description is not prominent by default; however, some themes
                            may show it.</p>
                    </div>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo isset($categoryType) ? 'Update Product' : 'Register Product'; ?>">
                        <span class="spinner"></span>
                    </p>
                </form>
            </div>
        </div>
        <div class="wrap">
            <h2>Registered Categories</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Type</th>
                        <th>Parent Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $last_main_id = 0;
                    foreach ($categories as $category):
                        if ($last_main_id !== $category->idmain_category) {
                            $last_main_id = $category->idmain_category;
                            echo "<tr>
                                <td>{$category->idmain_category}</td>
                                <td><strong>{$category->main_cat_name}</strong></td>
                                <td>{$category->main_cat_slug}</td>
                                <td>Main Category</td>
                                <td> - - - </td>
                                <td>
                                    <a href='" . admin_url("admin.php?page=category-menu&action=edit&id={$category->idmain_category}&type=main") . "'>Edit</a> | 
                                    <a href='" . wp_nonce_url(admin_url("admin-post.php?action=delete_category&id={$category->idmain_category}&type=main"), 'delete_category_action', 'delete_category_nonce') . "' onclick=\"return confirm('Are you sure?');\">Delete</a>
                                </td>
                            </tr>";
                        }
                        if ($category->idsub_category) {
                            echo "<tr>
                                <td>---</td>
                                <td>--{$category->sub_cat_name}</td>
                                <td>{$category->sub_cat_slug}</td>
                                <td>Sub Category</td>
                                <td>{$category->main_cat_name}</td>
                                <td>
                                    <a href='" . admin_url("admin.php?page=category-menu&action=edit&id={$category->idsub_category}&type=sub") . "'>Edit</a> | 
                                    <a href='" . wp_nonce_url(admin_url("admin-post.php?action=delete_category&id={$category->idsub_category}&type=sub"), 'delete_category_action', 'delete_category_nonce') . "' onclick=\"return confirm('Are you sure?');\">Delete</a>
                                </td>
                            </tr>";
                        }
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>

    <?php
    }

}

