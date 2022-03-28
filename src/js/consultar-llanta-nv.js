function busqueda() {
  Swal.fire({
    icon: "info",
    width: "1250px",
    html: `<div class="container">
                   <div class="row">
                        <div class="col-12 col-md-12">
                            <b>Consulta la disponibilidad de una llanta en otras sucursales</b>
                        </div>
                   </div>

                   <div class="row m-3">
                        <div class="col-12 col-md-3">
                            <label for="ancho">Ancho</label>
                            <select class="form-control" id="ancho" onclick="appendOption('ancho');">
                                <option value="No aplica">Selecciona un ancho</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="proporcion">Perfil</label>
                            <select class="form-control" id="proporcion" onclick="appendOption('proporcion');">
                                <option value="No aplica">Selecciona un perfil</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="diametro">Diametro</label>
                            <select class="form-control" id="diametro" onclick="appendOption('diametro');">
                                <option value="No aplica">Selecciona un rin</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3" style="display:flex; align-items:end;">
                            <div class="btn btn-info" onclick="buscarNeumatico();"><i class="fas fa-search"></i></div>
                       </div>
                   </div>

                   <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action active">
                                    <div class="row">
                                        <div class="col-12 col-md-1">#</div>
                                        <div class="col-12 col-md-3">Descripcion</div>
                                        <div class="col-12 col-md-1">Stock</div>
                                        <div class="col-12 col-md-1">Marca</div>
                                        <div class="col-12 col-md-2">Precio</div>
                                        <div class="col-12 col-md-2">Mayoreo</div>
                                        <div class="col-12 col-md-2">Sucursal</div>
                                    </div>
                                </a>
                                <div id="resultados_encontrados">
                                     <a href="#" class="list-group-item list-group-item-action">
                                        <div class="row">
                                             <div class="col-12 col-md-12">Sin datos</div>
                                        </div>     
                                     </a>   
                                </div>
                           </div>
                        </div>
                   </div>
               </div>`,
    didOpen: function () {
      /* appendOption(); */
    },
  }).then(function (response) {
        if(response.isConfirmed){

          
        }
  });
}

function appendOption(parametro) {

    let $select = $("#"+parametro);
    $.ajax({
        type: "POST",
        url: `./modelo/ventas/traer-datos-filtro-${parametro}.php`,
        data: "sucursal_id",
        dataType: "JSON",
        success: function (response) {
          
            response.sort(function(a, b) {return a - b});
            let selectedValue = $("#"+parametro).val();
            
            let html = response.filter((e, i, a) => a.indexOf(e) === i).map(item => `<option value="${item}">${item}</option>`); 
            
            $select.html(html).val(selectedValue);
    
    
        }
    });
}
/* 
function appendOptions(array,selector){
  
    array.forEach(element => {
        switch(selector) {
            case "ancho":
                valor = element;
            break; 
            case "proporcion":
                valor = element;
            break; 
            case "diametro":
                valor = element;
            break; 
            default:
                valor = "pero";
                break;
        }  
        $("#"+selector).append(`<option value="${valor}">${valor}</option>`);   
    });
}
 */

function buscarNeumatico(){
    if($("#ancho").val() == "No aplica"){

        toastr.error('Selecciona un ancho a buscar.', 'Elige un ancho'); 
    }else if($("#proporcion").val() == "No aplica"){

        toastr.error('Selecciona un alto o perfil a buscar.', 'Elige un alto'); 
    }else if($("#diametro").val() == "No aplica"){

    
        toastr.error('Selecciona una medida de rin.', 'Elige un rin'); 
    }else{
        let ancho = $("#ancho").val();
        let proporcion = $("#proporcion").val();
        let diametro = $("#diametro").val();

        $.ajax({
            type: "POST",
            url: "./modelo/ventas/buscar-neumatico-por-medida.php",
            data: {"ancho": ancho, "proporcion":proporcion, "diametro": diametro},
            dataType: "JSON",
            success: function (response) {
                $("#resultados_encontrados").empty();

                if(response.estatus == false){
                    $("#resultados_encontrados").append(`
                        <a href="#" class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-12 col-md-12">${response.mensj}</div>
                        </div>     
                     </a> 
                        `);
                }else{
                    response.forEach(element => {
                        contador++;
                        $("#resultados_encontrados").append(`
                        <a href="#" class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-12 col-md-1">${contador}</div>
                            <div class="col-12 col-md-3">${element.descripcion}</div>
                            <div class="col-12 col-md-1">${element.stock}</div>
                            <div class="col-12 col-md-1">${element.marca}</div>
                            <div class="col-12 col-md-2">${element.precio}</div>
                            <div class="col-12 col-md-2">${element.precio_mayoreo}</div>
                            <div class="col-12 col-md-2">${element.sucursal}</div>
                        </div>     
                     </a> 
                        `);
                    });
                }
              
            }
        });
         
    }; 
};
