function addSucursal(){

    Swal.fire({

        title: 'Agregar sucursal nueva',
        confirmButtonText:'Registrar',
        html: `
        <div class="container">

        <form id="nueva_suc_form">
            <div class="row">
                <div class="col-12 col-md-12">
                    <label for="nombre_sucursal"><b>Nombre de la sucursal</b></label>
                    <input class="form-control" id="nombre_sucursal" name="nombre_sucursal" placeholder="Ingrese aquí sucursal"/>
                </div>
            </div>
            <div class="row mt-3">
            <div class="col-12 col-md-12">
                <label for="nombre_sucursal"><b>Telefono</b></label>
                <input class="form-control" type="number" name="telefono" id="telefono" placeholder="Ingrese aquí un telefono"/>
            </div>
        </div>
            <div class="row mt-3">
            <div class="col-12 col-md-12">
                <label for="nombre_sucursal"><b>Dirección</b></label>
                <textarea class="form-control" name="direccion" id="direccion" placeholder="Direccion de la nueva sucursal"></textarea>
            </div>
            </div>
        </div>
            </div>
        `,
        
        preConfirm: function () {

            if($("#nombre_sucursal").val() == ""){
                Swal.showValidationMessage(
                    `Nombre invalido`
                  )
            }else if($("#telefono").val() == ""){
                Swal.showValidationMessage(
                    `Telefono invalido`
                  )
            }else if($("#direccion").val() == ""){
                Swal.showValidationMessage(
                    `Dirección invalida`
                  )
            }

        }
    }).then(function(response){

        if(response.isConfirmed){
            let form = $("#nueva_suc_form").serialize();
            console.log(form);

            $.ajax({
                type: "POST",
                url: "./modelo/sucursales/agregar-sucursal.php",
                data: form,
                success: function (response) {
                    if(response == 1){
                        Swal.fire({
                            icon: "success",
                            title: 'Agregada',
                            allowOutsideClick: false,
                            html: `<b>¡La sucursal fue agregada con exito!</b>`
                            
                        }).then(function(res){
                            if(res.isConfirmed){
                                window.setTimeout(recargarPag, 1000);
                            }
                        });
                    }
                }
            });
        }

        function recargarPag(){
            location.reload(true);
            }
        

    })
};

