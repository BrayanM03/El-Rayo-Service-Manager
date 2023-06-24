function ejecutarPanelReporteVentas(){

    Swal.fire({
        icon: 'info',
        didOpen: function () {
            $.ajax({
                type: "POST",
                url: "./modelo/panel/traer_sucursales.php",
                data: "data",
                dataType: "JSON",
                success: function (response) {
                    
                    /* $("#sucursal").append(`
                        <option value="all">Todas las plazas</option>
                    `);  */
                    response.forEach(element => {
                       
                    $("#sucursal").append(`
                        <option value="${element.id}">${element.nombre}</option>
                    `); 
                    });
                    
                }
            });

        },
        html: `
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-12">

                        <label for="sucursal">Selecciona una sucursal</label>
                        <select class="form-control" id="sucursal">
                           
                        </select>

                        <label class="mt-3" for="fecha-venta">Selecciona una fecha</label>
                        <input type="date" class="form-control" id="fecha-venta">
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Descargar',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        customClass: {
            validationMessage: 'my-validation-message'
          },
        preConfirm: () => {
            fecha = $("#fecha-venta").val();
            if (!fecha) {
              Swal.showValidationMessage(
                '<i class="fa-solid fa-circle-info"></i> La fecha es requerida'
              )
            }
          }
    }).then((response) => {
        if(response.isConfirmed){

        id_sucursal = $("#sucursal").val();
        if(id_sucursal == "all"){
            window.open("./modelo/panel/excel-ventas-diarias-all.php?fecha="+fecha + "&id_sucursal="+ id_sucursal);
        }else{
            window.open("./modelo/panel/excel-ventas-diarias.php?fecha="+fecha + "&id_sucursal="+ id_sucursal);
        }
  
        }
    });

}

function ejecutarPanelReporteComisiones(){
    Swal.fire({
        icon: 'info',
        didOpen: function () {
            $.ajax({
                type: "POST",
                url: "./modelo/panel/traer_sucursales.php",
                data: "data",
                dataType: "JSON",
                success: function (response) {
                    
                    /* $("#sucursal").append(`
                        <option value="all">Todas las plazas</option>
                    `);  */
                    response.forEach(element => {
                       
                    $("#sucursal").append(`
                        <option value="${element.id}">${element.nombre}</option>
                    `); 
                    });
                    
                }
            });

        },
        html: `
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-12">
                        <label for="sucursal">Selecciona una sucursal</label>
                        <select class="form-control" id="sucursal">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-6">
                        <label class="mt-3" for="fecha-inicio-rc">Fecha inicio</label>
                        <input type="date" class="form-control" id="fecha-inicio-rc">
                    </div>
                    <div class="col-md-12 col-6">
                        <label class="mt-3" for="fecha-final-rc">Fecha final</label>
                        <input type="date" class="form-control" id="fecha-final-rc">
                    </div>
                </div>
                   
            </div>
        `,
        confirmButtonText: 'Descargar',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        customClass: {
            validationMessage: 'my-validation-message'
          },
        preConfirm: () => {
            let fecha_inicio = $("#fecha-inicio-rc").val();
            let fecha_final = $("#fecha-final-rc").val();
            console.log(fecha_inicio);
            console.log(fecha_final);
            if (!fecha_final || !fecha_inicio) {    
              Swal.showValidationMessage(
                '<i class="fa-solid fa-circle-info"></i> La fecha es requerida'
              )
            }
          }
    }).then((response) => {
        if(response.isConfirmed){
        let fecha_inicio = $("#fecha-inicio-rc").val();
        let fecha_final = $("#fecha-final-rc").val();
        id_sucursal = $("#sucursal").val();
        console.log(id_sucursal);

        if(id_sucursal == "all"){
          console.log("LOL");
            //window.open("./modelo/panel/excel-ventas-diarias-all.php?fecha="+fecha + "&id_sucursal="+ id_sucursal);
        }else{
            window.open("./modelo/panel/excel-reporte-comision.php?fecha_inicio="+fecha_inicio +"&fecha_final="+fecha_final+ "&id_sucursal="+ id_sucursal);
        }
  
        }
    });
}