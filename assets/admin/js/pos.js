jQuery(document).ready(function($) {
    $('#productSearch').on('input', function() {
        const query = $(this).val().trim();
        if (query.length > 2) { // Start searching after 3 characters

            // console.log('Sending AJAX to:', ajax_object.ajax_url);
            // console.log('Data:', {action: 'search_product', query: $('#productSearch').val()});
            
            jQuery.ajax({
                url: ajax_object.ajax_url,
                method: 'POST',
                data: {
                    action: 'search_product',  // This should match the action hook in PHP.
                    query: $('#productSearch').val(),  // Ensure the input ID is correct.
                    nonce: ajax_object.nonce  // Sending nonce for security.
                },
                success: function(response) {
                    console.log(response);  // Check the response for debugging.
                    displaySearchResults(response.data);
                },
                error: function(xhr) {
                    console.error('AJAX error:', xhr.responseText);  // Log detailed error.
                }
            });

        } else {
            $('#searchResults').empty(); // Clear results if the input is too short
        }
    });

    const taxRate = 0.1;

    function displaySearchResults(products) {
        let resultsContainer = $('#searchResults');
        resultsContainer.empty(); // Clear previous results

        products.forEach(product => {
            let resultItem = $('<div>')
                .addClass('result-item')
                .text(`${product.name} (SKU: ${product.sku}) - Rs.${product.price}`)
                .data('product', product)
                .on('click', function() {
                    addProductToCart($(this).data('product'));
                    resultsContainer.empty(); // Clear results after selection
                    $('#productSearch').val(''); // Clear the search field
                    $('#productSearch').focus(); // Auto-focus on the search field
                });

            resultsContainer.append(resultItem);
        });

            // If only one result and user presses Enter, add it to the cart
        if (products.length === 1) {
            $('#productSearch').off('keypress').on('keypress', function(e) {
                if (e.which === 13) {  // 13 is the enter key
                    addProductToCart(products[0]);
                    resultsContainer.empty();
                    $('#productSearch').val('');
                    $('#productSearch').focus(); // Auto-focus on the search field
                }
            });
        }

    }

    function addProductToCart(product) {
        let cartTable = $('#cartTable tbody');
        let existingRow = cartTable.find(`tr[data-sku="${product.sku}"]`);

        if (existingRow.length) {
            let quantityCell = existingRow.find('.quantity');
            let quantity = parseInt(quantityCell.text()) + 1;
            quantityCell.text(quantity);
            existingRow.find('.total').text(`Rs.${(quantity * product.price).toFixed(2)}`);
        } else {
            let newRow = $('<tr>').attr('data-sku', product.sku);
            newRow.append(`<td>${product.name}</td>`);
            newRow.append(`<td class="quantity">1</td>`);
            // newRow.append(`<td ><button class="decrease-quantity" >-</button>
            // <span id="quantity-${product.id}" class="quantity">1</span>
            // <button class="increase-quantity" >+</button></td>`);
            newRow.append(`<td>Rs.${product.price}</td>`);
            newRow.append(`<td class="total">Rs.${product.price}</td>`);
            newRow.append('<td><button onclick="removeProductFromCart(this)">Remove</button></td>');

            cartTable.append(newRow);
        }

        updateCartTotals();
    }

    function updateCartTotals() {
        let subtotal = 0;
        $('#cartTable tbody tr').each(function() {
            subtotal += parseFloat($(this).find('.total').text().replace('Rs.', ''));
        });

        $('#subtotal').text(subtotal.toFixed(2));
        applyDiscount(); // Update totals with discount and tax
    }

    window.removeProductFromCart = function(button) {
        $(button).closest('tr').remove();
        updateCartTotals();
    };

    // Increase quantity
    $('#cartTable').on('click', '.increase-quantity', function() {
        let row = $(this).closest('tr');
        let quantitySpan = row.find('.quantity');
        let quantity = parseInt(quantitySpan.text()) + 1;
        quantitySpan.text(quantity);
        updateLineTotal(row, parseFloat(row.find('td:nth-child(3)').text().slice(1)), quantity);
        // updateCartTotals();
    });

    // Decrease quantity
    $('#cartTable').on('click', '.decrease-quantity', function() {
        let row = $(this).closest('tr');
        let quantitySpan = row.find('.quantity');
        let quantity = parseInt(quantitySpan.text()) - 1;
        if (quantity < 1) quantity = 1; // Prevents quantity from going below 1
        quantitySpan.text(quantity);
        updateLineTotal(row, parseFloat(row.find('td:nth-child(3)').text().slice(1)), quantity);
        // updateCartTotals();
    });

function updateLineTotal(row, price, quantity) {
    let totalCell = row.find('.total');
    totalCell.text(`Rs.${(price * quantity).toFixed(2)}`);
    updateCartTotals(); // Update the overall cart totals
}


});

const taxRate = 0.1;

function applyDiscount() {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent);
    const discountRate = parseFloat(document.getElementById('discount').value) / 100;
    const discount = subtotal * discountRate;
    const tax = (subtotal - discount) * taxRate;
    const total = subtotal - discount + tax;

    document.getElementById('tax').textContent = tax.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
}

