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
            importetotal = $("#total").val();
           
           

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
                    var metodos_pago = $("#metodos-pago").val();
                    let area_metodos = $("#metodos_pagos_area");
                    var opciones = {
                        0: "Efectivo",
                        1: "Tarjeta",
                        2: "Transferencia",
                        3: "Cheque",
                        4: "Sin definir"
                      };
            
                      var arregloMetodos= metodos_pago.reduce(function(result, key) {
                        result[key] = opciones[key];
                        return result;
                      }, {});

                      for (var clave in arregloMetodos) {
                        if (Object.hasOwnProperty.call(arregloMetodos, clave)) {
                            let nombre_metodo = arregloMetodos[clave];
                            area_metodos.append(`
                            <div class="col-md-2">
                                <label>${nombre_metodo}</label>
                                <input type="number" class="form-control" id="monto_metodo_credito_${clave}" onkeyup='sumarMonto(${JSON.stringify(metodos_pago)})'  placeholder="0.00">
                            </div>
                      `);
                        }

                        // A partir del codigo forin acceder al input y obtener el valor dependiendo del metodo de pago
                        // y sumarlo para obtener el total de la venta


                      }
                       $("#abono").change(()=>{
                          
                          let importetotal = parseFloat($("#total").val());
                          let abono = parseFloat($("#abono").val());
                          let restante = importetotal - abono;
                          $("#restante").val("$" + restante);
                      });
                },
                preConfirm: (respuesta) =>{

                    data = {
    
                      "abono":         $("#abono").val(),
                      "restante":      $("#restante").val(),
                      "total":         $("#importe-total").val(),
                      
                    };

                    sbstring = data["restante"].substring(1);
                    restant = parseInt(sbstring);
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
                       }else if(parseFloat(data["abono"]) > parseFloat(data["total"])){
                        $(".border-danger").removeClass("border-danger"); 
                        $("#restante").addClass("border-danger");
                        Swal.showValidationMessage(
                          `El abono supera el total.`
                        )
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
                '<div class="row" id="metodos_pagos_area">'+
                '</div>'+    
                '<div class="row mt-2">'+
                    '<div class="col-4">'+
                    '<label><b>Primer pago:</b></label></br>'+
                    '<input id="abono" onchange="restarAbono()" disabled type="number" class="form-control" value="0" placeholder="$ 0.00">'+
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

                        if(result.isConfirmed){

                            
                                                //Insertar venta
                                llantaData = $("#pre-venta").dataTable().fnGetData();
                                
                                total = $("#total").val();
                                fecha = $("#fecha").val(); 
                                cliente = $("#select2-clientes-container").attr("id-cliente");
                                sucursal_id =$("#sucursal").val();
                                comentario = $("#hacer-comentario").attr("comentario");
                                //Creando objecto de pagos
                                var metodos_pago = $("#metodos-pago").val();
                                var opciones = {
                                    0: "Efectivo",
                                    1: "Tarjeta",
                                    2: "Transferencia",
                                    3: "Cheque",
                                    4: "Sin definir"
                                };
                              
                               //Transfotmando el arreglo de los metodos
                               var metodosPago = []; // Arreglo donde se almacenarán los métodos de pago

                                var inputs = document.querySelectorAll('#metodos_pagos_area input[type="number"]');

                                inputs.forEach(function(input) {
                                var clave = input.id.split("_")[3]; // Obtener la clave del método de pago del ID del input
                                var metodo = opciones[clave]; // Obtener el nombre del método de pago según la clave
                                
                                var monto = parseFloat(input.value); // Obtener el monto ingresado en el input
                                
                                // Crear un objeto con la información del método de pago y el monto
                                var metodoPago = {
                                    clave: clave,
                                    metodo: metodo,
                                    monto: monto
                                };
                                
                                metodosPago.push(metodoPago); // Agregar el objeto al arreglo metodosPago
                                });

                                console.log(metodosPago);
                                //Enviando data
                                
                                $.ajax({ 
                                    type: "POST",
                                    url: "./modelo/ventas/insertar-venta.php",
                                    data: {'data': llantaData,
                                        'plazo': plazo,
                                        'cliente': cliente,
                                        'metodo_pago': metodosPago,
                                        'fecha': fecha,
                                        'sucursal' : sucursal_id,
                                        'total': total,
                                        'comentario': comentario,
                                        'tipo': "vt-credito"},
                                    dataType: "JSON",
                                    
                                    success: function (response) {
                                        if (response.estatus) {
                                            
                                            $.ajax({
                                                type: "POST",
                                                url: "./modelo/creditos/nuevo-credito.php",
                                                data: {"id_cliente": clienteid, "arreglo_metodos":metodosPago, "metodo_pago": metodos_pago, "sucursal_id": sucursal_id, "plazo": plazo, "importe": fimporte_total, "abono": fabono, "restante": frestante, "fecha": fecha},
                                                //dataType: "",
                                                success: function (response) {
                
                                                }
                                            });

                                            
                                            Swal.fire({
                                                title: 'Venta a credito realizada',
                                                html: "<span>La venta a credito se realizo con exito</br></span>"+
                                                "ID Venta: RAY" + response.folio,
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
                                                table.ajax.reload(null,false);
                                                    $("#pre-venta tbody tr").remove();
                                                    $(".pre-venta-error").html("");
                                                    $(".products-grid-error").remove();
                                                    $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                                    $("#pre-venta_processing").css("display","none");
                                                    $("#total").val(0);
                                                    table.clear().draw();
                                                

                                                }else if(result.isDenied){
                        
                                                    window.open('./modelo/creditos/generar-reporte-credito.php?id='+ response.folio, '_blank');
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

                    
        }
    });
   
   
                
       

    }

}

function sumarMonto(metodos) {
    let monto_acumulado = 0;
    metodos.forEach(element => {
        var valor = $("#monto_metodo_credito_" + element).val();
       
        var monto_metodo = valor =="" || undefined ? 0 : parseFloat(valor);

        monto_acumulado += monto_metodo;
    });
   $("#abono").val(monto_acumulado);
   // Simular el evento onchange
    var event = new Event("change");
    document.getElementById('abono').dispatchEvent(event);

}

function restarAbono(){

    let abono_valor = $("#abono").val();
    let importetotal = $("#total").val();
    console.log(importetotal);
    restante = importetotal - abono_valor;
    $("#restante").val("$" + restante);

        //abono_tecleado = $(this).val();
        restante = parseFloat(importetotal) - parseFloat(abono_valor);
        noNumerico = isNaN(restante);
        restanteVal = $("#restante").val();
        if ( noNumerico == false) {
            $("#restante").val("$" + importetotal);    
        }else if(noNumerico == true){
            
            $("#restante").val("$-");
        }
        else{
            $("#restante").val("$" + restante);
        }
        
   
};

