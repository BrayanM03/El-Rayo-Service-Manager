$.ajax({
    type: "POST",
    url: "./modelo/ventas/borrar-producto-temp.php",
    data: {"reinicio":"reinicio"}, 
   
    success: function (response) {
        var validarSuc = document.getElementById("sucursal");
        validarSuc.setAttribute("rol", response);
        var atributoRol =validarSuc.getAttribute("rol");
        if(atributoRol == 1){
            validarSuc.removeAttribute("disabled");
            validarSuc.setAttribute("enabled", "");
            $("#sucursal").addClass("cursor-pointer");
        }else{
            validarSuc.removeAttribute("disabled");
            validarSuc.setAttribute("disabled", "");
            $("#sucursal").addClass("cursor-not-allowed");
            
        }
    }
});


const inputs = document.querySelectorAll(".input-group");
const selects = document.querySelectorAll(".select-group");


inputs.forEach( input => {  

    input.onfocus = function(){
        input.previousElementSibling.classList.add('top');
        input.previousElementSibling.classList.add('focus');
        input.parentNode.classList.add('focus');  
    }


    input.onblur = function(){
        input.value = input.value.trim();
        if (input.value.trim().length == 0) {
            input.previousElementSibling.classList.remove('top');
        }
        
        input.previousElementSibling.classList.remove('focus');
        input.parentNode.classList.remove('focus');
    }
});


