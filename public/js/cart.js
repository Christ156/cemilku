// cart.js
console.log(
    "cart.js loaded! Current timestamp: " + new Date().toLocaleString()
);

var total_price_cart = 0; // Ini adalah total harga dari item yang dipilih (sebelum ongkir)
var total_item_check = 0;
var total_price_with_ship = 0; // Ini adalah total harga dari item yang dipilih + ongkir

var allCheck = 0;
const SHIPPING_COST = 20000; // Define shipping cost here, MUST BE CONSISTENT WITH PHP CONTROLLER

// Fungsi untuk memperbarui hidden input total_price di form checkout
function updateHiddenTotalPrice() {
    const hiddenTotalPriceInput = document.getElementById('total_price');
    if (hiddenTotalPriceInput) {
        hiddenTotalPriceInput.value = total_price_with_ship;
        console.log("Hidden total_price updated to: " + hiddenTotalPriceInput.value);
    } else {
        console.warn("Hidden input 'total_price' not found in the form.");
    }
}


function allCheckboxCheck(count, carts) {
    var cart_items = document.getElementsByClassName("product-checkbox");
    var checkbox_all = document.getElementById("select_all");

    if (
        allCheck == 0 &&
        total_item_check == 0 &&
        checkbox_all.checked == true
    ) {
        total_price_cart = 0;
        for (var i = 0; i < count; i++) {
            cart_items[i].checked = true;
            total_price_cart =
                total_price_cart + parseInt(carts[i]["total_price"]);
            document.getElementById("item_cart_" + carts[i]["id"]).value =
                "true";
        }
        allCheck = 1;
        total_item_check = count;
    } else if (
        allCheck == 1 &&
        total_item_check == count &&
        checkbox_all.checked == false
    ) {
        for (var i = 0; i < count; i++) {
            cart_items[i].checked = false;
            // total_price_cart = total_price_cart - parseInt(carts[i]["total_price"]); // No need to subtract here, will be recalculated
            document.getElementById("item_cart_" + carts[i]["id"]).value =
                "false";
        }

        allCheck = 0;
        total_item_check = 0;
        total_price_cart = 0; // Reset total price when all are unchecked
    } else if (total_item_check != count) {
        // This block handles cases where not all are checked, but 'select all' is clicked
        total_price_cart = 0; // Reset before recalculating for select all
        for (var i = 0; i < count; i++) {
            if (cart_items[i].checked == false) { // Only check if false, because if true, it's already counted
                cart_items[i].checked = true;
                total_item_check = total_item_check + 1;
            }
            // Always add the price if the item is now checked
            total_price_cart =
                total_price_cart + parseInt(carts[i]["total_price"]);
            document.getElementById("item_cart_" + carts[i]["id"]).value =
                "true";
        }

        allCheck = 1; // Now all are checked
        total_item_check = count; // Ensure count is correct
    }

    removeSelected(total_item_check);
    total_price_with_ship = total_price_cart + SHIPPING_COST; // Use the defined shipping cost

    document.getElementById("total_price_cart").innerText =
        total_price_cart.toLocaleString("id-ID");
    document.getElementById("total_price_with_ship").innerText =
        total_price_with_ship.toLocaleString("id-ID");
    document.getElementById("product-count").innerText = total_item_check;
    // Update the hidden input for form submission
    updateHiddenTotalPrice();
    // Re-check button status after price update
    checkItemSelected(item_count, all_carts, primary_address);
}

function previewPrice(price, cart_item_id) {
    var checkbox = document.getElementById("checkbox_item" + cart_item_id);

    if (checkbox.checked == false) {
        total_price_cart = total_price_cart - price;
        total_item_check = total_item_check - 1;
        document.getElementById("item_cart_" + cart_item_id).value = "false";
    } else {
        total_price_cart = total_price_cart + price;
        total_item_check = total_item_check + 1;
        document.getElementById("item_cart_" + cart_item_id).value = "true";
    }

    total_price_with_ship = total_price_cart + SHIPPING_COST; // Use the defined shipping cost

    document.getElementById("product-count").innerText = total_item_check;
    document.getElementById("total_price_cart").innerText =
        total_price_cart.toLocaleString("id-ID");
    document.getElementById("total_price_with_ship").innerText =
        total_price_with_ship.toLocaleString("id-ID");
    // Update the hidden input for form submission
    updateHiddenTotalPrice();
    // Re-check button status after price update
    checkItemSelected(item_count, all_carts, primary_address);
}

