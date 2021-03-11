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
        { title: "Fecha",          data: "Fecha"          },
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
       '<select class="form-control" id="marca" name="marca"></select>'+
          '</div>'+
          '</div>'+
       '</div>'+

    '<div class="row">'+
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label for="ancho"><b>Ancho:</b></label></br>'+
        '<input type="number" class="form-control" id="ancho"  name="ancho" placeholder="Ancho" autocomplete="off">'+


   ' </div>'+
    '</div>'+
    
    
   '<div class="col-4">'+
    '<div class="form-group">'+
    '<label><b>Alto:</b></label></br>'+
    '<input type="number" name="alto" id="alto" class="form-control" placeholder="Proporcion">'+
    '</div>'+
    '</div>'+

    
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Rin</b></label>'+
        '<input type="text" class="form-control"  id="rin" name="rin" placeholder="Diametro">'+
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
        '<input type="number" class="form-control" value="" name="mayorista" id="mayorista" placeholder="0.00">'+
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
            '<input type="number" class="form-control" name="cantidad" id="cantidad" placeholder="0">'+
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
    focusConfirm: false,
    iconColor : "#36b9cc",
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
    showLoaderOnConfirm: true,
    preConfirm: (respuesta) =>{

      data = {
        "marca":          $("#select2-marca-container").text(),  
        "ancho":          $("#ancho").val(),
        "alto":           $("#alto").val(),
        "rin":            $("#rin").val(),
        "costo":          $("#costo").val(),
        "precio":         $("#precio").val(),
        "mayorista":      $("#mayorista").val(),
        "modelo":         $("#modelo").val(),
        "fecha":          $("#fecha").val(),
        "descripcion":    $("#descripcion").val()
      };

      if(data["marca"] == "Selecciona una marca"){
        /*const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })
        
        Toast.fire({
          icon: 'error',
          title: 'Falta poner la marca'
        })*/
        $(".datoVacio").removeClass("datoVacio");
        $(".select2-container").addClass("datoVacio");
        Swal.showValidationMessage(
          `Selecciona una marca`
        )
      }else if( data["ancho"] == ""){
        $(".datoVacio").removeClass("datoVacio");
        $(".border-danger").removeClass("border-danger");
        $("#ancho").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece un ancho`
        )
      }else if(data["alto"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#alto").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece un alto`
        )
      }else if( data["rin"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#rin").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece el rin`
        )
      }else if( data["modelo"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#modelo").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece un modelo`
        )
      }else if(data["costo"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#costo").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece el precio que te costó la llanta`
        )
      }else if( data["precio"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#precio").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece un precio`
        )
      }else if(data["mayorista"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#mayorista").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece un precio de mayorista o descuento`
        )
      }else if(data["fecha"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#fecha").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece una fecha`
        )
      }else if( data["cantidad"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#cantidad").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece una descripcion`
        )
      }else if( data["descripcion"] == ""){
        $(".datoVacio").removeClass("datoVacio");      
        $(".border-danger").removeClass("border-danger");
        $("#descripcion").addClass("border-danger");
        Swal.showValidationMessage(
          `Establece una descripcion`
        )
      }
    }
    //Si el resultado es OK tons:
  }).then((result) => {  

   if(result.isConfirmed){

    data = {
      "marca":          $("#select2-marca-container").text(),  
      "ancho":          $("#ancho").val(),
      "alto":           $("#alto").val(),
      "rin":            $("#rin").val(),
      "costo":          $("#costo").val(),
      "precio":         $("#precio").val(),
      "mayorista":      $("#mayorista").val(),
      "modelo":         $("#modelo").val(),
      "fecha":          $("#fecha").val(),
      "descripcion":    $("#descripcion").val()
    };

    console.log(data["marca"]);  

    $.ajax({
      type: "POST",
      url: "./modelo/agregar-llanta-inv-total.php",
      data:data,
      cache: false,
      success: function(response) {
        if (response==1) {
          Swal.fire(
            "¡Correcto!",
            "Se agrego la llanta",
            "success"
            )
            console.log(response);
        }else{
          Swal.fire(
            "¡Erro!",
            "No se agrego la llanta",
            "error"
            )
            console.log(response);
        }
          

          
      },
      failure: function (response) {
          Swal.fire(
          "Error",
          "La llanta no fue agregada.", // had a missing comma
          "error"
          )
      }
  });
    

    
   }

   

     
   
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