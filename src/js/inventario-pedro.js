function MostrarInventarioPedro() { 
  
    table = $('#inventario-pedro').DataTable({
      
      
        ajax: {
            method: "POST",
            url: "./modelo/traerInventario.php"
        },  
  
      columns: [   
        { title: "#",              data: null             },
        { title: "Codigo",         data: "Codigo"         },
        { title: "Descripcion",    data: "Descripcion"    },
        { title: "Marca",          data: "Marca"          },
        { title: "Modelo",         data: "Modelo"         },
        { title: "Costo",          data: "precio_Inicial" },
        { title: "Precio",         data: "precio_Venta"   },
        { title: "Precio Mayoreo", data: "precio_Mayoreo" },
        { title: "Sucursal",       data: "Sucursal"       },
        { title: "Stock",          data: "Stock"          },
        { title: "Imagen",         data: "Marca", render: function(data,type,row) {
          return '<img src="./src/img/logos/'+ data +'.jpg" style="width: 60px; border-radius: 8px">';
          }},
        {
          data: null,
          className: "celda-acciones",
          render: function (row) { 
            console.log(row);
            return '<div style="display: flex"><button type="button" onclick="editarInvPedro('+row.id_Llanta+');" id="'+row.id_Llanta+'" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" onclick="borrarRegistro('+row.id_Llanta+');" id="'+row.id_Llanta+'" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
          },
        },
      ],
      paging: true,
      searching: true,
      scrollY: "300px",
      info: true,
      responsive: true,
      
      language: {
            
        emptyTable: "No hay registros",
        infoEmpty: "Ups!, no hay registros aun en esta categoria."
      }
      
    });// fin de la tabla


    //table.appendTo("#inventario-pedro_wrapper .col-md-6:eq(0)");

     //Enumerar las filas "index column"
    table.on( 'order.dt search.dt', function () {
      table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
          cell.innerHTML = i+1;
         
      } );
  } ).draw();


    
 }


 MostrarInventarioPedro();

/**/

