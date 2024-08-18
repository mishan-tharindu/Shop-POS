// document.addEventListener('DOMContentLoaded', function() {
//     document.querySelectorAll('.delete-button').forEach(button => {
//         button.addEventListener('click', function(event) {
//             event.preventDefault();
//             if (confirm('Are you sure you want to delete this product?')) {
//                 const productId = this.getAttribute('data-id');
//                 const form = document.createElement('form');
//                 form.method = 'POST';
//                 form.action = admin_url('admin-post.php');
//                 form.innerHTML = `<input type="hidden" name="action" value="delete_product">
//                                    <input type="hidden" name="product_id" value="${productId}">`;
//                 document.body.appendChild(form);
//                 form.submit();
//             }
//         });
//     });

//     // Handle Edit functionality similarly
// });

console.log("Loada All Admin Scripts !!! ");


// document.getElementById('productImages').addEventListener('change', function(event) {
//     const imagePreview = document.getElementById('imagePreview');
//     imagePreview.innerHTML = ''; // Clear previous images
    
//     const files = event.target.files;
//     Array.from(files).forEach(file => {
//         const reader = new FileReader();
//         reader.onload = function(e) {
//             const img = document.createElement('img');
//             img.src = e.target.result;
//             imagePreview.appendChild(img);
//         }
//         reader.readAsDataURL(file);
//     });
// });

document.addEventListener('DOMContentLoaded', function() {
    const imagePreview = document.getElementById('imagePreview');
    const productImagesInput = document.getElementById('productImages');

    // Handle image file selection and preview
    productImagesInput.addEventListener('change', function(event) {
        const files = event.target.files;
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.classList.add('image-item');
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Product Image" width="100">
                    <span class="remove-image" style="cursor: pointer;">&times;</span>
                `;
                imagePreview.appendChild(div);

                // Remove image from preview on click
                div.querySelector('.remove-image').addEventListener('click', function() {
                    div.remove();
                });
            }
            reader.readAsDataURL(file);
        });
    });

    // Remove existing image from preview
    imagePreview.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-image')) {
            const imageItem = event.target.closest('.image-item');
            if (imageItem) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_images[]';
                input.value = imageItem.getAttribute('data-image-url');
                imagePreview.appendChild(input);
                imageItem.remove();
            }
        }
    });
});

// document.getElementById('productForm').addEventListener('submit', function(event) {
//     event.preventDefault();

//     // Collect form data
//     const productName = document.getElementById('productName').value;
//     const sku = document.getElementById('sku').value;
//     const description = document.getElementById('description').value;
//     const category = document.getElementById('category').value;
//     const price = document.getElementById('price').value;
//     const quantity = document.getElementById('quantity').value;
//     const supplier = document.getElementById('supplier').value;
//     const productImages = document.getElementById('productImages').files;

//     // Display success message
//     const successMessage = document.getElementById('successMessage');
//     successMessage.textContent = `Product "${productName}" registered successfully with ${productImages.length} images!`;

//     // Reset form
//     document.getElementById('productForm').reset();
//     document.getElementById('imagePreview').innerHTML = ''; // Clear image previews
// });

