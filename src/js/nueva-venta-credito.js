function revisarCredito(){
    let credito_vencido = $("#select2-clientes-container").attr("credito_vencido");
    let cliente_nuevo = $("#select2-clientes-container").attr("cliente_nuevo");
    let nombre_cliente = $("#select2-clientes-container").attr("nombre");
    console.log(cliente_nuevo);
    if(credito_vencido==1){
        avisoCreditoVencido(nombre_cliente)
    }else if(cliente_nuevo == 0){
        avisoClienteNuevo(nombre_cliente)
    }else{
        realizarVentaCredito();
    }
}

function avisoClienteNuevo(nombre_cliente){ 
    Swal.fire({
        icon: 'warning',
        width: '800px',
        html:`
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <img class="mb-3" src="src/img/sad.png" style="width:80px;">
                        <h5>El cliente: ${nombre_cliente} es un cliente sin credito, ya que es nuevo o nunca se le a vendido un credito.</h5>
                    </div>
                </div>
                <small style="color:gray;">Ingrese el token para autorizar el credito</small>
                <div class="row mb-3 mt-3 justify-content-center">
                    <div class="col-3 text-center" id="contenedor-tabla-creditos-vencidos">
                        <input type="text" id="token-autorizar-credito" placeholder="0000" class="form-control">
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Procesar venta',
        preConfirm: function(){
            token = $("#token-autorizar-credito").val();
            if(token.length ==0 || token=='' || token==null || token== undefined){

                return Swal.showValidationMessage(`
                Ingrese un token porvafor
                `);
            }else{
                //nuevoToken = Math.floor((Math.random() * (9999 - 1000) + 1000)); // Eliminar `0.`
                const nuevoToken = generarCodigoAlfanumerico();
                console.log(nuevoToken);
                return $.ajax({
                type: "post",
                url: "./modelo/token.php",
                data: {"comprobar-token" : token, "nuevo-token": nuevoToken, 'tipo-token' : 2},
                dataType: "json",
                success: function (response) {

                    if (response == 3) {
                        Swal.fire({
                            title: 'Token correcto',
                            html: "<span>Ahora puedes vender a credito a este cliente en esta venta</br></span>",   
                            icon: "success",
                            cancelButtonColor: '#00e059',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar', 
                            cancelButtonColor:'#ff764d',
                            showDenyButton: false,
                            denyButtonText: 'Reporte',
                            allowOutsideClick: false,
                            confirmButtonText: 'Proceder'

                        }).then(function(response) {
                            realizarVentaCredito();
                        });

                        }else{
                            return Swal.showValidationMessage(`
                                Token incorrecto, intenta de nuevo
                                `);
                    }
                }
            }); 
            }
        }
    })
}

function avisoCreditoVencido(nombre_cliente){ 
    Swal.fire({
        icon: 'warning',
        width: '800px',
        html:`
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <img class="mb-3" src="src/img/sad.png" style="width:80px;">
                        <h5>El cliente: ${nombre_cliente} tiene un credito vencido</h5>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-6 text-center">
                        <div class="btn btn-info" onclick="verCreditosVencidos()">Ver creditos vencidos</div>
                    </div>
                </div>
                <div class="row mb-2 mt-3 justify-content-center">
                    <div class="col-12 text-center" id="contenedor-tabla-creditos-vencidos">
                        
                    </div>
                </div>
                <small style="color:gray;">Ingrese el token para autorizar el credito</small>
                <div class="row mb-3 mt-3 justify-content-center">
                    <div class="col-3 text-center" id="contenedor-tabla-creditos-vencidos">
                        <input type="text" id="token-autorizar-credito" placeholder="0000" class="form-control">
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'Procesar venta',
        preConfirm: function(){
            token = $("#token-autorizar-credito").val();
            if(token.length ==0 || token=='' || token==null || token== undefined){
                return Swal.showValidationMessage(`
                Ingrese un token porvafor
                `);
            }else{
                //nuevoToken = Math.floor((Math.random() * (9999 - 1000) + 1000)); // Eliminar `0.`
                const nuevoToken = generarCodigoAlfanumerico();
                   
                return $.ajax({
                type: "post",
                url: "./modelo/token.php",
                data: {"comprobar-token" : token, "nuevo-token": nuevoToken, 'tipo_token' : 2},
                dataType: "json",
                success: function (response) {

                    if (response == 3) {
                        Swal.fire({
                            title: 'Token correcto',
                            html: "<span>Ahora puedes vender a credito a este cliente en esta venta</br></span>",   
                            icon: "success",
                            cancelButtonColor: '#00e059',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar', 
                            cancelButtonColor:'#ff764d',
                            showDenyButton: false,
                            denyButtonText: 'Reporte',
                            allowOutsideClick: false,
                            confirmButtonText: 'Proceder'

                        }).then(function(response) {
                            realizarVentaCredito();
                        });
                          

                        }else{
                            return Swal.showValidationMessage(`
                                Token incorrecto, intenta de nuevo
                                `);
                    }
                }
            }); 
            }
        }
    })
}

