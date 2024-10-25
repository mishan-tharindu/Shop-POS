<?php

namespace Inc\Admin;

class ViewPluginHomePage {

    public function displayhome_hooks() {

    }


    public function view_pluginhomepage() {
        ?>
            <div class="dashboard-container">
                <div class="grid-container">
                        <!-- Add your graph here -->
                        <?php
                            global $wpdb;

                            $last_7_days = [];
                            for ($i = 6; $i >= 0; $i--) {
                                $last_7_days[] = date('Y-m-d', strtotime("-$i days"));
                            }

                            $query = "
                                SELECT 
                                    DATE(date) as sale_date, 
                                    SUM(payment) as total_sales 
                                FROM 
                                    {$wpdb->prefix}mt_invoice 
                                WHERE 
                                    DATE(date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                GROUP BY 
                                    DATE(date)
                                ORDER BY 
                                    sale_date ASC
                            ";

                            $sales_data = $wpdb->get_results($query);

                            $dates = [];
                            $sales = [];

                            $sales_lookup = [];
                            foreach ($sales_data as $data) {
                                $sales_lookup[$data->sale_date] = floatval($data->total_sales);
                            }

                            foreach ($last_7_days as $date) {
                                $dates[] = $date;
                                $sales[] = isset($sales_lookup[$date]) ? $sales_lookup[$date] : 0;
                            }

                            $dates_json = json_encode($dates);
                            $sales_json = json_encode($sales);

                            $colors = [
                                'rgba(255, 99, 132, 0.5)',  // Red
                                'rgba(54, 162, 235, 0.5)',  // Blue
                                'rgba(255, 206, 86, 0.5)',  // Yellow
                                'rgba(75, 192, 192, 0.5)',  // Green
                                'rgba(153, 102, 255, 0.5)', // Purple
                                'rgba(255, 159, 64, 0.5)',  // Orange
                                'rgba(199, 199, 199, 0.5)'  // Grey
                            ];
                            
                            $border_colors = [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(199, 199, 199, 1)'
                            ];

                            ?>

                            <div class="grid-item graph">
                                <h2>Last Week Sales Bar Graph</h2>
                                <canvas id="salesChart"></canvas>
                            </div>

                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                                const ctx = document.getElementById('salesChart').getContext('2d');
                                const salesChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: <?php echo $dates_json; ?>,
                                        datasets: [{
                                            label: 'Total Sales',
                                            data: <?php echo $sales_json; ?>,
                                            backgroundColor: <?php echo json_encode($colors); ?>,
                                            borderColor: <?php echo json_encode($border_colors); ?>,
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                title: {
                                                    display: true,
                                                    text: 'Sales Amount (in Rs.)'
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Date'
                                                }
                                            }
                                        }
                                    }
                                });
                            </script>
                    <div class="grid-item buttons">
                        <button class="material-btn">Return Products</button>
                        <button class="material-btn">Add Discount</button>
                        <button class="material-btn">Invoice Check</button>
                        <button class="material-btn">Add Product</button>
                    </div>
                    <div class="grid-item low-stock">
                        <h2>Low Stock Products View</h2>
                        <?php $this->display_low_stock_products(); ?>
                        <!-- Add your low stock products view here -->
                    </div>
                    <div class="grid-item best-sales">
                        <h2>Best Sales Products Last Week</h2>
                        <!-- Add your best sales products view here -->
                    </div>
                </div>
            </div>
        <?php
    }

    public function get_low_stock_products() {
        global $wpdb;
        $table_product_stock = $wpdb->prefix . 'mt_product_stock';
    
        // Query to get all products with quantity <= 3
        $low_stock_products = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_product_stock WHERE qty <= %d",
            3
        ));
    
        return $low_stock_products;
    }

    public function display_low_stock_products() {
        $low_stock_products = $this->get_low_stock_products();
    
        echo '<div class="low-stock-container">';
        echo '<h3>Low Stock Products</h3>';
    
        if (!empty($low_stock_products)) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Product Name</th><th>SKU</th><th>Quantity</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($low_stock_products as $product) {
                echo '<tr>';
                echo '<td>' . esc_html($product->product_name) . '</td>';
                echo '<td>' . esc_html($product->sku) . '</td>';
                echo '<td>' . esc_html($product->qty) . '</td>';
                echo '</tr>';
            }
    
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No products are low in stock.</p>';
        }
    
        echo '</div>';
    }


}