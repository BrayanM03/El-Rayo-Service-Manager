function realizarVentaCredito(){

    if ( !table.data().any()){

        toastr.warning('La tabla no tiene productos', 'Sin productos' ); 
    
    }else{
        
             //Pasar el codigo de aqui abajo al IF cuando termines
    clienteid = $("#select2-clientes-container").attr("id-cliente");
    
    $.ajax({
        type: "post",
        url: "./modelo/creditos/traer-cliente-credito.php",
        data: {"id": clienteid},
        datatype: "json",
        
        success: function (response) {
            importetotal = $("#total").val() ;
           
           

            //restante = parseFloat(importetotal) - parseFloat(abono);

            Swal.fire({
                title: "Nuevo credito",
                background: "#dcdcdc" ,
                width: '800px',
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Registrar', 
                cancelButtonColor:'#ff764d',
                didOpen: function () {   
                
                },
                preConfirm: (respuesta) =>{

                    data = {
    
                      "abono":         $("#abono").val(),
                      "restante":      $("#restante").val(),
                      "total":         $("#importe-total").val(),
                      
                    };

                    sbstring = data["restante"].substring(1);
                    restant = parseInt(sbstring);
                    console.log(restant);
                    abono_valid = data["abono"].trim();
    
                    if( data["abono"] < 0){
                     // $(".datoVacio").removeClass("datoVacio");
                     $(".border-danger").removeClass("border-danger");
                      $("#abono").addClass("border-danger");
                      Swal.showValidationMessage(
                        `El abono no puede ser negativo.`
                      );
                    }else if( abono_valid == ""){
                        // $(".datoVacio").removeClass("datoVacio");
                        $(".border-danger").removeClass("border-danger");
                         $("#abono").addClass("border-danger");
                         Swal.showValidationMessage(
                           `Ingrese una cantidad.`
                         );
                       }else if(restant < 0){
                     /*  $(".datoVacio").removeClass("datoVacio");*/
                      $(".border-danger").removeClass("border-danger"); 
                      $("#restante").addClass("border-danger");
                      Swal.showValidationMessage(
                        `El restante no puede ser negativo.`
                      )
                    }
    
                  },
                html: '<form class="mt-4" id="formulario-nuevo-credito">'+
                '<h5>Agregar nuevo registro de credito para '+ response+'</h5><br>'+
                '<div class="row">'+
                    '<div class="col-4">'+
                    '<label><b>Primer pago:</b></label></br>'+
                    '<input id="abono" type="number" class="form-control" placeholder="$ 0.00">'+
                    '</div>'+
                    '<div class="col-4">'+
                    '<label><b>Restan:</b></label></br>'+
                    '<input id="restante" type="text" class="form-control" value="$" disabled>'+
                    '</div>'+
                    '<div class="col-4">'+
                    '<label><b>Total a pagar:</b></label></br>'+
                    '<input type="text" id="importe-total" class="form-control" value="$ '+  importetotal +'" disabled>'+
                    '</div>'+
                    '<div class="col-12 mt-5">'+
                    '<div class="form-group">'+
                    '<label><b>Elige un plazo para pagar:</b></label></br>'+
                    '<select class="form-control" id="plazo">'+
                    '<option value="1">1 Semana</option>'+
                    '<option value="2">15 dias</option>'+
                    '<option value="3">1 mes</option>'+
                    '<option value="5">Sin definir</option>'+
                    '</select>'+
                    '</div>'+
                    '</div>'+

                    /*'<div class="col-12">'+
                    '<div class="form-group">'+
                    '<label><b>Productos:</b></label></br>'+
                    '<table class="table table-bordered" style="border: 1px solid black;">'+
                    '<thead class="thead-dark">'+
                    '<tr>'+
                    '<th>Codigo</th>'+
                    '<th>Concepto</th>'+ 
                    '<th>Precio</th>'+
                    '<th>Cantidad</th>'+
                    '</thead>'+
                    '<tbody id="tbody-products-credito" style="background: white;">'+
                    '</tbody>'+
                    '</div>'+
                    '</div>'+*/


                '</div>'+
        
                    '</form>'
                    
                    }).then((result) =>{ 

                        plazo = $("#plazo").val();
                        importe_total = $("#importe-total").val();
                        abono = $("#abono").val();
                        restante = $("#restante").val();

                        fimporte_total    =    importe_total.replace('$','');
                        fabono    =    abono.replace('$','');
                        frestante =    restante.replace('$','');
                        fecha = $("#fecha").val();

                        console.log(fecha);

                        if(result.isConfirmed){

                            
                                                //Insertar venta
                                llantaData = $("#pre-venta").dataTable().fnGetData();
                               
                                    
                                
                                total = $("#total").val();
                                fecha = $("#fecha").val(); 
                                cliente = $("#select2-clientes-container").attr("id-cliente");
                                metodo_pago = $("#metodos-pago").val();  
                                sucursal =$("#sucursal").val();
                                comentario = $("#hacer-comentario").attr("comentario");
                                //Enviando data


                                
                                $.ajax({
                                    type: "POST",
                                    url: "./modelo/ventas/insertar-venta.php",
                                    data: {'data': llantaData,
                                        'plazo': plazo,
                                        'cliente': cliente,
                                        'metodo_pago': metodo_pago,
                                        'fecha': fecha,
                                        'sucursal': sucursal,
                                        'total': total,
                                        'comentario': comentario,
                                        'tipo': "vt-credito"},
                                    dataType: "JSON",
                                    
                                    success: function (response) {
                                        console.log(response);
                                        if (response) {
                                            
                                            $.ajax({
                                                type: "POST",
                                                url: "./modelo/creditos/nuevo-credito.php",
                                                data: {"id_cliente": clienteid, "metodo_pago": metodo_pago, "plazo": plazo, "importe": fimporte_total, "abono": fabono, "restante": frestante, "fecha": fecha},
                                                //dataType: "",
                                                success: function (response) {
                                                  console.log(response); 
                
                                                }
                                            });

                                            
                                            Swal.fire({
                                                title: 'Venta a credito realizada',
                                                html: "<span>La venta a credito se realizo con exito</br></span>"+
                                                "ID Venta: RAY" + response,
                                                icon: "success",
                                                cancelButtonColor: '#00e059',
                                                showConfirmButton: true,
                                                confirmButtonText: 'Aceptar', 
                                                cancelButtonColor:'#ff764d',
                                                showDenyButton: true,
                                                denyButtonText: 'Reporte'
                                            },
                                            
                                            ).then((result) =>{
                                
                                                if(result.isConfirmed){
                                                //location.reload();
                                                table.ajax.reload(null,false);
                                                    $("#pre-venta tbody tr").remove();
                                                    $(".pre-venta-error").html("");
                                                    $(".products-grid-error").remove();
                                                    $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                                    $("#pre-venta_processing").css("display","none");
                                                    $("#total").val(0);
                                                    table.clear().draw();
                                                

                                                }else if(result.isDenied){
                        
                                                    window.open('./modelo/creditos/generar-reporte-credito.php?id='+ response, '_blank');
                                                    table.ajax.reload(null,false);
                                                    $("#pre-venta tbody tr").remove();
                                                    $(".pre-venta-error").html("");
                                                    $(".products-grid-error").remove();
                                                    $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                                    $("#pre-venta_processing").css("display","none");
                                                    $("#total").val(0);
                                                    table.clear().draw();
                                                        
                                                
                                                    
                                                }else{
                                                    table.ajax.reload(null,false);
                                                    $("#pre-venta tbody tr").remove();
                                                    $(".pre-venta-error").html("");
                                                    $(".products-grid-error").remove();
                                                    $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                                    $("#pre-venta_processing").css("display","none");
                                                    $("#total").val(0);
                                                    table.clear().draw();
                                                }
                                
                                                $("#hacer-comentario").attr("comentario", " ");
                                                });

                                                
                                        }
                                        
                                    }
                                }); //fin
                        }
                    });

                    abono = $("#abono");
                    abono.keyup(function () { 
                        abono_tecleado = $(this).val();
                        restante = parseFloat(importetotal) - parseFloat(abono_tecleado);
                        noNumerico = isNaN(restante);
                        restanteVal = $("#restante").val();
                        if (abono_tecleado == 0 && noNumerico == false) {
                            $("#restante").val("$" + importetotal);    
                        }else if(noNumerico == true){
                            
                            $("#restante").val("$-");
                        }
                        else{
                            $("#restante").val("$" + restante);
                        }
                        
                    });
        }
    });
   
   
                
       

    }

}

