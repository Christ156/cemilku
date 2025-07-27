var currentQuantity = 1;

function setQuantity(state, quantity){
    if(state == 'add'){
        currentQuantity = currentQuantity + 1;
    }else{
        currentQuantity = currentQuantity - 1;
    }

    document.getElementById("value_quantity").value = currentQuantity;

    checkQuantityValid(quantity);
}

function checkQuantityValid(max_quantity){
    if(document.getElementById("value_quantity").value < 1){
        currentQuantity = 1;
        showAlert("Quantity must be at least 1pcs!");
    }else if(document.getElementById("value_quantity").value > max_quantity){
        currentQuantity = max_quantity;
        showAlert("Quantity is a max!");
    }
    document.getElementById("value_quantity").value = currentQuantity;
}

function showAlert(message) {
            const alertContainer = document.getElementById("topAlertContainer");
            const alertMessage = document.getElementById("topAlertMessage");

            if (alertContainer && alertMessage) {
                alertMessage.textContent = message;
                alertContainer.classList.add("show");

                setTimeout(() => {
                    alertContainer.classList.remove("show");
                }, 3000);
            }
        }

// if (window.collectionDetailJsInitialized) {
//     console.log(
//         "DEBUG: collection_detail.js already initialized. Skipping re-initialization."
//     );
// } else {
//     window.collectionDetailJsInitialized = true;
//     console.log(
//         "DEBUG: collection_detail.js loaded and initializing for the first time."
//     );

//     document.addEventListener("DOMContentLoaded", function () {
//         console.log("DEBUG: DOMContentLoaded fired for collection_detail.js.");

//         const minusBtn = document.getElementById("minus");
//         const plusBtn = document.getElementById("plus");
//         const valueInput = document.getElementById("value");
//         const stockInput = document.getElementById("stock");
//         const stock = stockInput ? parseInt(stockInput.value) : 9999;

//         const topAlertContainer = document.getElementById("topAlertContainer");
//         const topAlertMessage = document.getElementById("topAlertMessage");

//         const toast = document.getElementById("toastAlert");
//         const toastMessage = document.getElementById("toastMessage");

//         let alertTimeout;

//         const addToCartDetailBtn = document.getElementById("add-to-cart-detail-btn");

//         const doneModalElement = document.getElementById('doneModal');

//         console.log("DEBUG: AddToCart button found:", !!addToCartDetailBtn);
//         console.log("DEBUG: Value input found:", !!valueInput);
//         console.log("DEBUG: Stock input found:", !!stockInput, "Stock value:", stock);
//         console.log("DEBUG: TopAlertContainer element found:", !!topAlertContainer);
//         console.log("DEBUG: TopAlertMessage element found:", !!topAlertMessage);
//         console.log("DEBUG: Toast element found:", !!toast);
//         console.log("DEBUG: DoneModal element found:", !!doneModalElement);

//         if (addToCartDetailBtn) {
//             addToCartDetailBtn.addEventListener("click", function (e) {
//                 e.preventDefault();
//                 console.log("DEBUG: AddToCart button received a click event!");

//                 let currentValue = parseInt(valueInput.value);

//                 if (isNaN(currentValue) || currentValue < 1) {
//                     showAlert("Minimum quantity is 1!");
//                     valueInput.value = 1;
//                     return;
//                 }

//                 if (currentValue > stock) {
//                     showAlert(`Oops! Only ${stock} items left in stock.`);
//                     valueInput.value = stock;
//                     return;
//                 }

//                 const itemId = document.getElementById('item-id').value;

//                 const apiUrl = '/cart/add';
//                 const itemPrice = parseInt(document.querySelector('input[name="price"]').value);
//                 const itemNameElement = document.querySelector(".title");
//                 const itemName = itemNameElement ? itemNameElement.textContent : "Unknown Item";
//                 const itemImageElement = document.querySelector(".collections_img img");
//                 const itemImage = itemImageElement ? itemImageElement.getAttribute("src") : "";
//                 const itemDescriptionElement = document.querySelector(".description p");
//                 const itemDescription = itemDescriptionElement ? itemDescriptionElement.textContent : "";

//                 const itemToAdd = {
//                     id: itemId,
//                     name: itemName,
//                     price: itemPrice,
//                     image: itemImage,
//                     description: itemDescription,
//                     quantity: currentValue,
//                 };

