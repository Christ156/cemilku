var total_price_cart = 0;
var total_item_check = 0;
var total_price_with_ship = 0;

var allCheck = 0;

function allCheckboxCheck(count, carts){
    var cart_items = document.getElementsByClassName("product-checkbox");
    var checkbox_all = document.getElementById("select_all");

    if(allCheck == 0 && total_item_check == 0 && checkbox_all.checked == true){
        total_price_cart = 0;
        for(var i = 0; i < count; i++){
            cart_items[i].checked = true;
            total_price_cart = total_price_cart + parseInt(carts[i]['total_price']);
            document.getElementById("item_cart_"+carts[i]['id']).value = "true";
        }
        allCheck = 1;
        total_item_check = count;
    }else if(allCheck == 1 && total_item_check == count && checkbox_all.checked == false){
        for(var i = 0; i < count; i++){
            cart_items[i].checked = false;
            total_price_cart = total_price_cart - parseInt(carts[i]['total_price']);
            document.getElementById("item_cart_"+carts[i]['id']).value = "false";
        }

        allCheck = 0;
        total_item_check = 0;
    }else if(total_item_check != count){
        for(var i = 0; i < count; i++){
            if(cart_items[i].checked == false){
                cart_items[i].checked = true;
                total_item_check = total_item_check + 1;
                total_price_cart = total_price_cart + parseInt(carts[i]['total_price']);
                document.getElementById("item_cart_"+carts[i]['id']).value = "true";
            }
        }

        allCheck = 1;
    }

    removeSelected(total_item_check);
    total_price_with_ship = total_price_cart + 0;

    document.getElementById("total_price_cart").innerText = total_price_cart.toLocaleString("id-ID");
    document.getElementById("total_price_with_ship").innerText = total_price_with_ship.toLocaleString("id-ID");
    document.getElementById("product-count").innerText = total_item_check;
    document.getElementById("total_price").value = total_price_cart;
}

function previewPrice(price, cart_item_id){
    var checkbox = document.getElementById("checkbox_item"+cart_item_id);

    if(checkbox.checked == false){
        total_price_cart = total_price_cart - price;
        total_item_check = total_item_check - 1;
        document.getElementById("item_cart_"+cart_item_id).value = "false";
    }else{
        total_price_cart = total_price_cart + price;
        total_item_check = total_item_check + 1;
        document.getElementById("item_cart_"+cart_item_id).value = "true";
    }

    total_price_with_ship = total_price_cart + 0;

    document.getElementById("product-count").innerText = total_item_check;
    document.getElementById("total_price_cart").innerText = total_price_cart.toLocaleString("id-ID");
    document.getElementById("total_price_with_ship").innerText = total_price_with_ship.toLocaleString("id-ID");
    document.getElementById("total_price").value = total_price_cart;
}

function allCheckTrue(count){
    var checkedTrue = 0;
    var checkedFalse = 0;
    var cart_items = document.getElementsByClassName("product-checkbox");

    for(var i = 0; i < count; i++){
        if(cart_items[i].checked == true){
            checkedTrue = checkedTrue + 1;
        }else{
            checkedFalse = checkedFalse + 1;
        }
    }

    if(checkedTrue == count){
        document.getElementById("select_all").checked = true;
        allCheck = 1;
    }else{
         document.getElementById("select_all").checked = false;
         allCheck = 0;
    }

    removeSelected(total_item_check);
}

function removeSelected(count_checked){
    if(count_checked > 0){
        document.getElementById("remove-btn").style.display = "Block";
    }else{
        document.getElementById("remove-btn").style.display = "None";
    }
}

function checkItemSelected(count, carts, address){
    var selectedItem = 0;
    var selectedPayment = 0;
    var selectedAddress = 0;

    for(var i = 0; i < count; i++){
        if(document.getElementById("item_cart_"+carts[i]["id"]).value == "true"){
            selectedItem = 1;
            break;
        }
    }

    for(var i = 1; i <= 4;i++){
        if(document.getElementById("payment_method_"+i).checked){
            selectedPayment = 1;
            break;
        }
    }

    if(address == 1){
        selectedAddress = 1;
    }

    if(selectedItem == 1 && selectedPayment == 1 && selectedAddress == 1){
        document.getElementById("checkout_btn").disabled = false;
    }else{
        document.getElementById("checkout_btn").disabled = true;
    }
}

function updateQuantity(state, id){
    const form_update = document.getElementById('set-quantity-cart');
    const cart_item_id = document.getElementById('cart_item_id');
    const quantity_item = document.getElementById('quantity_item');
    const quantity_cart = document.getElementById('quantity_cart_'+id);
    var quantity = parseInt(quantity_cart.value);

    if(quantity >= 1){
        if(state == 'add'){
            quantity = quantity + 1;
        }else{
            quantity = quantity - 1;
        }

        if(quantity != 0){
            cart_item_id.value = id;
            quantity_item.value = quantity;
            quantity_cart.value = quantity;
            form_update.submit();
        }else{
            cart_item_id.value = id;
            quantity_item.value = 1;
            quantity_cart.value = 1;
            form_update.submit();
        }
    }else{
        cart_item_id.value = id;
        quantity_item.value = 1;
        quantity_cart.value = 1;
        form_update.submit();
    }
}

function updateQuantityByField(id){
    const form_update = document.getElementById('set-quantity-cart');
    const quantity_cart = document.getElementById('quantity_cart_'+id);
    var quantity = parseInt(quantity_cart.value);
    const cart_item_id = document.getElementById('cart_item_id');
    const quantity_item = document.getElementById('quantity_item');

    if(quantity >= 1){
        cart_item_id.value = id;
        quantity_item.value = quantity;
        form_update.submit();
    }else{
        cart_item_id.value = id;
        quantity_item.value = 1;
        form_update.submit();
    }
}
