$(document).ready(function() {

    $.fn.dataTable.ext.errMode = 'none';
    let tipo_cotizacion = $("#btn-agregar").attr("cotizacion");
    let id_usuario = $("#emp-title").attr('sesion_id');
    id_sucursal_destino = $("#emp-title").attr('sesion_sucursal_id');
    depurarTabla()
    function depurarTabla(){
      $.ajax({
        type: "POST",
        url: "./modelo/cambios/depurar-tabla.php",
        data: {"id_usuario": id_usuario},
        success: function (response) {}
          
      });
    }
   
    
    table = $('#pre-requisicion').DataTable({
        
        destroy: true,
        serverSide: true,
        processing: false,
        ajax: {
            method: "POST",
            url: "./modelo/requerimientos/traer-req-temp.php",
            dataType: "json",
            data: {'id_usuario': id_usuario},
            error: function(){  // error handling
              numRows = table.column( 0 ).data().length;
         
          if (numRows == 0) {
            $(".pre-cotizacion-error").html("");
            $('#pre-requisicion > tbody').empty();
            $("#pre-requisicion tbody").append('<tr><th id="empty-table" style="text-align: center;" colspan="8">Preventa vacia</th></tr>');
            $("#pre-requisicion_processing").css("display","none");
          }
            }
    
        },  
    
      columns: [   
        { title: "#",               data: null             },
        { title: "Descripcion",     data: "descripcion"    },
        { title: "Marca",           data: "marca"          },
        { title: "Cantidad",        data: "cantidad"       },
        { title: "Ubicación",       data: "sucursal_remitente"      },
        { title: "Destino",         data: "sucursal_destino"        },
        { title: "Acción",
          data: null,
          className: "celda-acciones",
          render: function (row, data) {
            return '<div style="display:flex; justify-content: center; align-items:center;">'+
            '<span class="hidden-xs"></span></button><br><button type="button" rowid="'+ row.id +'" class="borrar-articulo btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>'+
            '</div>';
          },
        },
      ],
    
      paging: false,
      searching: false,
      //scrollY: "auto",
      info: false,
      responsive: true,
      order: [0, "desc"],
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
      }
    });
    
    $("table.dataTable tbody").addClass("table-light");

      table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
           
        } );
    });
    
    
    
    table.on('click', '.borrar-articulo', function() {
      
      let $tr = $(this).closest('tr');
      let id = $(this).attr("rowid");
    
      $.ajax({
        type: "POST",
        url: "./modelo/cambios/eliminar-llanta.php",
        data: {"id_cambio":id},
        dataType: "json",
        success: function(response) {
          if(response.estatus == 'success'){
           /*  $.ajax({
              type: "POST",
              url: "./modelo/cambios/traer-importe-cotizacion.php",
              data: {'tipo_cotizacion': tipo_cotizacion},
              success: function (response) {
                $("#total-cotizacion").val(response);
                
                toastr.success('Producto borrado', 'Listo');
              }
            }); */
              //tabla_presalida.ajax.reload(null, false);
              // Le pedimos al DataTable que borre la fila
              table.row(this).remove().draw();
    
              
          toastr.success('Producto borrado con exito', 'Correcto' );
         
          }else{
            toastr.warning('Hubo un error al borrar el producto', 'Error' );
          }
    
        }
    
      });
    
    
    });
    
    
    }); 
    
    
    function agregarProducto() { 
    
      tyre_amount = $("#cantidad").val();
      tyre_description = $("#btn-agregar").attr("descripcion");
      let sucursal_remitente = $("#sucursal-ubicacion").val();
       /*  tyre_precio = $("#precio").val();
        modelo = $("#btn-agregar").attr("modelo");
        tyre_import = parseFloat(tyre_precio) * tyre_amount;
     */
        if(tyre_amount <= 0 ){
            toastr.warning('La cantidad no puede estar vacia, ser 0 o negativo', 'Alerta');
        }else if(tyre_description == null){
    
          toastr.warning('Selecciona una llanta', 'Alerta');
        }else{
            let id_llanta = $("#btn-agregar").attr("id_llanta");
    
            $.ajax({
                type: "POST",
                url: "./modelo/requerimientos/agregar-req-temp.php",
                data: {'id_llanta':id_llanta, 'sucursal_remitente': sucursal_remitente, 'sucursal_destino': id_sucursal_destino, 'cantidad': tyre_amount},
                dataType:'json',
                success: function (response) {
                  if (response.estatus =='success') {
                    table.ajax.reload(null,false);
                    $("#total-llantas").val(response.total_llantas)
                    toastr.success(response.mensaje, '¡Exito!');
                   /*  $.ajax({
                      type: "POST",
                      url: "./modelo/cotizaciones/traer-importe-cotizacion.php",
                      data: {'tipo_cotizacion': tipo_cotizacion},
                      success: function (response) {
                        $("#total-cotizacion").val(response);
                        toastr.success('Producto agregado', 'Listo');
                      }
                    }); */
                  }else{
                    toastr.error(response.mensaje, 'Error');
                  }
                }
            });
        }
     }
    
    function reload() {
      table.ajax.reload(null,false);
      }

    
    inicializarSelect2()

    function formatRepo (repo) {
        
      if (repo.loading) {
        return repo.text;
      }
      
        var $container = $(
            "<div class='select2-result-repository clearfix' desc='"+repo.Descripcion+" marca='"+repo.Marca +
            " id='"+repo.Marca+" costo='"+repo.precio_Inicial +" id='tyre' precio='"+repo.precio_Venta+" idcode='"+repo.id+"'>" +
            "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
            "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.Marca + ".jpg' /></div>" +
              "<div class='col-md-10 select2-contenedor'>" +
              "<div class='select2_modelo'>Modelo "+ repo.Modelo +"</div>" +
              "<div class='select2_description'>" + repo.Descripcion + "</div>" +
              "<div class='select2_description'><i class='fas fa-fw fa-store'></i> " + repo.Sucursal + "</br> <b>Stock:</b> "+repo.Stock+"</div>" +
              "</div>" +
              "</div>" +
              "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
              "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.Marca+"</div>" +
                "<div class='select2_costo'><i class='fa fa-circle'</i> Tu stock actual: "+repo.stock_actual +"</div>" +
              "</div>" +
            "</div>" +
          "</div>"
        );
      
      /*  $container.find(".select2_modelo").text(repo.modelo);
        $container.find(".select2_description").text(repo.descripcion);
        $container.find(".select2_marca").append(repo.marca);
        $container.find(".select2_precio_venta").append(repo.precio);
        $container.find(".select2_costo").append(repo.costo);*/ 
        //

        return $container;
        
      }

    function formatRepoSelection (repo) {
        //A partir de aqui puedes agregar las llantas Brayan
       // ruta = "./src/img/logos/" + repo.marca + ".jpg";

        if(repo.id !== ""){

          $("#stock").prop("disabled", false);
        }
        $("#btn-agregar").attr("descripcion", repo.Descripcion);
        $("#btn-agregar").attr("id_llanta", repo.id_Llanta);
      /*
        $("#btn-agregar").attr("modelo", repo.modelo);
        $("#btn-agregar").attr("marca", repo.marca);
        $("#btn-agregar").attr("costo", repo.costo);
        $("#btn-agregar").attr("precio", repo.precio);
        $("#precio").val(repo.precio); */
       /* $("#ancho-agregado").text(repo.ancho);
        $("#alto-agregado").text(repo.alto);
        $("#rin-agregado").text(repo.rin);
        $("#modelo-agregado").text(repo.modelo);
        $("#marca-agregado").text(repo.marca);
        $(".logo-marca-agregada").attr("src", ruta);

        $("#costo-agregado").text(repo.costo);
        $("#precio-agregado").text(repo.precio);
        $("#mayoreo-agregado").text(repo.mayoreo);*/
        //$("#mayoreo-agregado").fadeIn(400)
       

        return repo.text || repo.Descripcion;
      }

    function resetearSelect2(){
      $("#btn-agregar").attr("id_llanta", '');
      $("#btn-agregar").attr("descripcion", '');
      $("#contenedor-llantas-buscador").empty().append(`
        <label for="busquedaLlantas" class="">Selecciona una llanta</label>
        <select style="width:100%" class="form-control" id="busquedaLlantas" value="" name="search"></select> 
        `)

      inicializarSelect2()
     } 

     function inicializarSelect2(){
      $('#busquedaLlantas').select2({
        placeholder: "La busqueda se realizará en el inventario de la sucursal seleccionada",
        theme: "bootstrap",
        minimumInputLength: 1,
        ajax: {
            url: "./modelo/requerimientos/busqueda-llanta-requerimiento.php",
            type: "post",
            dataType: 'json',
            delay: 250,

            data: function (params) {
              if(params.term == undefined){
                params.term = "";
              }
              let ide_sucursal = $("#sucursal-ubicacion").val()
              return {
                searchTerm: params.term, // search term
               id_sucursal: ide_sucursal,
               id_sucursal_destino: id_sucursal_destino,
               page: params.page || 1,
             };
            }
        },
        processResults: function (data) {
          return {
             results: data
          };
        },
     
      cache: true,
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
    })
     };

     function realizarComentario(){
          Swal.fire({
            title: 'Comentarios',
            html: `<div class="container">
                        <div class="row">
                              <div class="col-md-12">
                                  <textarea class="form-control" id="comentario-req" placeholder="Escribe un comentario" rows="5" cols="10"></textarea>
                              </div >
                        </div>
                   </div>
            `,
            didOpen: ()=>{
                let com = $("#hacer-comentario").attr('comentario')
                if(com.length>0){
                  $("#comentario-req").val(com);
                 palabra = 'actualizado'
                }else{
                  palabra = 'agregado'
                }
            },
            confirmButtonText: 'Agregar comentario',
            cancelButtonText: 'Cancelar',
            showCancelButton: true
          }).then(function(r){
            if(r.isConfirmed){
              let coment = $("#comentario-req").val();
              toastr.success('Comentario ' + palabra, 'Alerta');
                $("#hacer-comentario").attr('comentario', coment)
            }
          })
     }

     function realizarRequerimiento(){
      let comentario = $("#hacer-comentario").attr('comentario')

      $.ajax({
        type: "post",
        url: "./modelo/requerimientos/nuevo-requerimiento.php",
        data: {comentario},
        dataType: "JSON",
        success: function (response) {
          if(response.estatus){ 
              Swal.fire({
                icon: 'success',
                title: response.mensaje
              })
              depurarTabla()
          }else{
            Swal.fire({
              icon: 'error',
              title: response.mensaje
            })
          }
        }
      });
     }

    
     