//                 fetch(apiUrl, {
//                     method: 'POST',
//                     headers: {
//                         'Content-Type': 'application/json',
//                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                     },
//                     body: JSON.stringify({
//                         collection_id: itemId,
//                         quantity: currentValue
//                     })
//                 })
//                     .then(response => {
//                         const contentType = response.headers.get("content-type");
//                         if (contentType && contentType.indexOf("application/json") !== -1) {
//                             return response.json();
//                         } else {
//                             return response.text().then(text => {
//                                 throw new Error(`Server returned non-JSON response (status: ${response.status}): ${text.substring(0, 100)}...`);
//                             });
//                         }
//                     })
//                     .then(data => {
//                         console.log("DEBUG: Item successfully added via API:", data);
//                         if (doneModalElement) {
//                             var doneModal = new bootstrap.Modal(doneModalElement);
//                             doneModal.show();
//                         } else {
//                             console.error("DEBUG: Modal 'doneModal' not found.");
//                         }

//                         if (window.updateCartDisplay) {
//                             window.updateCartDisplay();
//                         }
//                     })
//                     .catch(error => {
//                         console.error('Error:', error);
//                         alert("Terjadi kesalahan: " + error.message);
//                     });
//             });
//         } else {
//             console.error("ERROR: AddToCart button with ID 'add-to-cart-detail-btn' not found!");
//         }

//         function isMobileView() {
//             return window.matchMedia("(max-width: 430px)").matches;
//         }

//         function showToast(message) {
//             if (toast && toastMessage) {
//                 toastMessage.textContent = message;
//                 toast.classList.add("show");

//                 clearTimeout(alertTimeout);
//                 alertTimeout = setTimeout(() => {
//                     toast.classList.remove("show");
//                 }, 3000);
//             } else {
//                 console.error("DEBUG: Toast elements (toast or toastMessage) not found.");
//             }
//         }

//         function showAlert(message) {
//             const alertContainer = document.getElementById("topAlertContainer");
//             const alertMessage = document.getElementById("topAlertMessage");

//             if (alertContainer && alertMessage) {
//                 alertMessage.textContent = message;
//                 alertContainer.classList.add("show");

//                 setTimeout(() => {
//                     alertContainer.classList.remove("show");
//                 }, 3000);
//             }
//         }

//         if (valueInput && stockInput) {
//             let currentQuantity = parseInt(valueInput.value);

//             function updateQuantityDisplay() {
//                 let inputVal = parseInt(valueInput.value);

//                 if (isNaN(inputVal) || inputVal < 1) {
//                     currentQuantity = 1;
//                     showAlert("Minimum quantity is 1!");
//                 } else if (inputVal > stock) {
//                     currentQuantity = stock;
//                     showAlert(`Oops! Only ${stock} items left in stock.`);
//                 } else {
//                     currentQuantity = inputVal;
//                 }

//                 valueInput.value = currentQuantity;
//             }

//             if (minusBtn) {
//                 minusBtn.onclick = function () {
//                     if (currentQuantity > 1) {
//                         currentQuantity--;
//                         updateQuantityDisplay();
//                     } else {
//                         showAlert("Minimum quantity is 1!");
//                     }
//                 };
//             }

//             if (plusBtn) {
//                 plusBtn.onclick = function () {
//                     if (currentQuantity < stock) {
//                         currentQuantity++;
//                         updateQuantityDisplay();
//                     } else {
//                         showAlert(`Oops! Only ${stock} items left in stock.`);
//                     }
//                 };
//             }

//             valueInput.addEventListener("input", function () {
//                 updateQuantityDisplay();
//             });

//             valueInput.addEventListener("blur", function () {
//                 if (
//                     valueInput.value === "" ||
//                     isNaN(parseInt(valueInput.value)) ||
//                     parseInt(valueInput.value) < 1
//                 ) {
//                     currentQuantity = 1;
//                     valueInput.value = 1;
//                     showAlert("Minimum quantity is 1!");
//                 } else {
//                     updateQuantityDisplay();
//                 }
//             });

//             valueInput.addEventListener("keydown", function (e) {
//                 if (e.key === "Enter") {
//                     e.preventDefault();
//                     if (addToCartDetailBtn) {
//                         addToCartDetailBtn.click();
//                     }
//                 }
//             });

//             updateQuantityDisplay();
//         } else {
//             console.error("ERROR: Quantity counter elements (valueInput, stockInput) not found!");
//         }

//         if (doneModalElement) {
//             doneModalElement.addEventListener('hidden.bs.modal', function (event) {
//                 console.log("DEBUG: 'doneModal' has been hidden. Redirecting to /cart.");
//                 window.location.href = '/cart';
//             });
//         } else {
//             console.error("DEBUG: 'doneModal' element not found for adding hidden event listener.");
//         }
//     });

//     window.showTopAlert = function (message, duration = 3000) {
//         const alertContainer = document.getElementById("topAlertContainer");
//         const alertMessage = document.getElementById("topAlertMessage");

//         alertMessage.textContent = message;
//         alertContainer.classList.add("show");

//         setTimeout(() => {
//             alertContainer.classList.remove("show");
//         }, duration);
//     };
// }
