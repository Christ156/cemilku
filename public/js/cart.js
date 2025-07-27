var total_price_cart = 0;
var total_item_check = 0;

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

    document.getElementById("total_price_cart").innerText = total_price_cart.toLocaleString("id-ID");
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

    document.getElementById("product-count").innerText = total_item_check;
    document.getElementById("total_price_cart").innerText = total_price_cart.toLocaleString("id-ID");
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

function checkItemSelected(count, carts){
    var selected = 0;

    for(var i = 0; i < count; i++){
        if(document.getElementById("item_cart_"+carts[i]["id"]).value == "true"){
            selected = 1;
            break;
        }
    }

    if(selected == 0){
        document.getElementById("checkout_btn").disabled = true;
    }else{
        document.getElementById("checkout_btn").disabled = false;
    }
}