function agregarLLanta() {

  Swal.fire({
    title: "Agregar llanta nueva",
    background: "#dcdcdc" ,
    html: '<form class="mt-4" id="formulario-editar-registro">'+

      '<div class="row">'+
          '<div class="col-8">'+
          '<div class="form-group">'+
          '<label><b>Buscar llanta:</b></label></br>'+
          '<select class="form-control" id="busquedaLlantas" value="" name="cate-input-modal">'+
          '</select>'+
          '</div>'+
          '</div>'+

          '<div class="col-4">'+
          '<div class="form-group">'+
          '<label for=""><b>Cantidad:</b></label></br>'+
          '<input type="number" class="form-control" id="cantidad" value="" name="cr-input-nuevaOrden" aria-describedby="emailHelp" placeholder="0" autocomplete="off">'+
          '</div>'+
          '</div>'+
       '</div>'+


       '<div class="row">'+
       
       '<div class=" inventario-pedro">'+

       '<div class="arriba">'+
       '<span class="tag"><b>Ancho: </b><span id="ancho-agregado">   </span></span>'+
       '<span class="tag"><b>Alto: </b><span id="alto-agregado">   </span></span>'+ 
       '<span class="tag"><b>Rin: </b><span id="rin-agregado">   </span></span>'+
       '</div>'+

       '<div class="abajo">'+
       '<div class="caja">'+
       '<span><b>Modelo: </b></span>'+
       '<span id="modelo-agregado"></span>'+
       '</div>'+

       '<div class="caja">'+
       '<span><b>Marca: </b></span>'+
       '<img class="logo-marca-agregada" src="./src/img/logos/Atlas.jpg">'+
       '<span id="marca-agregado"></span>'+
       '</div>'+
       '</div>'+

       '<div class="precios">'+
       '<span class="tag">Precio por unidad: </span>' +
       '<div class="precios-body">'+
       '<span class="tag">' +
       '<span><b>Costo: </b></span>'+
       '<span>$<span id="costo-agregado"></span></span></span>'+
       '<span class="tag">' +  
       '<span><b>Precio: </b></span>'+
       '<span>$<span id="precio-agregado"></span></span></span>'+
       '<span class="tag">' + 
       '<span><b>Mayoreo: </b></span>'+
       '<span>$<span id="mayoreo-agregado"></span></span></span>'+
       '</div>'+
       '</div>'+
      

       '</div>'+//inventario-pedro
       '</div>'+//row

'</form>',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#00e059',
    showConfirmButton: true,
    confirmButtonText: 'Actualizar', 
    cancelButtonColor:'#ff764d',
    didOpen: function () {
        $(document).ready(function() {
            $('#busquedaLlantas').select2({
                placeholder: "Selecciona una llanta",
                theme: "bootstrap",
                minimumInputLength: 1,
                ajax: {
                    url: "./modelo/traer_stock_llantas_totales.php",
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
                    "<div class='select2-result-repository clearfix' id='"+repo.id+"'>" +
                    "<div class='select2-contenedor-principal'>" +
                    "<div class='select2-result-repository__avatar'><img style='width: 50px; border-radius: 6px' src='./src/img/logos/" + repo.marca + ".jpg' /></div>" +
                      "<div class='select2-contenedor'>" +
                      "<div class='select2_modelo'></div>" +
                      "<div class='select2_description'></div>" +
                      "<div class='select2_statistics'>" +
                      "<div class='select2_marca'><i class='fa fa-star'></i> </div>" +
                        "<div class='select2_costo'><i class='fa fa-dollar-sign'></i> </div>" +
                        "<div class='select2_precio_venta'><i class='fa fa-tag'></i> </div>" +
                      "</div>" +
                      "</div>" +
                      "</div>" +
                    "</div>" +
                  "</div>"
                );
              
                $container.find(".select2_modelo").text(repo.modelo);
                $container.find(".select2_description").text(repo.descripcion);
                $container.find(".select2_marca").append(repo.marca);
                $container.find(".select2_precio_venta").append(repo.precio);
                $container.find(".select2_costo").append(repo.costo);

                $(".select2-result-repository clearfix").on("click", function() { 
                  alert("Llanta " + repo.marca);
                 })  
                //
              
                return $container;
              }

              function formatRepoSelection (repo) {
                //A partir de aqui puedes agregar las llantas Brayan
                ruta = "./src/img/logos/" + repo.marca + ".jpg";
                
                $("#select2-busquedaLlantas-container").attr("codigo", repo.id);
                $("#ancho-agregado").text(repo.ancho);
                $("#alto-agregado").text(repo.alto);
                $("#rin-agregado").text(repo.rin);
                $("#modelo-agregado").text(repo.modelo);
                $("#marca-agregado").text(repo.marca);
                $(".logo-marca-agregada").attr("src", ruta);

                $("#costo-agregado").text(repo.costo);
                $("#precio-agregado").text(repo.precio);
                $("#mayoreo-agregado").text(repo.mayoreo);
                $("#mayoreo-agregado").fadeIn(400)
               

                return repo.text || repo.descripcion;
              }
        });
    } ,

    
  }).then((result) =>{

    if(result.isConfirmed){

      code = $("#select2-busquedaLlantas-container").attr("codigo");
      cantidad = $("#cantidad").val();
      $.ajax({
        type: "POST",
        url: "./modelo/agregar-llanta-inv-pedro.php",
        data: {"code": code, "stock": cantidad},
        //dataType: "dataType",
        success: function (response) {
          
          if (response==1) {
            Swal.fire(
              "Correcto",
              "Se agrego la llanta correctamente",
              "success"
            ).then((result) =>{

              if(result.isConfirmed){
                table.ajax.reload();
              }
              table.ajax.reload();
              });
            
           

          }else if ( response == 2){
            Swal.fire(
              "Error",
              "Hubo un problema al insertar la llanta",
              "error"
            )

          }else if ( response == 3){
            Swal.fire(
              "Peligro",
              "Selecciona una llanta!",
              "warning"
            )
          }else if ( response == 4){
            Swal.fire(
              "Error",
              "Ingresa una cantidad",
              "warning"
            )
          }else if ( response == 5){
            Swal.fire(
              "Error",
              "Esa llanta ya esta en el inventario",
              "warning"
            )
          }

           

        }
      });


    }


  });
  
}



