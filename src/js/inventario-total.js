$(document).ready(function() {


    table = $('#inventario').DataTable({
      
      
        ajax: {
            method: "POST",
            url: "./modelo/traer_inv_total.php",
            dataType: "json",
            data: function (d) {
              d.page = Math.ceil(d.start / d.length) + 1;
          }
        },  
        dataSrc: function (json) {
          // Obtener el número total de registros y el número de registros filtrados
          var recordsTotal = json.recordsTotal || 0;
          var recordsFiltered = json.recordsFiltered || 0;

          // Actualizar la información en el área correspondiente
          $('.dataTables_info').html('Mostrando ' + recordsFiltered + ' registros filtrados de un total de ' + recordsTotal);

          return json.data; // Devolver los datos para DataTables
      },
      columns: [   
        { title: "#",              data: null             },
        //{ title: "Codigo",         data: "id"             },
        { title: "Descripcion",    data: "descripcion"    },
        { title: "Marca",          data: "marca"          },
        { title: "Modelo",         data: "modelo"         },
        { title: "Costo",          data: "costo"          },
        { title: "Precio",         data: "precio"         },
        { title: "Precio Mayoreo", data: "mayoreo"        },   
        { title: "id",             data: "id"        },
        {title: "Sucursal",
          data: 'sucursales',
          className: "celda-select",
          render: function (data, display, row) {
            sucursales = data;
            options = "";
            sucursales.forEach(element => {
                  id_suc = element["id"];
                  nombre = element["nombre"];
                  options += '<option value="'+ id_suc +'">'+ nombre +'</option>';
                  
            });
                
            return'<select class="select-sucursal form-control" id="select'+ row.id +'" codigo="'+row.id +'">'+
                '<option value="total">Total</option> '+
                options+
                '</select>'; 
           
          },
        },
        {title: "Stock",
          data: "stock",
          className: "celda-stock",
          render: function (data,display, row) {  
            $(document).on('change', '#select'+row.id, function(event) {
            
            codigo = $(this).attr("codigo");
            suc = $(this).find("option:selected").attr("value");

                        $.ajax({
                          type: "post",
                          url: "./modelo/traer-stock-por-suc.php",
                          data: {"codigo" : codigo, "sucursal" : suc},
                          dataType: "json",
                          success: function (response) {
                          
                             
                              $('#select'+row.id).attr("respuesta", response);
                              valorcillo = $('#select'+row.id).attr("respuesta");  
                              valor = $('#select'+row.id).parent().next().text(response.stock); 
                                            
                             
                          },
                         
                        });
                              
            });

           
            return row.stock;
          },
        },
        //{ title: "Stock",          data: "stock"          },
        { title: "Fecha",          data: "fecha"          },
        { title: "Imagen",         data: "marca", render: function(data,type,row) {
          
          return '<img src="./src/img/logos/'+ data +'.jpg" style="width: 60px; border-radius: 8px">'; 
          }}, 
        {
          data: null,
          className: "celda-acciones",
          render: function (row) {
            let rol = $('#id_rol').attr('role');
            if(rol ==1 ){
              return `<div style="display: flex"><button type="button" onclick="editarRegistro(${row.id});" id="${row.id}" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button></br><button type="button" onclick="borrarRegistro(${row.id});" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>`;
            }else{
            return '<div style="display: flex"><button type="button" onclick="editarRegistro('+row.id+');" id="'+ row.id +'" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button>';
          }
          },
        },
      ],
      columnDefs: [
        {
            targets: 0,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1; // Display row number
            }
        },
        { targets: [8], orderable: false }
        // Add more column definitions as needed
    ],
      paging: true,
      searching: true,
      serverSide: true, // Enable server-side processing
      processing: true, // Show processing indicator
      pageLength: 10, // Number of rows per page
      lengthMenu: [10, 25, 50, 100],
      scrollY: "300px",
      info: true,
      dom: 'Blfrtip',
      responsive: true,
      order: [7, "desc"],
      lengthChange: false,
      buttons: [
        'copy', 'csv', {
          'extend':'excel',
          'title': 'Hoja Excel',
          'titleAttr': 'Excel',
          'action': newexportaction
        }, 'pdf', 'print'
    ],

      language: {
            
        emptyTable: "No hay registros",
        infoEmpty: "Ups!, no hay registros aun en esta categoria."
      }
      
    });

    $("table.dataTable thead").addClass("table-success")

    table.columns( [7] ).visible( false );
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
    
       '<div class="col-12">'+
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
        '<input type="number" class="form-control" id="ancho"  name="ancho" placeholder="Ancho" autocomplete="off" step="0.1">'+


   ' </div>'+
    '</div>'+
    
    
   '<div class="col-4">'+
    '<div class="form-group">'+
    '<label><b>Alto:</b></label></br>'+
    '<input type="number" name="alto" id="alto" class="form-control" placeholder="Proporcion" step="0.1">'+
    '</div>'+
    '</div>'+

    
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Rin</b></label>'+
        '<input type="number" class="form-control"  id="rin" name="rin" placeholder="Diametro" step="0.1">'+
    '</div>'+
        '</div>'+

       

        '<div class="col-8 ">'+
        '<div class="form-group">'+
        '<label><b>Modelo</b></label>'+
        '<input type="text" class="form-control" id="modelo" name="modelo" placeholder="Modelo">'+
        '</div>'+
        '</div>'+

       
    /*'<div class="col-6">'+
        '<div class="form-group">'+
            '<label><b>Fecha</b></label>'+
            '<input type="date" class="form-control" value="" name="fecha" id="fecha" >'+
        '</div>'+
    '</div>'+*/
    
    
   
       


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
      "descripcion":    $("#descripcion").val()
    };
 

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
            ).then((result) =>{

              if(result.isConfirmed){
                table.ajax.reload(null,false);
              }else if(result.isDenied){
                table.ajax.reload(null,false);
              }
              });
           
        }else{
          Swal.fire(
            "¡Erro!",
            "No se agrego la llanta",
            "error"
            )
            console.log(response);
            table.draw(false);
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

function newexportaction(e, dt, button, config) {
  var self = this;
  var oldStart = dt.settings()[0]._iDisplayStart;

  dt.one('preXhr', function (e, s, data) {
    // Just this once, load all data from the server...
    data.start = 0;
    data.length = -1;

    dt.one('preDraw', function (e, settings) {
      // Call the original action function
      if (button[0].className.indexOf('buttons-copy') >= 0) {
        $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
      } else if (button[0].className.indexOf('buttons-excel') >= 0) {
        $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
          $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
          $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
      } else if (button[0].className.indexOf('buttons-csv') >= 0) {
        $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
          $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
          $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
      } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
        $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
          $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
          $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
      } else if (button[0].className.indexOf('buttons-print') >= 0) {
        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
      }

      dt.one('preXhr', function (e, s, data) {
        // DataTables thinks the first item displayed is index 0, but we're not drawing that.
        // Set the property to what it was before exporting.
        settings._iDisplayStart = oldStart;
        data.start = oldStart;
      });

      // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
      setTimeout(function () {
        dt.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
          cell.innerHTML = i + 1;
        });
        dt.ajax.reload();
      }, 0);

      // Prevent rendering of the full data to the DOM
      return false;
    });
  });

  // Requery the server with the new one-time export settings
  dt.ajax.reload();
}