function verCreditosVencidos(){
    let id_cliente = $("#select2-clientes-container").attr("id-cliente");
    let contenedor = $("#contenedor-tabla-creditos-vencidos");
    contenedor.empty().append(`
        <img src="src/img/preload.gif" style="width:100px;">
    `);
    setTimeout(function(){
        $.ajax({
            type: "post",
            url: "./modelo/creditos/traer-creditos-vencidos.php",
            data: {"id_cliente": id_cliente},
            dataType: "json",
            success: function (response) {
                console.log(response);
                    contenedor.empty();
                    contenedor.append(`
                        <table class="table table-responsive text-center" style="font-size:14px; margin:auto;">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha apertura</th>
                                    <th>Fecha vencimiento</th>
                                    <th>Monto</th>
                                    <th>Pagado</th>
                                    <th>Restante</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-creditos-vencidos"></tbody>
                        </table>
                    `);
                    let tbody_cred = $("#tbody-creditos-vencidos");
                    response.forEach(element => {
                        tbody_cred.append(`
                        <tr>
                                    <th>${element.id_cred}</th>
                                    <th>${element.fecha_inicio}</th>
                                    <th>${element.fecha_final}</th>
                                    <th>${element.total}</th>
                                    <th>${element.pagado}</th>
                                    <th>${element.restante}</th>
                                    <th><button onclick="pdfCredito(${element.id_venta})" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span></th>
                        </tr>
                    `)
                    });
                    
            }
        });
    },1500);
   
    
}

function pdfCredito(id) {
    window.open(
      "./modelo/creditos/generar-reporte-credito.php?id=" + id,
      "_blank"
    );
  }

function realizarVentaCredito(){
    let metodos_pagos = $("#metodos-pago").val();  

    if ( !table.data().any()){

        toastr.warning('La tabla no tiene productos', 'Sin productos' ); 
    
    }else if(metodos_pagos.length == 0){
            toastr.warning('Agrega un metodo de pago', 'Sin metodo pago' ); 
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
                        5: "Deposito",
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
                      $("#plazo").val(1)
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
                    '<option value="6">1 día</option>'+
                    '<option value="1">1 Semana</option>'+
                    '<option value="2">15 días</option>'+
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
                                    5: "Deposito",
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

function generarCodigoAlfanumerico() {
    // Crear un conjunto de caracteres permitidos (letras y números)
    const caracteresPermitidos = 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
  
    let codigo = '';
    for (let i = 0; i < 5; i++) {
      // Elegir un carácter aleatorio del conjunto
      const caracterAleatorio = caracteresPermitidos.charAt(Math.floor(Math.random() * caracteresPermitidos.length));
  
      // Agregar el carácter al código
      codigo += caracterAleatorio;
    }
  
    return codigo;
  }



