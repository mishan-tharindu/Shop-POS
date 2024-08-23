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
            echo '<div class="updated notice"><p>Product successfully registered!</p></div>';
        }else if (isset($_GET['status']) && $_GET['status'] === 'error') {
            '<div class="error notice"><p>Failed to register product Category!</p></div>';
        }

        ?>


        <div class="wrap">
            <div class="form-wrap">
                <h2>Add New Category</h2>
                <form id="categoryForm" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('register_category_action', 'register_category_nonce'); ?>
                    <input type="hidden" name="action" value="save_category">

                    <div class="form-field form-required term-name-wrap">
                        <label for="tag-name">Name</label>
                        <input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true"
                            aria-describedby="name-description">
                        <p id="name-description">The name is how it appears on your site.</p>
                    </div>
                    <div class="form-field term-slug-wrap">
                        <label for="tag-slug">Slug</label>
                        <input name="slug" id="tag-slug" type="text" value="" size="40" aria-describedby="slug-description">
                        <p id="slug-description">The “slug” is the URL-friendly version of the name. It is usually all
                            lowercase and contains only letters, numbers, and hyphens.</p>
                    </div>
                    <div class="form-field term-parent-wrap">
                        <label for="parent">Parent Category</label>
                        <select name="parent" id="parent" class="postform">
                            <option value="-1">None</option>
                            <?php foreach ($main_categories as $category) { ?>
                            <option value="<?php echo esc_attr($category->idmain_category); ?>">
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
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Category">
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
                                <td>
                                    <a href='#'>Edit</a> | 
                                    <a href='#' onclick=\"return confirm('Are you sure?');\">Delete</a>
                                </td>
                            </tr>";
                        }
                        if ($category->idsub_category) {
                            echo "<tr>
                                <td>---</td>
                                <td>--{$category->sub_cat_name}</td>
                                <td>{$category->sub_cat_slug}</td>
                                <td>Sub Category</td>
                                <td>
                                    <a href='#'>Edit</a> | 
                                    <a href='#' onclick=\"return confirm('Are you sure?');\">Delete</a>
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

