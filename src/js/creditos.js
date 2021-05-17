function MostrarCreditos() {  
    //$.fn.dataTable.ext.errMode = 'none';

table = $('#creditos').DataTable({
      
    serverSide: false,
    ajax: {
        method: "POST",
        url: "./modelo/creditos/traer-creditos.php",
        dataType: "json"
 
    },  

  columns: [   
    { title: "#",              data: null             },
    { title: "id",             data: "id", render: function(data,type,row) {
        return '<span>CRED'  + data +'</span>';
        }},
    { title: "Cliente",        data: "cliente"   },
    { title: "Fecha inicio",   data: "fecha_inicial"   },
    { title: "Fecha final",    data: "fecha_final"    },
    { title: "Total",          data: "total"          },
    { title: "Restante",       data: "restante"       },
    { title: "Pagado",         data: "pagado"         },
    { title: "Total",          data: "total"          },
    { title: "Estatus",        data: "estatus", render: function(data,type,row) {
        switch (data) {
            case "1":
                return '<span class="badge badge-info">Primer abono</span>';    
            break;
            case "2":
                return '<span class="badge badge-warning">Pagando</span>';    
            break;
            case "3":
                return '<span class="badge badge-dark">Finalizado</span>';    
            break;
        
            default:
                break;
        }
        
        } },
    { title: "Plazo",          data: "plazo", render: function(data,type,row) {
        switch (data) {
            case "1":
                return '<span>7 dias</span>';    
            break;
            case "2":
                return '<span>15 dias</span>';    
            break;
            case "3":
                return '<span>1 mes</span>';    
            break;
            case "4":
                return '<span>1 año</span>';    
            break;
            case "5":
                return '<span>7 dias</span>';    
            break;
        
            default:
                break;
        }
        
        }      },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
    
        return '<div style="display: flex"><button onclick="traerCredito(' +row.id+ ');" type="button" class="buttonPDF btn btn-primary" style="margin-right: 8px"><span class="fa fa-eye"></span><span class="hidden-xs"></span></button><br><button type="button" onclick="borrarVenta('+ row.folio +');" class="buttonBorrar btn btn-warning"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
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

$("table.dataTable thead").addClass("table-info")

 //Enumerar las filas "index column"
 table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} ).draw();

}

MostrarCreditos();


function borrarVenta(id) {

    Swal.fire({
        title: "Eliminar Venta",
        html: '<span>¿Estas seguro de eliminar esta venta?</span>',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Borrar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){    

    $.ajax({
        type: "post",
        url: "./modelo/ventas/borraVentaHistorial.php",
        data: {"folio": id},
        success: function (response) {
           if (response==1) {
              
            Swal.fire({
                title: 'Venta eliminada',
                html: "<span>La venta se elimino con exito</span>",
                icon: "success",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
            }).then((result) => {  

                if(result.isConfirmed){
                    location.reload();
                }});

           
           }else{
            Swal.fire({
                title: 'Venta no eliminada',
                html: "<span>La venta no se pudo eliminar, dedido a algun error inesperado</span>",
                icon: "warning",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
                showDenyButton: true,
                
            }).then((result) => {  

                if(result.isConfirmed){
                    location.reload();
                }});
           }
        }
    });
}

        });
  }


  function traerCredito(id){

    $.ajax({
        type: "post",
        url: "./modelo/creditos/traer-abonos.php",
        data: {"id_credito": id},
        dataType: "JSON",
        success: function (response) {

            Swal.fire({
                title: "Historial de credito",
                background: "#dcdcdc" ,
                didOpen: function () {
                  $(document).ready(function() {

                    $("#abonar-btn").on('click', function () { 
                      var item = document.getElementById("chevron");
                      var chevron = $("#chevron");
                      var hasChevronRight = item.classList.contains( 'chevron-der' );
                      if (hasChevronRight == true) {
                        chevron.removeClass("chevron-der");
                        chevron.addClass("chevron-abaj");
                        $("#contenedor-abono").empty();
                      }else{
                        chevron.removeClass("chevron-abaj");
                        chevron.addClass("chevron-der");
                        $("#contenedor-abono").append('<input type="number" id="abono-in" style="width: 120px; margin-right: 10px;" class="form-control">'+
                        '<div id="registrar-abono" class="btn btn-warning">Registrar</div>');
                        $("#registrar-abono").on('click', function () { 
                          registrarAbono(id);
                        });
                      }

                     }) //Termino de clickear el boton 

                  
                    

                     function registrarAbono(id) {
                      abono_in = $("#abono-in").val();
                      if(abono_in == null || abono_in ==0){
                        alert("Ingresa una cantidad");
                      }else{
                        $.ajax({
                          type: "POST",
                          url: "./modelo/creditos/insertar-abono.php",
                          data: {"id-credito":  id, "abono": abono_in},
                          dataType: "JSON",
                          success: function (response) {
                            tabla.ajax.reload();
                            $("#pagado").val(response.pagado_nuevo);
                            $("#restante").val(response.restante_nuevo);

                            
                          }
                        });
                      }
                       
                       }


                     tabla = $("#tabla-abonos").DataTable({
                      //destroy: true,
                      //processing: true,
                      //serverSide: true,
                      ajax: {
                       type: "POST",
                        data: {"id_cred": id},
                        url: "./modelo/creditos/traer-abonos.php",
                        dataType: "JSON"
                    },   
                    columns: [   
                      { title: "#",             data: null , width:"60px"            },
                      { title: "Abono",         data: "abono"         },
                      { title: "fecha",         data: "fecha_abono"    }],
                      
                      paging: false,
                      searching: false,
                      scrollY: "260px",
                      info: false,
                      responsive: true,
                     });

                     tabla.on( 'order.dt search.dt', function () {
                      tabla.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                          cell.innerHTML = i+1;
                         
                      } );
                  } ).draw();


                });
                },
                html: '<form class="mt-4" id="formulario-editar-abono">'+
            
                  '<div class="row">'+
                      '<div class="col-8">'+
                      '<div class="form-group">'+
                      '<label><b>Cliente:</b></label></br>'+
                      '<input class="form-control" value="'+ response.cliente +'" disabled>'+
                      '</div>'+
                      '</div>'+
            
                      '<div class="col-4">'+
                      '<div class="form-group">'+
                      '<label for=""><b>Total:</b></label></br>'+
                      '<input type="text" class="form-control" id="total" value="$ '+ response.total+'" autocomplete="off" disabled>'+
                      '</div>'+
                      '</div>'+
                   '</div>'+
            
            
                   '<div class="card tabla-abonos">'+

                   '<div class="row">'+
                   '<div class="col-4">'+
                   '<div id="abonar-btn" class="btn btn-info" style="width: 100px; margin: 10px; ">Abonar<i id="chevron" class="chevron-abaj ml-2 fas fa-chevron-down"></i></div>'+
                   '</div>'+
                   '<div class="col-8">'+
                   '<div id="contenedor-abono" class="form-group" style="display:flex; margin-top:10px; "></div>'+
                   '</div>'+
                   '</div>'+
                   

                   '<div class="row">'+
                   '<div class="col-12 aling-items-center">'+
                   '<table style="margin: 8px;" id="tabla-abonos" class="table table-hover table-bordered">'+  
                   '<thead class="thead-dark"><tr>'+
                   '<th>#</th>'+ 
                   '<th>cantidad</th>'+
                   '<th>Fecha</th>'+
                   '</tr>'+
                   '</thead>'+
                   
                   '<tbody>'+
                   
                   '</tbody>'+
                   '</table>'+
                   '</div></div>'+

                          
                   '</div>'+
                   '<div class="row">'+
                   '<div class="col-6 aling-items-center">'+
                   '<div class="mt-2"><b>Pagado:</br> </div>'+
                   '<input value="$'+response.pagado+'" id="pagado" class="form-control" disabled>'+
                   '</div>'+
                   '<div class="col-6 aling-items-center">'+
                   '<div class="mt-2"><b>Restante:</br> </div>'+
                   '<input value="$'+response.restante+'" id="restante" class="form-control" disabled>'+
                   '</div>'+
                   '</div>'+
            
            '</form>',
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
                cancelButtonColor: '#00e059',
                showConfirmButton: false,
                confirmButtonText: 'Actualizar', 
                cancelButtonColor:'#ff764d'
        
            }).then((result) =>{
            
            
              });
        }
    });
    
    

  }


 