selects.forEach( select => {

    select.onfocus = function(){
        select.previousElementSibling.classList.add('top');
        select.previousElementSibling.classList.add('focus');
        select.parentNode.classList.add('focus');  
    }


    select.onblur = function(){
        
      var indice = select.selectedIndex;
      
        if (indice == 0) {
            select.previousElementSibling.classList.remove('top');
            select.previousElementSibling.classList.remove('focus');
            select.parentNode.classList.remove('focus');
        }else{
            select.previousElementSibling.classList.remove('focus');
        select.parentNode.classList.remove('focus');
       
        }
    }
        
        
});


   


  
    function buscar() {
        
        $('#search').select2({
            placeholder: "Selecciona una llanta",
            theme: "bootstrap",
            minimumInputLength: 0,
            ajax: {
                url: "./modelo/ventas/buscar-llantas-nueva-venta.php",
                type: "post",
                dataType: 'json',
                delay: 250,
    
                data: function (params) {
                
                 if(params.term == undefined){
                  params.term = "";
                }

                 return {
                   
                   searchTerm: params.term // search term
                   
                 };
                },
                processResults: function (data) {
                    return {
                       results: data
                    };
                  },
               
                cache: true
    
            },
            language:  {
    
                inputTooShort: function () {
                    return "Busca la llanta...";
                  },
                  
                noResults: function() {
            
                  return "Sin resultados";        
                },
                searching: function() {
            
                  return "Buscando..";
                }
              },
    
              templateResult: formatRepo,
              templateSelection: formatRepoSelection
        });
    
    
        function formatRepo (repo) {
            
          if (repo.loading) {
            return repo.text;
          }
          
            var $container = $(
                "<div style='' class='select2-result-repository clearfix' desc='"+repo.Descripcion+" marca='"+repo.Marca +
                "' costo='"+repo.precio_Inicial +" id='tyre"+repo.id+"' precio='"+repo.precio_Venta+" idcode='"+repo.id+"'>" +
                "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
                "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.Marca + ".jpg' /></div>" +
                  "<div class='col-md-10 select2-contenedor'>" +
                  "<div class='select2_modelo' style='font-size:14px;'>Modelo: "+ repo.Modelo +"</div>" +
                  "<div class='select2_description' style='font-size:14px;'>" + repo.Descripcion + "</div>" +

                  "<span style='font-size:14px; margin-left:80%;'><strong>"+ repo.Codigo +"</strong></span>"+
                  "<div class='select2_precio_venta' style='margin-left:65%;''><i class='fa fa-store'></i> "+ repo.Sucursal +"</div>" + 
                  "</div>" +
                  "</div>" +
                  "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
                  "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.Marca+"</div>" +
                    "<div class='select2_precio_venta'><i class='fa fa-dollar-sign'></i> "+ repo.precio_Venta +" (precio)</div>" + 
                    "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio_Mayoreo +" (al mayoreo)</div>" +
                    "<div class='select2_precio_venta'><i class='fa fa-bullseye'></i> "+ repo.Stock +"</div>" +
                  "</div>" +
                "</div>" +
              "</div>"
            );
       
          
            return $container;
          }
    
          function formatRepoSelection (repo) {
            //A partir de aqui puedes agregar las llantas Brayan
           // ruta = "./src/img/logos/" + repo.marca + ".jpg";
         
           
           if(repo.Stock <= 0){

             Swal.fire({
                title: 'Ya no quedan llantas',
                html: "<span>La llanta: </br>"+
                "Codigo: <strong>"+ repo.Codigo +"</strong>"+
                " Marca: <strong>"+ repo.Marca +"</strong></br>"+
                "Descripcion: <strong>"+ repo.Descripcion +"</strong></br>"+
                "Se agoto del inventario, contacta a un administrador para que modifique el inventario</span>"+
                "<img src='./src/img/sad.png' style='width:80px; margin:15px auto 8px auto;'>",
                icon: "warning",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d'
            });
            return repo.text;
           
               
           }else{

            $("#agregar-producto").attr("idcode", repo.id);
            $("#agregar-producto").attr("descripcion", repo.Descripcion);
            $("#agregar-producto").attr("modelo", repo.Modelo);
            $("#agregar-producto").attr("marca", repo.Marca);
            $("#agregar-producto").attr("precio", repo.precio_Venta);
            $("#agregar-producto").attr("codigo", repo.Codigo);
            $("#agregar-producto").attr("stock", repo.Stock);

            $("#modelo").attr("modelo", repo.Modelo);
            compr = $("#modelo").attr("modelo");

            if(compr !== ""){
                $("#description").focus().val(repo.Descripcion);
                $("#modelo").focus().val(repo.Modelo);
                $("#precio").focus().val(repo.precio_Venta);
                $("#tyre"+repo.id).on("click", function () { 
                    alert("Hola");
                 });
                
                if(repo.Sucursal == "Sendero"){

                    select = $("#sucursal");
                    select.focus().val(1).blur();
    
                }else{
                
                    select = $("#sucursal");
                    select.focus().val(0).blur();
                }

                var cuadro = document.getElementsByClassName("logo-marca-grande")[0];
                                
                cuadro.style.backgroundImage = "url('src/img/logos/"+ repo.Marca +".jpg')";

                $("#modelo").blur();
                $("#description").blur();
                $("#precio").blur();

                return repo.text || repo.Descripcion;

            }
           }
            
          
           return repo.text
    
          
          }

      }

      buscar();

     



      function realizarVenta(){


        
        if ( !table.data().any()){

            toastr.warning('La tabla no tiene productos', 'Sin productos' ); 

        }else{
            

            llantaData = $("#pre-venta").dataTable().fnGetData();
            console.log(llantaData);
                
              
            total = $("#total").val();
            fecha = $("#fecha").val(); 
            cliente = $("#select2-clientes-container").attr("id-cliente");
            metodo_pago = $("#metodos-pago").val();  
            tienda = $("#sucursal").val();
            comentario = $("#hacer-comentario").attr("comentario");
            
            //Enviando data
            
            $.ajax({
                type: "POST",
                url: "./modelo/ventas/insertar-venta.php", 
                data: {'data': llantaData,
                       'cliente': cliente,
                       'metodo_pago': metodo_pago,
                       'fecha': fecha,
                       'sucursal': tienda,
                       'total': total,
                       'comentario': comentario,
                       'tipo': 'vt-normal'},
                dataType: "JSON",
                success: function (response) {
                    console.log(response);
                    if (response) {
                        Swal.fire({
                            title: 'Venta realizada',
                            html: "<span>La venta se realizo con exito</br></span>"+
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
    
                                window.open('./modelo/ventas/generar-reporte-venta.php?id='+ response, '_blank');
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
            }); 

            
            
            


        }
        //$(".tbody").empty();
      }


      $(document).on("click",function(e) {

        var container = $("#table-llantas-mostradas");

            if (!container.is(e.target) && container.has(e.target).length === 0) { 
                contenedorLista = $(".contenedor-tabla");
                contenedorLista.addClass("oculto");

            }
   });


   $(document).ready(function() {

    $("#clientes").select2({
        placeholder: "Clientes",
        theme: "bootstrap",
        ajax: {
            url: "./modelo/ventas/traer_clientes.php",
            type: "post",
            dataType: 'json',
            delay: 250,

            data: function (params) {
             return {
               searchTerm: params.term // search term
               
             };
            },
            processResults: function (data) {
                return {
                   results: data
                }; 
              },
           
            cache: true

        },
        language:  {

            inputTooShort: function () {
                return "Busca un cliente...";
              },
              
            noResults: function() {
        
              return "Sin resultados";        
            },
            searching: function() {
        
              return "Buscando..";
            }
          },

          templateResult: formatResultClientes,
          templateSelection: formatSelection

    });

    function formatResultClientes(repo){


        if (repo.loading) {
            return repo.text;
          }
          
          if (repo.credito == 0) {
              cred = "Sin credito"
              badge="badge-info";
          }else if (repo.credito == 1){
              cred= "Con credito";
              badge = "badge-warning";
          }

            var $container = $(
                "<span id='"+repo.id+"'>"+ repo.nombre +" <span class='badge " + badge +"'>"+ cred +"</span></span>"
            );
          
           
            //
          
            return $container;

    }

    function formatSelection (repo) {
        //A partir de aqui puedes agregar los clientes
        
        $("#select2-clientes-container").attr("id-cliente", repo.id);
     
       

        return repo.text || repo.nombre;
      }

//Select2 para los metodos de pago:

    $("#metodos-pago").select2({
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


    
});


//Helps

$("#btn-add-client").hover(function() { 

    $("#help-addclient-span").css("display", "block");
    $("#help-addclient-span").css("position", "fixed");
   // $("#help-addclient-span").css("overflow", "hidden");

 },function(){
    $("#help-addclient-span").css("display", "none");
    });


    $("#btn-change-servicios").hover(function() { 

        $("#help-changeservice-span").css("display", "block");
        $("#help-changeservice-span").css("position", "fixed");
       // $("#help-addclient-span").css("overflow", "hidden");
    
     },function(){
        $("#help-changeservice-span").css("display", "none");
        });

//Alternar entre servicios y productos
    function changeServicios(){

        flag =$("#btn-change-servicios").attr("flag");

        if(flag == "0"){
            $("#title-help-card").empty();
            $("#title-help-card").append("Modo neumaticos");
            $("#body-help-card").empty();
            $("#body-help-card").append("Pulsa este boton para cambiar a modo venta de neumaticos");
            $("#texto-modo-venta").empty();
            $("#texto-modo-venta").append("Modo de venta: <span style='color: #cc0000; text-shadow:#ff4040 3px 0 10px;'>Servicios</span>");
            $("#btn-change-servicios").empty();
            $("#btn-change-servicios").append("<i class='fas fa-car'></i>");
            $("#select-search-contain").empty();
            $("#select-search-contain").append("<select id='changes' style='margin-bottom: 15px;' name='clientes' class='form-control'></select>");
            $("#btn-change-servicios").attr("flag", "1");

            $('#changes').select2({
                placeholder: "Selecciona una servicio",
                theme: "bootstrap",
                minimumInputLength: 1,
                ajax: {
                    url: "./modelo/ventas/buscar-nuevo-servicio.php",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
        
                    data: function (params) {
                     return {
                       searchTerm: params.term // search term
                       
                     };
                    },
                    processResults: function (data) {
                        return {
                           results: data
                        };
                      },
                   
                    cache: true
        
                },
                language:  {
        
                    inputTooShort: function () {
                        return "Busca un servicio...";
                      },
                      
                    noResults: function() {
                
                      return "Sin resultados";        
                    },
                    searching: function() {
                
                      return "Buscando..";
                    }
                  },
        
                  templateResult: formatRepoS,
                  templateSelection: formatRepoSelectionS
            });
        
        
            function formatRepoS (repo) {
                
              if (repo.loading) {
                return repo.text;
              }
              
                var $container = $(
                    "<div style='' class='select2-result-repository clearfix' desc='"+repo.descripcion+" marca='"+repo.imagen +
                    "' id='service"+repo.id+"' precio='"+repo.precio+" idcode='"+repo.id+"'>" +
                    "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
                    "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/services/" + repo.imagen + ".jpg' /></div>" +
                      "<div class='col-md-10 select2-contenedor'>" +
                      "<div class='select2_description' style='font-size:14px;'>" + repo.descripcion + "</div>" +
    
                      "</div>" +
                      "</div>" +
                      "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
                      
                        "<div class='select2_precio_venta'><i class='fa fa-dollar-sign'></i> "+ repo.precio +" (precio)</div>" + 
                      "</div>" +
                    "</div>" +
                  "</div>"
                );
           
              
                return $container;
              }
        
              function formatRepoSelectionS (repo) {
                //A partir de aqui puedes agregar las llantas Brayan
               // ruta = "./src/img/logos/" + repo.marca + ".jpg";
             
    
                $("#agregar-producto").attr("idcode", repo.id);
                $("#agregar-producto").attr("descripcion", repo.descripcion);
                $("#agregar-producto").attr("modelo", "N/A");
                $("#agregar-producto").attr("marca", repo.marca);
                $("#agregar-producto").attr("precio", repo.precio);
                $("#agregar-producto").attr("codigo", "SERV" + repo.id);
    
                $("#modelo").attr("modelo", repo.Modelo);
                
    
                
                    $("#description").focus().val(repo.descripcion);
                    $("#modelo").focus().val("no aplica");
                    $("#precio").focus().val(repo.precio);
                    select = $("#sucursal");
                    sucu = $("#agregar-producto").attr("sucursal");
                    console.log(sucu);
                    if(sucu =="Pedro"){
                        select.focus().val(0).blur();
                    }else if(sucu =="Sendero"){
                        select.focus().val(1).blur();
                    }
                   
                    
                
     
                    var cuadro = document.getElementsByClassName("logo-marca-grande")[0];
                                    
                    cuadro.style.backgroundImage = "url('src/img/services/"+ repo.imagen +".jpg')";
    
                    $("#modelo").blur();
                    $("#description").blur();
                    $("#precio").blur();
    
                    return repo.text || repo.descripcion;
        
              
              }
        }else if(flag == "1"){
            $("#title-help-card").empty();
            $("#title-help-card").append("Modo servicios");
            $("#body-help-card").empty();
            $("#body-help-card").append("Pulsa este boton para cambiar a modo venta de servicios");
            $("#texto-modo-venta").empty();
            $("#texto-modo-venta").append("Modo de venta: <span style='color: green; text-shadow:#00a000 3px 0 10px;'>Neumaticos</span>")
            $("#btn-change-servicios").empty();
            $("#btn-change-servicios").append("<i class='fas fa-dot-circle'></i>");
            $("#select-search-contain").empty();
            $("#select-search-contain").append("<select id='search' style='margin-bottom: 15px;' class='form-control'></select>");
            $("#btn-change-servicios").attr("flag", "0");
            
            $('#search').select2({
                placeholder: "Selecciona una llanta",
                theme: "bootstrap",
                minimumInputLength: 1,
                ajax: {
                    url: "./modelo/ventas/buscar-llantas-nueva-venta.php",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
        
                    data: function (params) {
                     return {
                       searchTerm: params.term // search term
                       
                     };
                    },
                    processResults: function (data) {
                        return {
                           results: data
                        };
                      },
                   
                    cache: true
        
                },
                language:  {
        
                    inputTooShort: function () {
                        return "Busca la llanta...";
                      },
                      
                    noResults: function() {
                
                      return "Sin resultados";        
                    },
                    searching: function() {
                
                      return "Buscando..";
                    }
                  },
        
                  templateResult: formatRepo,
                  templateSelection: formatRepoSelection
            });
        
        
            function formatRepo (repo) {
                
              if (repo.loading) {
                return repo.text;
              }
              
                var $container = $(
                    "<div style='' class='select2-result-repository clearfix' desc='"+repo.Descripcion+" marca='"+repo.Marca +
                    "' costo='"+repo.precio_Inicial +" id='tyre"+repo.id+"' precio='"+repo.precio_Venta+" idcode='"+repo.id+"'>" +
                    "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
                    "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.Marca + ".jpg' /></div>" +
                      "<div class='col-md-10 select2-contenedor'>" +
                      "<div class='select2_modelo' style='font-size:14px;'>Modelo: "+ repo.Modelo +"</div>" +
                      "<div class='select2_description' style='font-size:14px;'>" + repo.Descripcion + "</div>" +
    
                      "<span style='font-size:14px; margin-left:80%;'><strong>"+ repo.Codigo +"</strong></span>"+
                      "<div class='select2_precio_venta' style='margin-left:65%;''><i class='fa fa-store'></i> "+ repo.Sucursal +"</div>" + 
                      "</div>" +
                      "</div>" +
                      "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
                      "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.Marca+"</div>" +
                        "<div class='select2_precio_venta'><i class='fa fa-dollar-sign'></i> "+ repo.precio_Venta +" (precio)</div>" + 
                        "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio_Mayoreo +" (al mayoreo)</div>" +
                        "<div class='select2_precio_venta'><i class='fa fa-bullseye'></i> "+ repo.Stock +"</div>" +
                      "</div>" +
                    "</div>" +
                  "</div>"
                );
           
              
                return $container;
              }
        
              function formatRepoSelection (repo) {
                //A partir de aqui puedes agregar las llantas Brayan
               // ruta = "./src/img/logos/" + repo.marca + ".jpg";
             
               console.log(repo.Stock);
               if(repo.Stock <= 0){
    
                 Swal.fire({
                    title: 'Ya no quedan llantas',
                    html: "<span>La llanta: </br>"+
                    "Codigo: <strong>"+ repo.Codigo +"</strong>"+
                    " Marca: <strong>"+ repo.Marca +"</strong></br>"+
                    "Descripcion: <strong>"+ repo.Descripcion +"</strong></br>"+
                    "Se agoto del inventario, contacta a un administrador para que modifique el inventario</span>"+
                    "<img src='./src/img/sad.png' style='width:80px; margin:15px auto 8px auto;'>",
                    icon: "warning",
                    cancelButtonColor: '#00e059',
                    showConfirmButton: true,
                    confirmButtonText: 'Aceptar', 
                    cancelButtonColor:'#ff764d'
                });
                return repo.text;
               
                   
               }else{
    
                $("#agregar-producto").attr("idcode", repo.id);
                $("#agregar-producto").attr("descripcion", repo.Descripcion);
                $("#agregar-producto").attr("modelo", repo.Modelo);
                $("#agregar-producto").attr("marca", repo.Marca);
                $("#agregar-producto").attr("precio", repo.precio_Venta);
                $("#agregar-producto").attr("codigo", repo.Codigo);
    
                $("#modelo").attr("modelo", repo.Modelo);
                compr = $("#modelo").attr("modelo");
    
                if(compr !== ""){
                    $("#description").focus().val(repo.Descripcion);
                    $("#modelo").focus().val(repo.Modelo);
                    $("#precio").focus().val(repo.precio_Venta);
                    $("#tyre"+repo.id).on("click", function () { 
                        alert("Hola");
                     });
                    
                    if(repo.Sucursal == "Sendero"){
    
                        select = $("#sucursal");
                        select.focus().val(1).blur();
        
                    }else{
                    
                        select = $("#sucursal");
                        select.focus().val(0).blur();
                    }
    
                    var cuadro = document.getElementsByClassName("logo-marca-grande")[0];
                                    
                    cuadro.style.backgroundImage = "url('src/img/logos/"+ repo.Marca +".jpg')";
    
                    $("#modelo").blur();
                    $("#description").blur();
                    $("#precio").blur();
    
                    return repo.text || repo.Descripcion;
    
                }
               }
                
              
               return repo.text
        
              
              }
    
          }
        
       


    }    


    function agregarcliente(){
        Swal.fire({
            title: "Agregar cliente",
            showCancelButton: true,
                cancelButtonText: 'Cerrar',
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Agregar', 
                cancelButtonColor:'#ff764d',
                focusConfirm: false,
                iconColor : "#36b9cc",
            html: '<form class="mt-4" id="agregar-cliente-form">'+
            
            '<div class="row">'+
    
               '<div class="col-12">'+
               '<div class="form-group">'+
               '<label><b>Nombre:</b></label></br>'+
               '<input class="form-control" type="text" id="nombre-cliente" name="nombre" placeholder="Nombre completo">'+
                  '</div>'+
                  '</div>'+
               '</div>'+
        
            '<div class="row">'+
                '<div class="col-6">'+
                '<div class="form-group">'+
                '<label><b>Credito</b></label>'+
                '<select class="form-control" id="credito" name="credito">'+
                '<option value="0">Sin credito </option>'+
                '<option value="1">Con credito </option>'+
                '</select>'+
              
        
        
           ' </div>'+
            '</div>'+
            
            
           '<div class="col-6">'+
            '<div class="form-group">'+
            '<label for="telefono"><b>Telefono:</b></label></br>'+
            '<input type="number" class="form-control" id="telefono" name="telefono" placeholder="Telefono" autocomplete="off">'+
            '</div>'+
            '</div>'+
        
            
                '<div class="col-7">'+
                '<div class="form-group">'+
                 
                '<label><b>Correo:</b></label></br>'+
                  '<input type="text" name="correo" id="correo" class="form-control" placeholder="Correo">'+
            '</div>'+
                '</div>'+
        
               
        
                '<div class="col-5">'+
                '<div class="form-group">'+
                '<label><b>RFC</b></label>'+
                '<input type="text" class="form-control" id="rfc" name="rfc" placeholder="RFC">'+
                '</div>'+
                '</div>'+
        
               
            '<div class="col-12">'+
                '<div class="form-group">'+
                    '<label><b>Dirección</b></label>'+
                    '<textarea type="text" class="form-control" name="direccion" id="direccion" placeholder="Escribe la dirección del cliente">'+
                    '</textarea>'+
                '</div>'+
            '</div>'+
            '<div class="col-12">'+
                '<div class="form-group">'+
                    '<label><b>Mapear dirección</b></label>'+
                    '<div id="map-id"></div>'+
                    '<div class="alert alert-info coordenadas-agregar" id="label-coord"><strong>Cordenadas del marcador:</strong>'+
                    ' </br>Latitud: <span id="lat-agregar"></span> </br>longitud: <span id="long-agregar"></span></div>'+
                   // "<img id='marker' class='marker' src='./src/img/marker.svg' alt='insertar SVG con la etiqueta image'>"+
                    
                '</div>'+
            '</div>'+
        
            '</div>'+
        
    
    
                '<div>'+
        '</form>',
    
        }).then((result) =>{
            //Agregando cliente
            if(result.isConfirmed){
    
                nombre = $("#nombre-cliente").val();
                credito = $("#credito").val();
                telefono = $("#telefono").val();
                correo = $("#correo").val()
                rfc = $("#rfc").val();
                direccion = $("#direccion").val();
                latitud = $("#lat-agregar").text();
                longitud = $("#long-agregar").text();
    
                
                $.ajax({
                    type: "POST",
                    url: "./modelo/clientes/agregar-cliente.php",
                    data: {
                        "nombre": nombre,
                        "credito": credito,
                        "telefono": telefono,
                        "correo": correo,
                        "rfc": rfc,
                        "direccion": direccion,
                        "latitud": latitud,
                        "longitud": longitud},
                    
                    success: function (response) {
                       if (response == 1) {
                        Swal.fire(
                            "¡Registrado!",
                            "Se agrego el cliente correctamente",
                            "success"
                            ).then((result) => { 
                                if(result.isConfirmed){
                                  
                                }
                               
                                });
                       }else if(response == 0){
                        Swal.fire(
                            "¡Error!",
                            "No se puede agregar el cliente",
                            "error"
                            )
                       }
                    }
                });
    
            }
        });
    
    //Codigo que genera mapa
    mapboxgl.accessToken = 'pk.eyJ1IjoiYnJheWFubTAzIiwiYSI6ImNrbnRlNTdyZzAxcXcycG84ZnRvNnJtdmoifQ.8k-_U2-Eq-CmSrH6jm8KEg';
    
    var mymap = new mapboxgl.Map({
        container: 'map-id',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-97.51302566191197,25.860074578125104],
        zoom: 13
        }); 
    
    var nav = new mapboxgl.NavigationControl();   
    
    mymap.addControl(
      new MapboxGeocoder({
          accessToken: mapboxgl.accessToken,
          mapboxgl: mapboxgl,
      })
    );
    
    mymap.addControl(nav,"top-left");
    mymap.addControl(new mapboxgl.FullscreenControl());
    
    mymap.addControl(new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true
        },
        trackUserLocation: true
    }));
    
    mymap.on('click', function (e) {
    
        $("#marker").remove();  
            latitud = JSON.stringify(e.lngLat);
            console.log(latitud);
    
            var el = document.createElement("img");
            el.id = "marker";
            el.src = "./src/img/marker.svg";
    
          
            // make a marker for each feature and add it to the map
            marker = new mapboxgl.Marker({
                element: el,
                draggable: true
            }).setLngLat(e.lngLat).addTo(mymap);
              /*.setPopup(
                new mapboxgl.Popup({ offset: 25 }) // add popups
                  .setHTML(
                    '<h3>' +
                      marker.properties.title +
                      '</h3><p>' +
                      marker.properties.description +
                      '</p>'
                  )
              )*/
    
              $("#lat-agregar").text(e.lngLat.lat);
              $("#long-agregar").text(e.lngLat.lng);
              
    
    });
        
    };  

   /*  function comentario(){
     
    }
 */

    $("#hacer-comentario").on("click", function () { 
      
      Swal.fire({
        title: "Comentario",
        showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Agregar', 
            cancelButtonColor:'#ff764d',
            focusConfirm: false,
            iconColor : "#36b9cc",
            html:'<div class="m-auto"><label>Agregar un comentario:</label><br><textarea id="comentario" name="motivo" placeholder="Escribe un comentario sobre la venta..." class="form-control m-auto" style="width:300px;height:80px;" ></textarea></div>',
            }).then((result) => { 

             let comentario = $("#comentario").val();
             $("#hacer-comentario").attr("comentario", comentario);
             console.log(comentario);

            });
     })