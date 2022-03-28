toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }

function buscarLlanta(){
if($("#ancho").val() == "No aplica"){

    toastr.error('Selecciona un ancho a buscar.', 'Elige un ancho'); 
}else if($("#proporcion").val() == "No aplica"){

    toastr.error('Selecciona un alto o perfil a buscar.', 'Elige un alto'); 
}else if($("#diametro").val() == "No aplica"){

    toastr.error('Selecciona un diametro del rin a buscar.', 'Elige un diametro'); 
}else{

    $('#inventario-pedro').DataTable().ajax.reload();
}; 

//tabla.draw();

}


$(document).ready(function () {
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) { //'data' contiene los datos de la fila
            //En la columna 1 estamos mostrando el tipo de usuario
            let AnchoColumnData = data[2] || 0;
            let ProporcionColumnData = data[3]|| 0;
            let DiametroColumnData = data[4]|| 0;
            let idSucursalRowData = data[8];
            if (!filterByAncho(AnchoColumnData, ProporcionColumnData, DiametroColumnData, idSucursalRowData)) {
                return false;
            }
            return true;
        }
    );
});
function filterByAncho(AnchoColumnData, ProporcionColumnData, DiametroColumnData, idSucursalRowData) {
    let idSucursal = getParameterByName("id");
    let AnchoSelected = $('#ancho').val();
    let ProporcionSelected = $('#proporcion').val();
    let DiametroSelected = $('#diametro').val();
    //Si la opción seleccionada es 'TODOS', devolvemos 'true' para que pinte la fila
    if (idSucursal === idSucursalRowData) {
        return true;
    }
    //La fila sólo se va a pintar si el valor de la columna coincide con el del filtro seleccionado
    return AnchoColumnData === AnchoSelected && ProporcionColumnData === ProporcionSelected
    && DiametroColumnData === DiametroSelected || AnchoSelected == "No aplica";
}

idSucursal = getParameterByName("id");
function getMedidas(parametro){
    let $select = $("#"+parametro);

    $.ajax({
        type: "POST",
        url: `./modelo/inventarios/traer-datos-filtro-${parametro}.php`,
        data: {"sucursal_id": idSucursal},
        dataType: "JSON", 
        success: function (response) {
            response.sort(function(a, b) {return a - b});
            let selectedValue = $("#"+parametro).val();
            
            let html = response.filter((e, i, a) => a.indexOf(e) === i).map(item => `<option value="${item}">${item}</option>`); 
            
            $select.html(html).val(selectedValue);
        }
    }); 
    //$("#"+ parametro).append(`<option value="No aplica">Selecciona un ${parametro}</option>`);
   }

function informacion() { 
    Swal.fire({
        icon: "info",
        title: "Medidas de llanta",
        html: `<div class='container'>
                  <div class='row justify-content-center'>
                    <div class='col-12'>
                        <img style='width:400px;' src='https://la-motorbit-media.s3.amazonaws.com/2017/10/como-saber-la-medida-de-la-llanta-1.jpg'>
                    </div>
                  </div>
               </div>`
    })
 }

 function reload() {
     window.location.reload();  
 }