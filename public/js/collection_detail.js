function setQuantity(action, maxStock) {
    const quantityInput = document.getElementById("value_quantity");
    let currentQuantityValue = parseInt(quantityInput.value);

    if (isNaN(currentQuantityValue)) {
        currentQuantityValue = 1;
    }

    if (action == 'add') {
        currentQuantityValue = currentQuantityValue + 1;
    } else {
        currentQuantityValue = currentQuantityValue - 1;
    }

    checkQuantityValid(maxStock, currentQuantityValue);
}

function checkQuantityValid(max_quantity, value_from_input_or_set_quantity = null) {
    const quantityInput = document.getElementById("value_quantity");
    let currentQuantity = value_from_input_or_set_quantity !== null ? value_from_input_or_set_quantity : parseInt(quantityInput.value);

    if (isNaN(currentQuantity) || currentQuantity < 1) {
        currentQuantity = 1;
        showAlert("Quantity must be at least 1 pc!");
    } else if (currentQuantity > max_quantity) {
        currentQuantity = max_quantity;
        showAlert(`Oops! Only ${max_quantity} items left in stock.`);
    }
    quantityInput.value = currentQuantity;
}

function showAlert(message) {
    const alertContainer = document.getElementById("topAlertContainer");
    const alertMessage = document.getElementById("topAlertMessage");

    if (alertContainer && alertMessage) {
        alertMessage.textContent = message;
        alertContainer.classList.add("show");

        setTimeout(() => {
            alertContainer.classList.remove("show");
        }, 5000);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const addToCartButton = document.getElementById('add-to-cart-detail-btn');
    const addToCartForm = document.getElementById('addToCartForm');
    const quantityInput = document.getElementById('value_quantity');
    const itemId = document.getElementById('item-id').value;
    const itemStock = parseInt(document.getElementById('stock').value);

    const successModal = new bootstrap.Modal(document.getElementById('successAddToCartModal'));

    checkQuantityValid(itemStock);

    if (addToCartButton && addToCartForm) {
        addToCartButton.addEventListener('click', function() {
            checkQuantityValid(itemStock);
            const quantity = quantityInput.value;

            if (parseInt(quantity) < 1 || isNaN(parseInt(quantity))) {
                showAlert('Quantity must be at least 1.');
                return;
            }
            if (parseInt(quantity) > itemStock) {
                showAlert(`Quantity exceeds available stock (${itemStock}).`);
                return;
            }

            const url = `/collection/${itemId}/add-to-cart/${quantity}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('collection_id', itemId);

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        throw new Error('Server response was not JSON: ' + text);
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    successModal.show();
                } else {
                    console.error('Error adding to cart:', data.message);
                    showAlert(data.message || 'Failed to add product to cart.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('A network or server error occurred. Please try again.');
            });
        });
    }

    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            checkQuantityValid(itemStock);
        });
        quantityInput.addEventListener('blur', function() {
            checkQuantityValid(itemStock);
        });
    } else {
        console.error("ERROR: Quantity input element with ID 'value_quantity' not found!");
    }
});

