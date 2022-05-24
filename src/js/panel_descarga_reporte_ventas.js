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
        window.open("./modelo/panel/excel-ventas-diarias.php?fecha="+fecha + "&id_sucursal="+ id_sucursal);
  
        }
    });

}