function allCheckTrue(count) {
    var checkedTrue = 0;
    var checkedFalse = 0;
    var cart_items = document.getElementsByClassName("product-checkbox");

    for (var i = 0; i < count; i++) {
        if (cart_items[i].checked == true) {
            checkedTrue = checkedTrue + 1;
        } else {
            checkedFalse = checkedFalse + 1;
        }
    }

    if (checkedTrue == count) {
        document.getElementById("select_all").checked = true;
        allCheck = 1;
    } else {
        document.getElementById("select_all").checked = false;
        allCheck = 0;
    }

    removeSelected(total_item_check);
    // Re-check button status after allCheckTrue
    checkItemSelected(item_count, all_carts, primary_address);
}

function removeSelected(count_checked) {
    if (count_checked > 0) {
        document.getElementById("remove-btn").style.display = "Block";
    } else {
        document.getElementById("remove-btn").style.display = "None";
    }
}

function checkItemSelected(count, carts, address) {
    var selectedItem = 0;
    var selectedPayment = 0;
    var selectedAddress = 0;

    for (var i = 0; i < count; i++) {
        // Ensure to check the actual checkbox state for item selection
        if (document.getElementById("checkbox_item" + carts[i]["id"]).checked) {
            selectedItem = 1;
            break;
        }
    }

    for (var i = 1; i <= 4; i++) { // Sesuaikan loop ini dengan jumlah metode pembayaran yang Anda miliki
        if (document.getElementById("payment_method_" + i) && document.getElementById("payment_method_" + i).checked) {
            selectedPayment = 1;
            break;
        }
    }

    if (address == 1) { // This `address` parameter should be passed correctly from Blade
        selectedAddress = 1;
    }

    const checkoutBtn = document.getElementById("checkout_btn");
    if (selectedItem == 1 && selectedPayment == 1 && selectedAddress == 1) {
        if (checkoutBtn) checkoutBtn.disabled = false;
    } else {
        if (checkoutBtn) checkoutBtn.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var addAddressCartModal = document.getElementById('addAddressCartModal');
    var addressCartModal = document.getElementById('addressCartModal');

    // Fungsi untuk membersihkan backdrop ekstra yang tidak dikelola oleh modal aktif.
    function cleanUpExtraModalBackdrops() {
        var allBackdrops = document.querySelectorAll('.modal-backdrop');
        var openModals = document.querySelectorAll('.modal.show');

        if (openModals.length === 0) {
            allBackdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            return;
        }

        let expectedBackdropCount = 0;
        if (addressCartModal && addressCartModal.classList.contains('show')) {
            expectedBackdropCount++;
        }
        if (addAddressCartModal && addAddressCartModal.classList.contains('show')) {
            expectedBackdropCount++;
        }

        // Hapus backdrop yang berlebihan
        for (let i = allBackdrops.length - 1; i >= 0; i--) {
            if (expectedBackdropCount > 0) {
                expectedBackdropCount--;
            } else {
                allBackdrops[i].remove();
            }
        }
    }


    if (addAddressCartModal && addressCartModal) {
        // Event listener saat addAddressCartModal akan ditampilkan (show.bs.modal)
        addAddressCartModal.addEventListener('show.bs.modal', function () {
            // Sembunyikan modal induk agar tidak tumpang tindih
            if (addressCartModal.classList.contains('show')) {
                addressCartModal.style.display = 'none';
                // Hapus backdrop modal induk yang mungkin sudah ada
                const parentBackdrop = document.querySelector('.modal-backdrop');
                if (parentBackdrop) {
                    parentBackdrop.remove();
                }
            }
        });

        // Event listener saat addAddressCartModal sudah ditampilkan (shown.bs.modal)
        addAddressCartModal.addEventListener('shown.bs.modal', function () {
            // Setelah modal anak muncul, pastikan backdrop-nya memiliki z-index yang benar
            // dan warna yang gelap.
            const currentBackdrop = document.querySelector('.modal-backdrop.fade.show');
            if (currentBackdrop) {
                currentBackdrop.style.zIndex = '1054'; // Z-index yang benar untuk backdrop modal anak
                currentBackdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.85)'; // Warna hitam dengan opasitas 85%
            }
        });


        // Event listener saat addAddressCartModal ditutup (hidden.bs.modal)
        addAddressCartModal.addEventListener('hidden.bs.modal', function () {
            // Tampilkan kembali modal induk
            addressCartModal.style.display = 'block';
            var bsModal = new bootstrap.Modal(addressCartModal);
            bsModal.show();
            // Bersihkan backdrop ekstra setelah semua proses modal selesai
            cleanUpExtraModalBackdrops();
        });
    }

    // Event listener untuk addressCartModal
    if (addressCartModal) {
        // Saat addressCartModal ditampilkan, pastikan backdrop-nya ada
        addressCartModal.addEventListener('shown.bs.modal', function() {
            const currentBackdrop = document.querySelector('.modal-backdrop.fade.show');
            if (currentBackdrop) {
                // Default Bootstrap z-index untuk backdrop pertama adalah 1040
                currentBackdrop.style.zIndex = '1040';
                currentBackdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.85)'; // Disamakan menjadi opasitas 85%
            } else {
                // Jika tidak ada backdrop, buat satu
                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.style.zIndex = '1040';
                backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.85)'; // Disamakan menjadi opasitas 85%
                document.body.appendChild(backdrop);
            }
        });

        // Saat addressCartModal ditutup, bersihkan backdrop
        addressCartModal.addEventListener('hidden.bs.modal', function () {
            cleanUpExtraModalBackdrops();
        });
    }

    // Initialize all checkboxes and price on page load
    // This part ensures initial state is correct for checkboxes and prices.
    // Initial calculation when page loads
    // You might need to call a function here to initialize total_price_cart
    // and total_price_with_ship based on initially checked items if any.
    // For simplicity, let's assume all checkboxes are unchecked on load and user checks them.
    // If you have initially checked items, you'll need a function like:
    // initializeCartState(item_count, all_carts);
    //
    // Initial check of checkout button state on DOMContentLoaded
    // Ensure `item_count`, `all_carts`, `primary_address` are defined in your Blade file
    // and passed to this JS.
    if (typeof item_count !== 'undefined' && typeof all_carts !== 'undefined' && typeof primary_address !== 'undefined') {
        checkItemSelected(item_count, all_carts, primary_address);
    }
});


