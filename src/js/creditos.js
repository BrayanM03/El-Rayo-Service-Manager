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
    { title: "Cliente",        data: "cliente"        },
    { title: "Fecha inicio",   data: "fecha_inicial"  },
    { title: "Fecha final",    data: "fecha_final"    },
    { title: "Total",          data: "total"          },
    { title: "Restante",       data: "restante"       },
    { title: "Pagado",         data: "pagado"         },
    { title: "Total",          data: "total"          },
    { title: "Estatus",        data: "estatus", render: function(data,type,row) {
        switch (data) {
          case "0":
                return '<span class="badge badge-primary">Sin abono</span>';    
            break;

            case "1":
                return '<span class="badge badge-info">Primer abono</span>';    
            break;
            case "2":
                return '<span class="badge badge-warning">Pagando</span>';    
            break;
            case "3":
                return '<span class="badge badge-success">Finalizado</span>';    
            break;
            case "4":
              return '<span class="badge badge-danger">Vencido</span>';    
          break;
          case "5":
            return '<span class="badge badge-dark">Cancelada</span>';    
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
        { title: "Venta",          data: null, render: function (data, type, row) { 
          return 'RAY' + row.id_venta;
         }          },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
    
        return '<div style="display: flex"><button onclick="traerCredito(' +row.id+ ');" type="button" class="buttonPDF btn btn-primary" style="margin-right: 8px"><span class="fa fa-eye"></span><span class="hidden-xs"></span></button><br><button type="button" onclick="borrarCredito('+ row.id +');" class="buttonBorrar btn btn-warning"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
      },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: true,
  order: [1, "desc"],
 
  
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



function borrarCredito(id) {

    Swal.fire({
        title: "Eliminar credito",
        html: '<span>¿Estas seguro de eliminar este credito?</span>',
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
        url: "./modelo/creditos/borrar-credito.php",
        data: {"id": id},
        success: function (response) {
           if (response==1) {
              
            Swal.fire({
                title: 'Credito eliminado',
                html: "<span>El credito se eliminó con exito</span>",
                icon: "success",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
            }).then((result) => {  

                if(result.isConfirmed){
                  table.ajax.reload(null,false);
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
                  table.ajax.reload(null,false);
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
                width:'80vw',
                didOpen: function () {
                  $(document).ready(function() {

                    //Metodos de pago select 2 

                    $("#metodos_pago").select2({
                      placeholder: "Metodo de pago",
                      theme: "bootstrap",
                      templateResult: formatState,
                  });
              
              
                  function formatState (state) {
                      if (!state.id) {
                        return state.text;
                      }
              
                      switch (state.text) { 
                          case "Efectivo":
                              var $state = $(
                                  '<span><i class="fas fa-money-bill-wave"></i> '+state.text+'</span>'
                                  
                                );
                              
                              break;
                          case "Tarjeta":
                              var $state = $(
                                  '<span><i class="fas fa-money-check"></i> '+state.text+'</span>'
                                  
                                );
                              
                              break;
                          case "Transferencia":
                              var $state = $(
                                  '<span><i class="fas fa-university"></i> '+state.text+'</span>'
                                      
                              );
                                  
                                  break;           
                          case "Cheque":
                              var $state = $(
                                  '<span><i class="fas fa-money-check-alt"></i> '+state.text+'</span>'
                              );
                                          
                          break; 
                          
                          case "Sin definir":
                              var $state = $(
                                  '<span><i class="fas fa-question"></i> '+state.text+'</span>'
                              );
                                          
                          break; 
              
                          default:
                              break;
                      }
              
                      
                      return $state;
                    };

                    //Fin select2 meotodos de pago

                    restante = $("#restante").val();
                    pagado = $("#pagado").val();
                    if(restante=="$0.00" && pagado != "$0.00" ){
                      $("#alerta").empty();
                      $("#alerta").append('<div class="alert alert-success" role="alert">'+
                      'Credito pagado'+
                       '</div>');
                    }

                   /*  $("#abonar-btn").on('click', function () {                  

                      var item = document.getElementById("chevron");
                      var chevron = $("#chevron");
                      var hasChevronRight = item.classList.contains( 'chevron-der' );
                      if (hasChevronRight == true) {
                        $("#abono-in").removeClass("animation");
                        $("#abono-in").addClass("animation-out");

                        chevron.removeClass("chevron-der");
                        chevron.addClass("chevron-abaj");


                        function salida() { 
                          $("#contenedor-abono").fadeOut(function() {
                            $("#contenedor-abono").empty();
                            });
                         }
                        setTimeout(salida,400);
                      }else{
                        $("#contenedor-abono").fadeIn();
                        chevron.removeClass("chevron-abaj");
                        chevron.addClass("chevron-der");
                        $("#contenedor-abono").append('<input type="number" id="abono-in" style="width: 120px; margin-right: 10px;" class="animation form-control">'+
                        '<div id="registrar-abono" class="btn btn-warning">Registrar</div>');
                        $("#abono-in").removeClass("animation-out");
                        $("#abono-in").addClass("animation");

                        $("#registrar-abono").on('click', function () { 
                          registrarAbono(id);
                        });
                      }

                     }) //Termino de clickear el boton 
 */
                     $("#registrar-abono").on('click', function () { 
                      registrarAbono(id);
                    });
                    

                     function registrarAbono(id) {
                      abono_in = $("#abono").val();
                      metodos = $("#metodos_pago :selected").val();
                      fecha = $("#fecha").val();
                      if($("#restante").val() == "$0.00"){
                        $("#alerta").empty();
                        $("#alerta").append('<div class="alert alert-success" role="alert">'+
                        'El credito ya esta pagado.'+
                         '</div>');
                      }else{

                      
                      if(abono_in == null || abono_in ==0){ 
                        $("#alerta").empty();
                              $("#alerta").append('<div class="alert alert-warning" role="alert">'+
                              'Ingresa una cantidad.'+
                               '</div>');
                      }else if(abono_in < 0){
                        $("#alerta").empty();
                        $("#alerta").append('<div class="alert alert-warning" role="alert">'+
                        'No puedes ingresar cantidades negativas.'+
                         '</div>');
                      }else if(metodos == "" || metodos == null){
                        $("#alerta").empty();
                        $("#alerta").append('<div class="alert alert-warning" role="alert">'+
                        'Elige un metodo de pago.'+
                         '</div>');
                      }else{


                        $.ajax({
                          type: "POST",
                          url: "./modelo/creditos/insertar-abono.php",
                          data: {"id-credito":  id, "abono": abono_in, "metodo" : metodos, "fecha": fecha},
                          dataType: "JSON",
                          success: function (response) {
                            if(response == 1){
                              $("#alerta").empty();
                              $("#alerta").append('<div class="alert alert-warning" role="alert">'+
                              'El abono sobrepasa el total'+
                               '</div>');
                            }else if(response == 6){
                              $("#alerta").empty();
                              $("#alerta").append('<div class="alert alert-warning" role="alert">'+
                              'Esta venta esta cancelada, no puedes agregar mas abonos.'+
                               '</div>');
                            }else{
                              $("#alerta").empty();
                              tabla.ajax.reload(null,false);
                              $("#pagado").val(response.pagado_nuevo);
                              $("#restante").val(response.restante_nuevo);
                            }
                          

                            
                          }
                        });
                      }
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
                      { title: "Fecha",         data: "fecha_abono"    },
                      { title: "Hora",          data: "hora_abono"    },
                      { title: "Metodo",        data: "metodo_pago"   },
                      { title: "Usuario",        data: "usuario"    },
                      { title: "Accion",
                      data: null,
                      className: "celda-acciones",
                      render: function (row, data) {
                    
                        return '<div style="display: flex"><button metodo="'+ row.metodo_pago +'" fecha="'+ row.fecha_abono +'" abono="'+ row.abono +'" idrow="'+ row.id +'" type="button" class="buttonedit btn btn-primary" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" onclick="borrarAbono('+ row.id +');" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                      },
                    }],
                      
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

                  if(response.estatus == "5"){
                    
                      
                        $("#alerta").empty();
                        $("#alerta").append('<div class="alert alert-danger" role="alert">'+
                        'Esta venta esta cancelada.'+
                         '</div>');
                    
                   
                  }

                  tabla.on('click', '.buttonedit', function() {
  
                    let $tr = $(this).closest('tr');
                    abono = $(this).attr("abono");
                    fecha = $(this).attr("fecha");
                    metodo_p = $(this).attr("metodo");
                    console.log(abono);

                    $("#cuerpo_edit").append('<div class="row">'+
                    '<div col="col-12 col-md-3">'+
                    'Abono: <input type="text" class="form-control" value="'+ abono +'"></div>'+
                    '<div col="col-12 col-md-3" style="margin-left:20px;">'+
                    'Fecha: <input type="date" class="form-control" value="'+fecha+'"></div>'+
                    '<div col="col-12 col-md-3" style="margin-left:20px;">'+
                    'Metodo de pago: <select type="text" class="form-control" val="hola">'+
                    '<option value="Efectivo">Efectivo</option>'+
                    '<option value="Targeta">Targeta</option>'+
                    '<option value="Transferencia">Transferencia</option>'+
                    '<option value="Cheque">Cheque</option>'+
                    '<option value="Sin definir">Sin definir</option>'+
                    '</select></div>'+  
                    '<div col="col-12 col-md-3">'+              
                    '<buttom class="btn btn-info ml-2 mt-4" name="registrar-abono" id="registrar-abono">Actualizar</buttom>'+
                    '</div>'+
                    '</div>');
                  /*   let $id = $(this).attr("idrow");
                    let $importe = $(this).attr("importe"); */
                  
                   /*  $.ajax({
                      type: "POST",
                      url: "./modelo/ventas/borrar-producto-temp.php", 
                      data:{"id": $id, "borrar":"borrar"},
                      success: function(response) {
                        if(response == 1){
                            //tabla_presalida.ajax.reload(null, false);
                            // Le pedimos al DataTable que borre la fila
                            table.row($tr).remove().draw(false);
                  
                            
                        toastr.success('Producto borrado con exito', 'Correcto' );
                        total = $("#total").val();
                        result =  parseInt(total) - parseInt($importe);
                        console.log(result);
                        
                        if(total == 0){
                          $("#total").val(0);
                        }else{
                          $("#total").val(result);
                        }
                        }else{
                          toastr.warning('Hubo un error al borrar el producto', 'Error' );
                        }
                  
                      }
                  
                    }); */
                  
                  
                  });
                   

                  
                  


                });
                },

                html: '<div class="row">'+
              
                '<div class="col-12 col-md-8">'+
              '<div class="form-group">'+
                '<label><b>Cliente:</b></label>'+
                '<input class="form-control" type="text" value="'+ response.cliente +'" id="cliente" name="cliente" disabled>'+
              '</div>'+
              '</div>'+
                '<div class="col-12 col-md-3">'+
                  '<div class="form-group">'+
                    '<label><b>Fecha</b></label>'+
                    '<input type="date" class="form-control" name="fecha" id="fecha">'+
                  '</div>'+
                  '</div>'+
                
    '<div class="col-12 col-sm-4" >'+
    '<form class="mt-4" id="abonos">'+

        '<div class="row">'+

        '<div class="col-12 col-md-12">'+
        '<div class="form-group" id="area-solucion">'+
        '<label><b>Total</b></label>'+
        '<input type="text" class="form-control" value="$'+ response.total +'" name="total" id="total" placeholder="0.00" disabled>'+
        '</div>'+
        '</div>'+
        

        '<div class="col-12 col-md-12">'+
        '<div class="form-group">'+
            '<label><b>Pagado</b></label>'+
            '<input type="text" class="form-control" value="$'+ response.pagado +'" name="pagado" id="pagado" placeholder="0.00" disabled>'+
        '</div>'+
        '</div>'+

        '</div>'+
        '<div class="col-12 col-md-12">'+
        '<div class="form-group" id="area-solucion">'+
        '<label><b>Restante</b></label>'+
        '<input type="text" class="form-control" value="$'+ response.restante +'" name="restante" id="restante" placeholder="0.00" disabled>'+
        '</div>'+
        '</div>'+

      


'</form>'+
'</div>'+


'<div class="col-12 col-sm-7 mt-4">'+
'<div class="row">'+

'<div class="col-12 col-md-5">'+
'<div class="form-group">'+
    '<label><b>Abonar</b></label>'+
    '<input type="number" class="form-control" id="abono" name="abono" placeholder="$ 00.00">'+
    '<div class="invalid-feedback">Campo requerido.</div>'+
'</div>'+
'</div>'+
'<div class="col-12 col-md-5">'+
'<div class="form-group">'+
    '<label><b>Metodo de pago</b></label>'+
    '<select class="form-control" id="metodos_pago" name="metodo_pago">'+
    '<option value="Efectivo">Efectivo</option>'+
    '<option value="Targeta">Targeta</option>'+
    '<option value="Transferencia">Transferencia</option>'+
    '<option value="Cheque">Cheque</option>'+
    '<option value="Sin definir">Sin definir</option>'+
    '</select>'+
    '<div class="invalid-feedback">Sobrepasaste el stock.</div>'+
'</div>'+
'</div>'+

'<div class="col-12 col-md-2">'+
'<div class="form-group">'+
'<buttom class="btn btn-info" style="height:40px; margin-top:27px" name="registrar-abono" id="registrar-abono">Abonar</buttom>'+
'</div>'+
'</div>'+

'<div class="col-12 col-md-12">'+
'<span><table id="tabla-abonos" class="table table-primary table-hover table-striped table-bordered"></table></span>'+
'</div>'+
'<div id="alerta"></div>'+
'<div class="col-12 col-md-12">'+
'<div id="cuerpo_edit" style="margin:auto;"></div>'+
'</div>'+
'</div>',
                
              /*   html: '<form class="mt-4" id="formulario-editar-abono">'+
            
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
                   '<div id="alerta"></div>'+
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
            
            '</form>', */
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
                cancelButtonColor: '#00e059',
                showConfirmButton: false,
                confirmButtonText: 'Actualizar', 
                cancelButtonColor:'#ff764d'
        
            }).then((result) =>{

              table.ajax.reload(null,false);
            
            
              });

              table.ajax.reload(null,false);
        }
    });
    
    

  }


 