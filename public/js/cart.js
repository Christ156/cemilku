// 'cart' sekarang akan diinisialisasi dari data yang datang dari Blade (database)
let cart = [];
const SHIPPING_COST = 9500; // Biaya pengiriman tetap

// window.addToCart akan memanggil endpoint yang benar dan memicu refresh tampilan.
window.addToCart = function (item) {
    console.log("Adding item to cart:", item);

    // Data yang akan dikirim ke backend
    const payload = {
        collection_id: item.id,
        quantity: item.quantity
    };

    // Kirim data ke API (URL yang benar sesuai web.php)
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify(payload),
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Server error.');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log("Item added to cart successfully:", data);
            // Setelah sukses, muat ulang halaman untuk menampilkan data terbaru dari database
            window.location.reload();
        })
        .catch(error => {
            console.error("Error adding item to cart:", error);
            alert("Gagal menambahkan ke keranjang: " + error.message);
        });
};

document.addEventListener('DOMContentLoaded', function () {
    console.log("DEBUG: DOMContentLoaded fired for cart.js.");

    // Inisialisasi 'cart' dari data yang datang dari Blade
    if (typeof initialCartItems !== 'undefined' && Array.isArray(initialCartItems)) {
        cart = initialCartItems;
        console.log("DEBUG: Cart initialized from Blade data:", cart);
    } else {
        console.warn("WARNING: initialCartItems not found or not an array. Cart initialized as empty.");
        cart = [];
    }

    const cartProductList = document.getElementById('cart-product-list');

    if (cartProductList) {
        console.log("DEBUG: Running cart logic. cartProductList found.");

        const selectAllCheckbox = document.getElementById('select-all');
        const removeBtn = document.getElementById('remove-btn');
        const productCountSpan = document.getElementById('product-count');
        const productListSummary = document.getElementById('summary-product-list');
        const totalSpan = document.getElementById('total');

        if (!selectAllCheckbox) console.error("ERROR: selectAllCheckbox (id='select-all') not found!");
        if (!removeBtn) console.error("ERROR: removeBtn (id='remove-btn') not found!");
        if (!productCountSpan) console.error("ERROR: productCountSpan (id='product-count') not found!");
        if (!productListSummary) console.error("ERROR: productListSummary (id='summary-product-list') not found!");
        if (!totalSpan) console.error("ERROR: totalSpan (id='total') not found!");

        function renderCartItems() {
            console.log("DEBUG: renderCartItems called. Cart length:", cart.length);
            if (!cartProductList) {
                console.error("ERROR: cartProductList is null in renderCartItems. Cannot render.");
                return;
            }
            cartProductList.innerHTML = '';

            if (cart.length === 0) {
                cartProductList.innerHTML = '<p>Keranjang Anda kosong.</p>';
                if (removeBtn) removeBtn.classList.add('d-none');
            } else {
                if (removeBtn) removeBtn.classList.remove('d-none');
                cart.forEach((item, index) => {
                    console.log("DEBUG: Rendering item:", item.name, "at index:", index);
                    const productItemDiv = document.createElement('div');
                    productItemDiv.className = 'product-item d-flex justify-content-between align-items-center mb-3';
                    productItemDiv.dataset.index = index;
                    productItemDiv.dataset.price = item.price;
                    productItemDiv.dataset.name = item.name;

                    const isChecked = (item.selected === undefined || item.selected === true) ? 'checked' : '';

                    productItemDiv.innerHTML = `
                        <div class="d-flex align-items-center">
                            <input type="checkbox" class="product-checkbox me-2" ${isChecked}>
                            <img src="${item.image}" alt="${item.name}" class="product-img" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                    onerror="this.onerror=null;this.src='https://placehold.co/80x80/E2D2B0/52282A?text=No+Image';">
                            <div class="ms-2">
                                <h6 class="fw-bold mb-0">${item.name}</h6>
                                <small>${item.type || ''}</small> <!-- Menampilkan tipe -->
                                <div class="quantity-controls mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary minus-quantity" data-index="${index}">-</button>
                                    <span class="mx-2">${item.quantity || 1}</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary plus-quantity" data-index="${index}">+</button>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0 me-3">Rp ${(item.price * (item.quantity || 1)).toLocaleString('id-ID')}</p>
                    `;
                    cartProductList.appendChild(productItemDiv);

                    const checkbox = productItemDiv.querySelector('.product-checkbox');
                    checkbox.addEventListener('change', (event) => {
                        cart[index].selected = event.target.checked;
                        updateCartSummary();
                        updateRemoveButtonVisibility();
                    });

                    productItemDiv.querySelector('.minus-quantity').addEventListener('click', () => {
                        if (cart[index].quantity > 1) {
                            cart[index].quantity--;
                            updateQuantityInDatabase(item.id, cart[index].quantity);
                            renderCartItems();
                        }
                    });
                    productItemDiv.querySelector('.plus-quantity').addEventListener('click', () => {
                        // Validasi stok di sisi klien sebelum mengirim ke server
                        if (item.stock && cart[index].quantity < item.stock) { // Memeriksa jika ada stok dan kuantitas saat ini kurang dari stok
                            cart[index].quantity = (cart[index].quantity || 1) + 1;
                            updateQuantityInDatabase(item.id, cart[index].quantity);
                            renderCartItems();
                        } else if (item.stock && cart[index].quantity >= item.stock) {
                            alert(`Oops! Stok ${item.name} hanya tersedia ${item.stock}.`);
                        }
                    });
                });
            }
            updateCartSummary();
            updateRemoveButtonVisibility();
        }

        function updateQuantityInDatabase(itemId, newQuantity) {
            fetch('/cart/update-quantity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    collection_id: itemId,
                    quantity: newQuantity
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Failed to update quantity on server.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Quantity updated in database:", data);
                })
                .catch(error => {
                    console.error("Error updating quantity in database:", error);
                    alert("Gagal memperbarui kuantitas: " + error.message);
                    window.location.reload();
                });
        }

        function updateCartSummary() {
            let selectedProductCount = 0;
            let selectedProductsTotal = 0;
            let productListHtml = '';

            cart.forEach(item => {
                if (item.selected === undefined || item.selected === true) {
                    selectedProductCount += (item.quantity || 1);
                    selectedProductsTotal += (item.price * (item.quantity || 1));
                    productListHtml += `
                        <div class="d-flex justify-content-between">
                            <span>${item.name} x ${item.quantity || 1}</span>
                            <span>Rp ${(item.price * (item.quantity || 1)).toLocaleString('id-ID')}</span>
                        </div>
                    `;
                }
            });

            if (productCountSpan) productCountSpan.textContent = selectedProductCount;
            if (productListSummary) productListSummary.innerHTML = selectedProductCount > 0 ? productListHtml : '<em>No product selected</em>';

            const totalAmount = (selectedProductCount > 0 ? SHIPPING_COST : 0) + selectedProductsTotal;
            if (totalSpan) totalSpan.textContent = `Rp ${totalAmount.toLocaleString('id-ID')}`;

            if (selectAllCheckbox) {
                const allSelected = cart.length > 0 && cart.every(item => item.selected === undefined || item.selected === true);
                selectAllCheckbox.checked = allSelected;
            }
        }

        function updateRemoveButtonVisibility() {
            const anyCheckboxSelected = cart.some(item => item.selected === undefined || item.selected === true);
            const allCheckboxesSelected = cart.length > 0 && cart.every(item => item.selected === undefined || item.selected === true);

            if (removeBtn) {
                if (anyCheckboxSelected) {
                    removeBtn.classList.remove('d-none');
                    if (allCheckboxesSelected) {
                        removeBtn.textContent = 'Remove All';
                    } else {
                        removeBtn.textContent = 'Remove Selected';
                    }
                } else {
                    removeBtn.classList.add('d-none');
                    removeBtn.textContent = 'Remove';
                }
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', (event) => {
                const isChecked = event.target.checked;
                cart.forEach(item => {
                    item.selected = isChecked;
                });
                renderCartItems();
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                const itemsToRemove = cart.filter(item => item.selected === undefined || item.selected === true);
                if (itemsToRemove.length === 0) {
                    alert("Tidak ada item yang dipilih untuk dihapus.");
                    return;
                }

                fetch('/cart/remove-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        item_ids: itemsToRemove.map(item => item.id)
                    }),
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Failed to remove items from server.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Items removed from database:", data);
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error("Error removing items from database:", error);
                        alert("Gagal menghapus item dari keranjang: " + error.message);
                    });
            });
        }

        renderCartItems();
    }
});
