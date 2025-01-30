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

function ejecutarPanelTipoComision(){
    Swal.fire({
        icon: 'info',
        html: `
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-12">
                        <label for="tipo-comision">Selecciona el tipo de comisión</label>
                        <select class="form-control" id="tipo-comision">
                         <option value="encargado">Encargado de sucursal</option>
                         <option value="vendedor">Vendedor</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Proceder',
    }).then((r)=>{
        if(r.isConfirmed){
            let tipo_comision=$("#tipo-comision").val();
            console.log(tipo_comision);
            if(tipo_comision=='encargado'){
                 ejecutarPanelReporteComisionesEncargado()
            }else{
                ejecutarPanelReporteComisionesVendedor()
            }
        }
    })
}

function ejecutarPanelReporteComisionesEncargado(){
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
                        <select class="form-control" id="sucursal"></select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-12">
                        <label for="sucursal">Año</label>
                        <select class="form-control" id="year-comision">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-6">
                        <label class="mt-3" for="fecha-inicio-rc">Mes</label>
                        <select class="form-control" id="mes-comision">
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-6 col-12">
                        <div class="btn btn-success btn-icon-split" onclick="descargarExcel(1)">
                            <span class="icon text-white-50">
                            <i class="fas fa-file-excel"></i>
                            </span>
                            <span class="text">Descargar Excel</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="btn btn-danger btn-icon-split" onclick="descargarPDF(1)">
                            <span class="icon text-white-50">
                            <i class="fas fa-file-pdf"></i>
                            </span>
                            <span class="text">Descargar PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Descargar',
        showCloseButton: true,
        showCancelButton: false,
        showConfirmButton: false,
        showLoaderOnConfirm: true,
        customClass: {
            validationMessage: 'my-validation-message'
          },
    })
}

function ejecutarPanelReporteComisionesVendedor(){
    Swal.fire({
        icon: 'info',
        didOpen: function () {
            
            $.ajax({
                type: "POST",
                url: "./modelo/busqueda/traer-usuarios.php",
                data: "data",
                dataType: "JSON",
                success: function (respuesta) {
                    respuesta.forEach(element => {
                       
                    $("#asesor").append(`
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
                        <label for="asesor">Selecciona una asesor</label>
                        <select class="form-control" id="asesor"></select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-12">
                        <label for="sucursal">Año</label>
                        <select class="form-control" id="year-comision">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-6">
                        <label class="mt-3" for="fecha-inicio-rc">Mes</label>
                        <select class="form-control" id="mes-comision">
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-6 col-12">
                        <div class="btn btn-success btn-icon-split" onclick="descargarExcel(2)">
                            <span class="icon text-white-50">
                            <i class="fas fa-file-excel"></i>
                            </span>
                            <span class="text">Descargar Excel</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="btn btn-danger btn-icon-split" onclick="descargarPDF(2)">
                            <span class="icon text-white-50">
                            <i class="fas fa-file-pdf"></i>
                            </span>
                            <span class="text">Descargar PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Descargar',
        showCloseButton: true,
        showCancelButton: false,
        showConfirmButton: false,
        showLoaderOnConfirm: true,
        customClass: {
            validationMessage: 'my-validation-message'
          },
    })
}

function descargarPDF(tipo){
    let mes = $("#mes-comision").val();
    let year = $("#year-comision").val();
    if(tipo==1){
        id_sucursal = $("#sucursal").val();

        if(id_sucursal == "all"){
        console.log("LOL");
            //window.open("./modelo/panel/excel-ventas-diarias-all.php?fecha="+fecha + "&id_sucursal="+ id_sucursal);
        }else{
            window.open("./modelo/comisiones/pdf-comision-encargado.php?mes="+mes +"&year="+year+ "&id_sucursal="+ id_sucursal+"&tipo_comision="+tipo);
        }
    }else{
        let id_asesor = $('#asesor').val();
        window.open("./modelo/comisiones/pdf-comision-asesor.php?mes="+mes +"&year="+year+ "&id_asesor="+ id_asesor+"&tipo_comision="+tipo);
    }
    
}

function descargarExcel(tipo){
    let mes = $("#mes-comision").val();
    let year = $("#year-comision").val();
    console.log(tipo);
    if(tipo ==1){
        id_sucursal = $("#sucursal").val();
        window.open("./modelo/comisiones/excel-comision-encargado.php?mes="+mes+"&year="+year+ "&id_sucursal="+ id_sucursal);
    }else{
        let id_asesor = $('#asesor').val();
        window.open("./modelo/comisiones/excel-comision-asesor.php?mes="+mes+"&year="+year+ "&id_asesor="+ id_asesor);

    }
}