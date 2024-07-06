MostrarUsuarios();
function MostrarUsuarios() {  
    $.fn.dataTable.ext.errMode = 'none';
    id_sesion = $("#emp-title").attr("sesion_id");
    rol_sesion = $("#emp-title").attr("sesion_rol");

table = $('#usuarios').DataTable({
      
    serverSide: false,
    ajax: {
        method: "POST",
        url: "./modelo/comisiones/server_processing.php",
        dataType: "json"
 
    },  

  columns: [   
    //{ title: "#",              data: null             },
    { title: "id",            data: 0         },
    { title: "Nombre",         data: null, render: function(data,type,row) {
        return '<span>'+ row[1] + ' ' + row[2] +'</span>';
        }
    },
    { title: "Sucursal",       data: 3      },
    { title: "Comisión venta",       data: 4      },
    { title: "Comisión credito",       data: 5      },
    { title: "Editar comisión",
      data: null,
      className: "celda-acciones",
      render: function (data) {
        return '<div style="display: flex"><button onclick="editarComision(' +data[0] + ', '+ data[4] +', '+ data[5] +');" type="button" class="buttonPDF btn btn-success" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br>';
        /* if(rol_sesion == 2){ //Esta configuracion es especifica para el usuario de Mario y Amita se debe en un furturo hacer mas dinamico
            return '<div style="display: flex"><button onclick="editarCliente(' +row.id+ ');" type="button" class="buttonPDF btn btn-success" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br>';
        }else{
            return '<div style="display: flex"><button onclick="editarCliente(' +row.id+ ');" type="button" class="buttonPDF btn btn-success" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br>'+
            '<button type="button" onclick="borrarCliente('+ row.id +');" class="buttonBorrar btn btn-warning"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
          
        } */
    },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  order: [2, "desc"],
 
  
});

}

function editarComision(id, comision_venta, comision_credito){
    console.log(id);
    Swal.fire({
        html:`
        <div class="container">
        <h3>Editar comisión</h3>
            <div class="row">
                <div class="col-6">
                    <label for="comision-porcentaje">Porcentaje ventas normales</label>
                    <input type="number" id="comision-porcentaje-venta" value="${comision_venta}" class="form-control" placeholder="0">
                </div>
                <div class="col-6">
                    <label for="comision-porcentaje">Porcentaje ventas credito</label>
                    <input type="number" id="comision-porcentaje-credito" value="${comision_credito}" class="form-control" placeholder="0">
                </div>
            </div>        
        </div>
        `,
        confirmButtonText: 'Guardar',
        showCancelButton: true,
    }).then((result) => {
        if(result.isConfirmed){
            let comision_venta = $("#comision-porcentaje-venta").val()
            let comision_credito = $("#comision-porcentaje-credito").val()
            $.ajax({
                type: "POST",
                url: "./modelo/comisiones/editar_comision.php",
                data: {comision_credito, comision_venta, "id": id},
                dataType: "JSON",
                success: function (response) {
                    if(response.estatus){
                        Swal.fire({
                            icon: 'success',
                            title: 'Comisión editada correctamente',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        table.ajax.reload();
                }
            }
            });
        }
    })
} 