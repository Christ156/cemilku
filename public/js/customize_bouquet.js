var current_layer = 1;

const url = "http://127.0.0.1:8000/";

var priceLayer1 = 0;
var priceLayer2 = 0;
var priceLayer3 = 0;
var priceLayer4 = 0;
var tempTotalPrice = 0;

function setCurrentLayer(currentLayer){
    current_layer = currentLayer;

    for(var i = 1; i <= 4; i++){
        var snack_layer = "snack-layer-"+i;
        document.getElementById(snack_layer).style.display = "none";
    }

    document.getElementById("snack-layer-"+currentLayer).style.display = "block";
}

function changePreview(imageName, itemPrice, idItem, stateLayer){
    var base_preview = document.getElementById("preview-bouquet-base");
    var layer1_preview = document.getElementById("preview-bouquet-1");
    var layer2_preview = document.getElementById("preview-bouquet-2");
    var layer3_preview = document.getElementById("preview-bouquet-3");
    var layer4_preview = document.getElementById("preview-bouquet-4");


    if(stateLayer == 0){
        base_preview.src = url+"assets/bouquet_base/"+imageName+".png";
        controlProgress('next');
    }else if(stateLayer == 1){
        layer1_preview.src = url+"assets/bouquet_layer_1/"+imageName;
        priceLayer1 = itemPrice;
        document.getElementById("snack-1").value = idItem;
    }else if(stateLayer == 2){
        layer2_preview.src = url+"assets/bouquet_layer_2&3/"+imageName;
        priceLayer2 = itemPrice;
        document.getElementById("snack-2").value = idItem;
    }else if(stateLayer == 3){
        layer3_preview.src = url+"assets/bouquet_layer_2&3/"+imageName;
        priceLayer3 = itemPrice;
        document.getElementById("snack-3").value = idItem;
    }else{
        layer4_preview.src = url+"assets/bouquet_layer_4/"+imageName;
        priceLayer4 = itemPrice;
        document.getElementById("snack-4").value = idItem;
    }

    tempTotalPrice = priceLayer1 + priceLayer2 + priceLayer3 + priceLayer4;

    document.getElementById("temp_price1").textContent = tempTotalPrice.toLocaleString("id-ID");
    document.getElementById("temp_price2").textContent = tempTotalPrice.toLocaleString("id-ID");
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
    const bouquet_layer_1 = document.getElementById("bouquet-layer-1");
    const bouquet_layer_2 = document.getElementById("bouquet-layer-2");
    const bouquet_layer_3 = document.getElementById("bouquet-layer-3");
    const bouquet_layer_4 = document.getElementById("bouquet-layer-4");

    if(layer == 2){
        bouquet_layer_1.style.display = 'block';
        bouquet_layer_2.style.display = 'block';
        bouquet_layer_3.style.display = "none";
        bouquet_layer_4.style.display = "none";
    }else if(layer == 3){
        bouquet_layer_1.style.display = "block";
        bouquet_layer_2.style.display = "block";
        bouquet_layer_3.style.display = "block";
        bouquet_layer_4.style.display = "none";
    }else{
        bouquet_layer_1.style.display = "block";
        bouquet_layer_2.style.display = "block";
        bouquet_layer_3.style.display = "block";
        bouquet_layer_4.style.display = "block";
    }

    controlProgress('next');
    document.getElementById("layer-selected").value = layer;
}
