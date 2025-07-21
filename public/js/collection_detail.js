// Tambahkan flag global untuk memastikan script hanya diinisialisasi sekali
if (window.collectionDetailJsInitialized) {
    console.log("DEBUG: collection_detail.js already initialized. Skipping re-initialization.");
} else {
    window.collectionDetailJsInitialized = true;
    console.log("DEBUG: collection_detail.js loaded and initializing for the first time.");

    document.addEventListener("DOMContentLoaded", function () {
        console.log("DEBUG: DOMContentLoaded fired for collection_detail.js.");

        const minusBtn = document.getElementById("minus");
        const plusBtn = document.getElementById("plus");
        const valueInput = document.getElementById("value");
        const stockInput = document.getElementById("stock");
        const stock = stockInput ? parseInt(stockInput.value) : 9999;

        const alertBox = document.getElementById("alertBox");
        const alertMessage = document.getElementById("alertMessage");
        const toast = document.getElementById("toastAlert");
        const toastMessage = document.getElementById("toastMessage");

        let alertTimeout;

        const addToCartDetailBtn = document.getElementById("add-to-cart-detail-btn");

        console.log("DEBUG: AddToCart button found:", !!addToCartDetailBtn);
        console.log("DEBUG: Value input found:", !!valueInput);
        console.log("DEBUG: Stock input found:", !!stockInput, "Stock value:", stock);

        // BAGIAN INI TELAH DIUBAH: Menggunakan fetch API untuk mengirim data ke server
        if (addToCartDetailBtn) {
            addToCartDetailBtn.addEventListener("click", function (e) {
                e.preventDefault();
                console.log("DEBUG: AddToCart button received a click event!");

                let currentValue = parseInt(valueInput.value);

                if (isNaN(currentValue) || currentValue < 1) {
                    showAlert("Minimum quantity is 1!");
                    valueInput.value = 1;
                    return;
                }

                if (currentValue > stock) {
                    showAlert("Oops! Maximum stock limit reached.");
                    valueInput.value = stock;
                    return;
                }

                const itemId = document.getElementById('item-id').value;

                // URL API untuk menambah item ke keranjang
                // PERUBAHAN PENTING DI SINI: UBAH DARI '/api/cart/add' MENJADI '/cart/add'
                const apiUrl = '/cart/add'; // SESUAIKAN DENGAN ROUTE WEB LARAVEL ANDA

                // Lakukan panggilan ke server dengan fetch API
                fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        collection_id: itemId,
                        quantity: currentValue
                    })
                })
                    .then(response => {
                        // Cek apakah respons adalah HTML (misalnya dari halaman error)
                        const contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            // Jika bukan JSON, berarti ada error di server (misal 404, 500)
                            // Ini akan menangkap error jika server mengembalikan HTML (seperti halaman 404)
                            return response.text().then(text => {
                                throw new Error(`Server returned non-JSON response (status: ${response.status}): ${text.substring(0, 100)}...`);
                            });
                        }
                    })
                    .then(data => {
                        // JIKA RESPON DARI SERVER SUKSES, BARU TAMPILKAN MODAL
                        console.log("DEBUG: Item successfully added via API:", data);
                        const doneModalElement = document.getElementById('doneModal');
                        if (doneModalElement) {
                            var doneModal = new bootstrap.Modal(doneModalElement);
                            doneModal.show();
                        } else {
                            console.error("DEBUG: Modal 'doneModal' not found.");
                        }

                        // Tambahkan logika untuk memperbarui tampilan keranjang jika ada (dari cart.js)
                        if (window.updateCartDisplay) {
                            window.updateCartDisplay();
                        }
                    })
                    .catch(error => {
                        // TANGANI ERROR JIKA ADA MASALAH DENGAN PANGGILAN API ATAU SERVER
                        console.error('Error:', error);
                        alert("Terjadi kesalahan: " + error.message);
                    });
            });
        } else {
            console.error("ERROR: AddToCart button with ID 'add-to-cart-detail-btn' not found!");
        }

        // --- Fungsi-fungsi lain untuk Quantity Counter dan Alert tetap sama seperti sebelumnya ---

        function isMobileView() {
            return window.matchMedia("(max-width: 430px)").matches;
        }

        function showToast(message) {
            toastMessage.textContent = message;
            toast.classList.add("show");

            clearTimeout(alertTimeout);
            alertTimeout = setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        function showAlert(message) {
            if (isMobileView()) {
                showToast(message);
            } else {
                alertMessage.textContent = message;
                alertBox.classList.add("active");

                clearTimeout(alertTimeout);
                alertTimeout = setTimeout(() => {
                    alertBox.classList.remove("active");
                    alertBox.style.display = 'none';
                }, 5000);
            }
        }

        if (valueInput && stockInput) {
            let currentQuantity = parseInt(valueInput.value);

            function updateQuantityDisplay() {
                valueInput.value = currentQuantity;
                if (currentQuantity > stock) {
                    showAlert(`Oops! Stok hanya tersedia ${stock}.`);
                    valueInput.value = stock;
                    currentQuantity = stock;
                } else if (currentQuantity < 1) {
                    showAlert("Kuantitas minimal adalah 1!");
                    valueInput.value = 1;
                    currentQuantity = 1;
                } else {
                    alertBox.classList.remove("active");
                    alertBox.style.display = 'none';
                }
            }

            if (minusBtn) {
                minusBtn.onclick = function () {
                    if (currentQuantity > 1) {
                        currentQuantity--;
                        updateQuantityDisplay();
                    } else {
                        showAlert("Kuantitas minimal adalah 1!");
                    }
                };
            }

            if (plusBtn) {
                plusBtn.onclick = function () {
                    if (currentQuantity < stock) {
                        currentQuantity++;
                        updateQuantityDisplay();
                    } else {
                        showAlert(`Oops! Stok hanya tersedia ${stock}.`);
                    }
                };
            }

            valueInput.addEventListener("input", function () {
                let value = parseInt(valueInput.value);
                if (isNaN(value) || value < 1) {
                    currentQuantity = 1;
                } else if (value > stock) {
                    currentQuantity = stock;
                } else {
                    currentQuantity = value;
                }
                updateQuantityDisplay();
            });

            valueInput.addEventListener("blur", function () {
                if (valueInput.value === "" || isNaN(parseInt(valueInput.value)) || parseInt(valueInput.value) < 1) {
                    valueInput.value = 1;
                    currentQuantity = 1;
                    updateQuantityDisplay();
                }
            });

            valueInput.addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    if (addToCartDetailBtn) {
                        addToCartDetailBtn.click();
                    }
                }
            });

            updateQuantityDisplay();
        } else {
            console.error("ERROR: Quantity counter elements (valueInput, stockInput) not found!");
        }
    });
}