function updateQuantity(state, id) {
    const form_update = document.getElementById("set-quantity-cart");
    const cart_item_id = document.getElementById("cart_item_id");
    const quantity_item = document.getElementById("quantity_item");
    const quantity_cart = document.getElementById("quantity_cart_" + id);

    if (!quantity_cart) {
        console.error(
            "Error: quantity_cart element with ID 'quantity_cart_" +
                id +
                "' not found."
        );
        return;
    }

    var quantity = parseInt(quantity_cart.value);

    if (quantity >= 1) {
        if (state == "add") {
            quantity = quantity + 1;
        } else {
            quantity = quantity - 1;
        }

        if (quantity != 0) {
            if (cart_item_id) cart_item_id.value = id;
            if (quantity_item) quantity_item.value = quantity;
            quantity_cart.value = quantity;
            if (form_update) form_update.submit();
        } else {
            // If quantity becomes 0, set it back to 1 and submit
            if (cart_item_id) cart_item_id.value = id;
            if (quantity_item) quantity_item.value = 1;
            quantity_cart.value = 1; // Prevent quantity from going below 1
            if (form_update) form_update.submit();
        }
    } else {
        // If initial quantity is less than 1, set to 1 and submit
        if (cart_item_id) cart_item_id.value = id;
        if (quantity_item) quantity_item.value = 1;
        quantity_cart.value = 1; // Prevent quantity from going below 1
        if (form_update) form_update.submit();
    }
}

function updateQuantityByField(id) {
    const form_update = document.getElementById("set-quantity-cart");
    const quantity_cart = document.getElementById("quantity_cart_" + id);

    if (!quantity_cart) {
        console.error(
            "Error: quantity_cart element with ID 'quantity_cart_" +
                id +
                "' not found."
        );
        return;
    }

    var quantity = parseInt(quantity_cart.value);

    if (isNaN(quantity) || quantity < 1) { // Validate quantity from input field
        console.warn("Invalid quantity entered in field for item: " + id + ". Setting to 1.");
        quantity = 1; // Default to 1 if invalid
        quantity_cart.value = 1; // Update the field visually
    }

    if (cart_item_id) cart_item_id.value = id;
    if (quantity_item) quantity_item.value = quantity;
    if (form_update) form_update.submit();
}
