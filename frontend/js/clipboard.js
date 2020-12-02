function copyToClipboard(textoACopiar) {

    var aux = document.createElement("input");

    aux.setAttribute("value", textoACopiar);

    document.body.appendChild(aux);

    aux.select();

    document.execCommand("copy");

    document.body.removeChild(aux);

}

$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
});