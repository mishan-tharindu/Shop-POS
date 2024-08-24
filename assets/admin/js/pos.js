let cart = [];
let billtotal = 0;

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

    const taxRate = 0;


    function displaySearchResults(products) {
        let resultsContainer = $('#searchResults');
        resultsContainer.empty(); // Clear previous results

        products.forEach(product => {

            console.log("Product Name :: "+ product.product_name +" -- SKU ::"+product.sku+" -- selling Price ::"+product.selling_price) ;

            let resultItem = $('<div>')
                .addClass('result-item')
                .text(`${product.product_name} (SKU: ${product.sku}) - Rs.${product.selling_price}`)
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
            existingRow.find('.total').text(`Rs.${(quantity * product.selling_price).toFixed(2)}`);
        } else {
            let newRow = $('<tr>').attr('data-sku', product.sku);
            newRow.append(`<td>${product.product_name} - ${product.size}</td>`);
            newRow.append(`<td class="quantity">1</td>`);
            // newRow.append(`<td ><button class="decrease-quantity" >-</button>
            // <span id="quantity-${product.id}" class="quantity">1</span>
            // <button class="increase-quantity" >+</button></td>`);
            newRow.append(`<td>Rs.${product.selling_price}</td>`);
            newRow.append(`<td class="total">Rs.${product.selling_price}</td>`);
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

const taxRate = 0;
// const cart = document.getElementById('cartTable').rows;
// const cart = [];

function applyDiscount() {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent);
    const discountRate = parseFloat(document.getElementById('discount').value) / 100;
    const discount = subtotal * discountRate;
    const tax = (subtotal - discount) * taxRate;
    const total = subtotal - discount + tax;

    document.getElementById('tax').textContent = tax.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
    document.getElementById('discount-price').textContent = discount.toFixed(2);
}



// Assuming finalizeSale function calculates totals and updates the DOM
function finalizeSale() {
    // Calculate and update totals, tax, discounts, etc.
    // Here, add your logic or ensure it's updated before print

                // Select the source and destination tables
                const sourceTable = document.getElementById('cartTable');
                const destinationTable = document.getElementById('invoiceTable');
        
                // Get all rows from the source table
                const rows = sourceTable.getElementsByTagName('tr');
        
                // Loop through each row (skipping the header row)
                for (let i = 1; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    if (cells.length > 0) {
                        // Remove the last cell
                        cells[cells.length - 1].remove();
        
                        // Clone the row (excluding the last cell)
                        const newRow = rows[i].cloneNode(true);
        
                        // Append the modified row to the destination table
                        destinationTable.appendChild(newRow);
                    }
                }
                
    // const invoiceTable_body = document.getElementById('invoiceTable').getElementsByTagName('tbody')[1];

    // Example of updating invoice details directly
    document.getElementById('invoiceDate').innerText = new Date().toLocaleDateString();
    // Similarly, update other invoice fields like totals and payment method
    document.getElementById('invoicePaymentMethod').innerText = document.getElementById('paymentMethod').value;
    document.getElementById('invoiceSubtotal').innerText = document.getElementById('subtotal').innerText;
    document.getElementById('invoiceTotal').innerText = document.getElementById('total').innerText;
    document.getElementById('invoiceTax').innerText = document.getElementById('tax').innerText;
    document.getElementById('invoiceDiscount').innerText = document.getElementById('discount').value;

    // Show the invoice
    document.getElementById('invoice').style.display = 'block';
    const cartTables = document.getElementById('cartTable').getElementsByTagName('tbody')[0];
    cartTables.innerHTML = ''; // Clear existing items


    // Print the invoice
    printInvoice();
}

function printInvoice() {
    // window.print(); // Trigger the print dialog
    // document.getElementById('invoice').print();
    // document.getElementById('invoice').style.display = 'none'; // Optionally hide the invoice again after printing

    // var invoiceContent = document.getElementById('invoice').innerHTML;
    // var originalContent = document.body.innerHTML;

    // document.body.innerHTML = invoiceContent;
    // window.print();
    // document.body.innerHTML = originalContent;


    // const printWindow = window.open('', '', 'height=500,width=800');
    // printWindow.document.write('<html><head><title>Invoice</title>');
    // printWindow.document.write('</head><body >');
    // printWindow.document.write(document.getElementById('invoice').outerHTML);
    // printWindow.document.write('</body></html>');
    // printWindow.document.close();
    // printWindow.print();

    // // 2nd way

    const printWindow = window.open('', '', 'height=500,width=800');
    printWindow.document.write('<html><head><title>Invoice</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('#invoiceTable { width: 100%; border-collapse: collapse; }');
    printWindow.document.write('#invoiceTable th, #invoiceTable td { border: 1px solid #ddd; padding: 8px; }');
    printWindow.document.write('#invoiceTable th { text-align: left; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body >');
    printWindow.document.write(document.getElementById('invoice').outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();

  }

  jQuery(document).ready(function($) {
    $('.cash-payment').off('keypress').on('keypress', function(e) {
        if (e.which === 13) {
            // console.log("Press Cash Field !!");
            billtotal = parseFloat(document.getElementById('total').textContent);
            // cash = $('#cash-balance').val();
            cashInput = parseFloat(document.getElementById('cashbalance').value);

            // console.log("billtotal ::: "+billtotal+" -- cash ::: "+cash+" Cash input ::: "+cashInput);

            cashbalance = cashInput - billtotal;
            // console.log("cashbalance ::: "+cashbalance);

            document.getElementById("cash-balance").innerHTML = cashbalance;
        }
    });
});

function saveInvoice() {
    const invoiceTableBody = document.getElementById('invoiceTable').getElementsByTagName('tbody')[0];
    const sourceTableRows = document.getElementById('cartTable').getElementsByTagName('tr');

    let totalQty = 0;
    const cartItems = [];
    const invoiceID = generateInvoiceNumber();

    for (let i = 1; i < sourceTableRows.length; i++) {
        const cells = sourceTableRows[i].getElementsByTagName('td');
        if (cells.length > 0) {
            const newRow = sourceTableRows[i].cloneNode(true);
            newRow.removeChild(newRow.lastChild); // Remove the 'Remove' button cell
            invoiceTableBody.appendChild(newRow);

            const qty = parseInt(cells[2].textContent);
            totalQty += qty;

            cartItems.push({
                sku: cells[0].textContent.trim(),
                quantity: qty,
                price: parseFloat(cells[3].textContent.replace('Rs.', '').trim())
            });
        }
    }

    const invoiceData = {
        invoiceId: invoiceID,
        qty: totalQty,
        discount: parseFloat(document.getElementById('discount').value) || 0,
        paymentMethod: document.getElementById('paymentMethod').value,
        subtotal: parseFloat(document.getElementById('subtotal').textContent) || 0,
        tax: parseFloat(document.getElementById('tax').textContent) || 0,
        total: parseFloat(document.getElementById('total').textContent) || 0,
        cartItems: cartItems
    };

    const xhr = new XMLHttpRequest();
    xhr.open('POST', ajax_object.ajax_url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Invoice saved successfully.');
            document.getElementById('invoice').style.display = 'block';
            printInvoice();
        }
    };

    const params = `action=save_invoice&nonce=${ajax_object.nonce}&invoiceData=${encodeURIComponent(JSON.stringify(invoiceData))}`;
    xhr.send(params);
}

// Generate a unique invoice number
function generateInvoiceNumber() {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `INC${year}${month}${day}${hours}${minutes}${seconds}`;
}

// function changeCashBalance() {
//      // console.log("Press Cash Field !!");
//      billtotal = parseFloat(document.getElementById('total').textContent);
//      cashInput = parseFloat(document.getElementById('cashbalance').value);

//      console.log("billtotal ::: "+billtotal+"  Cash input ::: "+cashInput);

//      cashbalance = cashInput - billtotal;
//      console.log("cashbalance ::: "+cashbalance);

//      document.getElementById("cash-balance").innerHTML = cashbalance;
// }

