$(document).ready(function() {
  
    table = $('#inventario').DataTable({
      
      
        ajax: {
            method: "POST",
            url: "./modelo/traer_inv_total.php"
        },  
  
      columns: [   
        { title: "#",         data: null             },
        { title: "Codigo",         data: "id"             },
        { title: "Descripcion",    data: "Descripcion"    },
        { title: "Marca",          data: "Marca"          },
        { title: "Modelo",         data: "Modelo"         },
        { title: "Costo",          data: "precio_Inicial" },
        { title: "Precio",         data: "precio_Venta"   },
        { title: "Precio Mayoreo", data: "precio_Mayoreo" },
        { title: "Imagen",         data: "Marca", render: function(data,type,row) {
          return '<img src="./src/img/logos/'+ data +'.jpg" style="width: 60px; border-radius: 8px">';
          }},
        {
          data: null,
          className: "celda-acciones",
          render: function () {
            return '<div style="display: flex"><button type="button" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
          },
        },
      ],
      paging: true,
      searching: true,
      scrollY: "600px",
      info: true,
      responsive: true,
    
      
    });

     //Enumerar las filas "index column"
     table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
                   
      } );
    } ).draw();

      

});

function agregarLLanta() {

  Swal.fire({
    title: "Agregar llanta nueva",
    html: '<form class="mt-4" id="agregar-llanta-inv-total">'+

    '<div class="row">'+
    '<div class="col-4">'+
    '<div class="form-group">'+
    '<label><b>Unidad:</b></label></br>'+
    '<input class="form-control " value="" name="id-input-modal" readonly>'+
       '</div>'+
       '</div>'+
       '<div class="col-8">'+
       '<div class="form-group">'+
       '<label><b>Marca:</b></label></br>'+
       '<select class="form-control " id="marca" value="" name="marca"></select>'+
          '</div>'+
          '</div>'+
       '</div>'+

    '<div class="row">'+
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label for="ancho"><b>Ancho:</b></label></br>'+
        '<input type="number" class="form-control" id="ancho" value="" name="ancho" placeholder="Ancho" autocomplete="off">'+


   ' </div>'+
    '</div>'+
    
    
   '<div class="col-4">'+
    '<div class="form-group">'+
    '<label><b>Alto:</b></label></br>'+
    '<input type="number" value="" name="alto" id="alto" class="form-control" placeholder="Proporcion">'+
    '</div>'+
    '</div>'+

    
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Rin</b></label>'+
        '<input type="text" class="form-control" value=""  id="rin" name="rin" placeholder="Diametro">'+
    '</div>'+
        '</div>'+

       

        '<div class="col-6">'+
        '<div class="form-group">'+
        '<label><b>Modelo</b></label>'+
        '<input type="text" class="form-control" id="modelo" name="modelo" placeholder="Modelo">'+
        '</div>'+
        '</div>'+
       


    '</div>'+

    '<div class="row">'+
        '<div class="col-4">'+
            '<div class="form-group">'+
                '<label><b>Costo</b></label>'+
                '<input type="number" class="form-control" id="costo" value=""name="costo" placeholder="0.00">'+
            '</div>'+
        '</div>'+
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Precio</b></label>'+
        '<input type="number" class="form-control" value="" name="precio" id="precio" placeholder="0.00">'+
    '</div>'+
'</div>'+
'<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Mayorista</b></label>'+
        '<input type="number" class="form-control" value="" name="mayorista" di="mayorista" placeholder="0.00">'+
    '</div>'+
'</div>'+
        '</div>'+
    '</div>'+
    '<div class="row">'+
    '<div class="col-6">'+
        '<div class="form-group">'+
            '<label><b>Fecha</b></label>'+
            '<input type="date" class="form-control" value="" name="fecha" id="fecha" >'+
        '</div>'+
    '</div>'+
    
    '<div class="col-6">'+
        '<div class="form-group">'+
            '<label><b>Cantidad</b></label>'+
            '<input type="number" class="form-control" value=""name="cantidad" id="cantidad" placeholder="0">'+
        '</div>'+
    '</div>'+
    '</div>'+

    '<div class="row  mt-1">'+
    '<div class="col-12">'+
    '<div class="form-group" id="area-solucion">'+
    '<label><b>Descripción</b></label>'+
    '<textarea class="form-control" style="height:100px" name="descripcion" id="descripcion" form="formulario-editar-registro" placeholder="Escriba la descripcion del producto"></textarea>'+
    '</div>'+
    '</div>'+
    '</div>'+
            '</div>'+
'</form>',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#00e059',
    showConfirmButton: true,
    confirmButtonText: 'Actualizar', 
    cancelButtonColor:'#ff764d',
    didOpen: function () {
     
        $(document).ready(function() { 
            

            $('#marca').select2({
                placeholder: "Selecciona una marca",
                theme: "bootstrap",
                minimumInputLength: 1,
                ajax: {
                    url: "./modelo/traer-marca.php",
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
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-contenedor-principal'>" +
                    "<div class='select2-result-repository__avatar'><img style='width: 50px; border-radius: 6px' src='./src/img/logos/" + repo.imagen + ".jpg' /></div>" +
                      "<div class='select2-contenedor'>" +
                      "<div class='select2_marca' marca='"+ repo.imagen +"'></div>" +
                      "</div>" +
                      "</div>" +
                      "</div>" 
                );
              
                $container.find(".select2_marca").text(repo.nombre);

                
              
                return $container;
              }

             

              function formatRepoSelection (repo) {
                return repo.imagen || repo.text;
              }


        });
    } ,
  }).then(function () {  
      marcaLLanta = $("#select2-marca-container").text();  
      
      datos = $("#agregar-llanta-inv-total").serialize();
      model = $("#modelo").val();
     
    $.ajax({
        type: "POST",
        url: "./modelo/agregar-llanta-inv-total.php",
        data: { 'marca': marcaLLanta, 'modelo': datos},
        cache: false,
        success: function(response) {
            Swal.fire(
            "¡Correcto!",
            "El servidor respondio " + response,

            "success"
            )

            console.log(response);
        },
        failure: function (response) {
            Swal.fire(
            "Error",
            "La llanta no fue agregada.", // had a missing comma
            "error"
            )
        }
    });
}, 
function (dismiss) {
  if (dismiss === "cancel") {
    swal.fire(
      "Cancelled",
        "Se cancelo la operacion",
      "error"
    )
  };
})



}