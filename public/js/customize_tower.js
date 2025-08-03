var current_layer = 1;

const url = "http://127.0.0.1:8000/";

var layer_selected = 0;
var priceLayer1 = 0;
var priceLayer2 = 0;
var priceLayer3 = 0;
var priceLayer4 = 0;
var priceDecor = 0;
var tempTotalPrice = 0;

function setCurrentLayer(currentLayer){
    current_layer = currentLayer;

    for(var i = 1; i <= 4; i++){
        var snack_layer = "snack-layer-"+i;
        document.getElementById(snack_layer).style.display = "none";
    }

    document.getElementById("snack-layer-"+currentLayer).style.display = "block";
}

function changePreview(imageName, itemPrice, idItem){
    if(current_layer == 1){
        document.getElementById("preview-tower-1").src = url+"assets/tower_layer_1/"+imageName;
        priceLayer1 = itemPrice;
        document.getElementById("snack-1").value = idItem;
        changeLayerSetState(current_layer, layer_selected);
    }else if(current_layer == 2){
        document.getElementById("preview-tower-2").src = url+"assets/tower_layer_2/"+imageName;
        priceLayer2 = itemPrice;
        document.getElementById("snack-2").value = idItem;
        changeLayerSetState(current_layer, layer_selected);
    }else if(current_layer == 3){
        document.getElementById("preview-tower-3").src = url+"assets/tower_layer_3/"+imageName;
        priceLayer3 = itemPrice;
        document.getElementById("snack-3").value = idItem;
        changeLayerSetState(current_layer, layer_selected);
    }else{
        document.getElementById("preview-tower-4").src = url+"assets/tower_layer_4/"+imageName;
        priceLayer4 = itemPrice;
        document.getElementById("snack-4").value = idItem;
        changeLayerSetState(current_layer, layer_selected);
    }

    tempTotalPrice = priceLayer1 + priceLayer2 + priceLayer3 + priceLayer4 + priceDecor;

    document.getElementById("temp_price1").textContent = tempTotalPrice.toLocaleString("id-ID");
    document.getElementById("temp_price2").textContent = tempTotalPrice.toLocaleString("id-ID");
    document.getElementById("temp_price3").textContent = tempTotalPrice.toLocaleString("id-ID");
}

var currentProgress = 0;

function controlProgress(state){
    customize_menu = document.getElementById("customize-menu");

    if(state == 'prev'){
        if(currentProgress != 0){
            currentProgress = currentProgress + 100;
            customize_menu.style.marginLeft = currentProgress+"%";
        }
    }else if(state == 'next'){
        if(currentProgress != -300){
            currentProgress = currentProgress - 100;
            customize_menu.style.marginLeft = currentProgress+"%";
        }
    }

    node_1 = document.getElementById("progress-node-1");
    node_2 = document.getElementById("progress-node-2");
    node_3 = document.getElementById("progress-node-3");

    if(currentProgress == 0){
        node_1.className = "progress-node";
        node_2.className = "progress-node";
        node_3.className = "progress-node";
    }else if(currentProgress == -100){
        node_1.className = node_1.className+" progress-node-done";
        node_2.className = "progress-node";
        node_3.className = "progress-node";
    }else if(currentProgress == -200){
        node_2.className = node_2.className+" progress-node-done";
        node_3.className = "progress-node";
    }else if(currentProgress == -300){
        node_3.className = node_3.className+" progress-node-done";
        document.getElementById("customize-price").value = tempTotalPrice;
    }
}

function previewLayer(layer){
    const tower_layer_1 = document.getElementById("tower-layer-1");
    const tower_layer_2 = document.getElementById("tower-layer-2");
    const tower_layer_3 = document.getElementById("tower-layer-3");
    const tower_layer_4 = document.getElementById("tower-layer-4");

    if(layer == 2){
        layer_selected = 2;
        tower_layer_1.style.display = 'block';
        tower_layer_2.style.display = 'block';
        tower_layer_3.style.display = "none";
        tower_layer_4.style.display = "none";
    }else if(layer == 3){
        layer_selected = 3;
        tower_layer_1.style.display = "block";
        tower_layer_2.style.display = "block";
        tower_layer_3.style.display = "block";
        tower_layer_4.style.display = "none";
    }else{
        layer_selected = 4;
        tower_layer_1.style.display = "block";
        tower_layer_2.style.display = "block";
        tower_layer_3.style.display = "block";
        tower_layer_4.style.display = "block";
    }

    document.getElementById("layer-selected").value = layer;
    document.getElementById("warning-tower").innerText = "Note: Please fill all layer with snack before continue!"
    controlProgress('next');
}

function previewDecoration(decorName, itemPrice, idItem){

    decor = document.getElementById("preview-tower-decor");
    decorPreview = document.getElementById("tower-decor");

    decorPreview.style.display = "block";
    decor.src = url+"assets/decoration/"+decorName;

    priceDecor = itemPrice;
    tempTotalPrice = priceLayer1 + priceLayer2 + priceLayer3 + priceLayer4 + priceDecor;

    if(idItem != 0){
        document.getElementById("decoration").value = idItem;
    }else{
        document.getElementById("decoration").value = "";
    }

    document.getElementById("temp_price1").textContent = tempTotalPrice.toLocaleString("id-ID");
    document.getElementById("temp_price2").textContent = tempTotalPrice.toLocaleString("id-ID");
    document.getElementById("temp_price3").textContent = tempTotalPrice.toLocaleString("id-ID");
}

var layer_set_status = [false, false, false, false];

function changeLayerSetState(layer, layer_selected){
    if(layer == 1){
        layer_set_status[0] = true;
    }else if(layer == 2){
        layer_set_status[1] = true;
    }else if(layer == 3){
        layer_set_status[2] = true;
    }else{
        layer_set_status[3] = true;
    }

    all_layer_set = true;

    for(var i=0; i < layer_selected; i++){
        if(layer_set_status[i] == false){
            all_layer_set = false;
            break;
        }
    }

    if(all_layer_set == true){
        document.getElementById("next-progress-1").disabled = false;
        document.getElementById("warning-tower").innerText = "";
    }else{
        document.getElementById("next-progress-1").disabled = true;
        document.getElementById("warning-tower").innerText = "Note: Please fill all layer with snack before continue!"
    }
}

function checkCustomizeName(){
    if(document.getElementById("customize-name").value === ""){
        document.getElementById("warning-tower-name").innerText = "*Please fill the tower name!";
    }else{
        document.getElementById("warning-tower-name").innerText = "";
        document.getElementById("finish-customize").setAttribute("data-bs-toggle", "modal");
        document.getElementById("finish-customize").setAttribute("data-bs-target", "#confirmationBouquet");
    }
}
