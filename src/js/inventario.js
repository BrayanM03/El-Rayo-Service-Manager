function MostrarInventario(id_sucursal) { 
  ocultarSidebar();
    let user_sesion = $("#emp-title").attr("sesion_rol");
    let id_usuario = $("#emp-title").attr("sesion_id");
    user_sesion = parseInt(user_sesion);
    if(user_sesion != 1  && id_usuario !=7){
      visible_value = false;
     
    }else{
      visible_value = true;
    }

    $.ajax({
      type: "POST",
      url: "./modelo/inventarios/traer-dato-sucursal.php",
      data: {"id_suc": id_sucursal},
      success: function (response) {
        if(response.estatus){
          $("#sucursal_name").text(response.nombre);
          document.title = "Inventario de "+response.nombre;
        }
       
      }
    });

    table = $('#inventario-pedro').DataTable({   
        processing: true,
        serverSide: true, 
        rowCallback: function(row, data, index) {
          if(data[15] <= data[16] && data[16] !=0 && data[17] ==1){
            $(row).css('background-color','#edb95e')
          }/* else{
            $(row).css('background-color','#ffffbf')
          } */
        },
        ajax:'./modelo/traerInventario.php?id_sucursal='+id_sucursal,
            
        columns: [   
        { title: "#",              data: 0             },
        { title: "Codigo",         data: 12  , orderData: [1]},
        {title: 'Ancho',           data: 1,      visible: false },
        {title: 'Proporcion',      data: 2, visible: false },
        {title: 'Diametro',        data: 3,   visible: false },
        { title: "Descripcion",    data: 4    },
        { title: "Marca",          data: 5         },
        { title: "Modelo",         data: 6        },
        { title: "Costo",          data: 7, visible: visible_value, render: function(data){
          // Convert the string to a number using parseFloat()
          const numero = parseFloat(data);
      
          // Check if the conversion was successful (returns NaN if not a number)
          if (!isNaN(numero)) {
            let resultado = numero.toFixed(2);
            return resultado;
          } else {
            return "N/A"; // Or any other default value
          }
        }},
        { title: "Precio",         data: 8,    render: function(data){
          // Convert the string to a number using parseFloat()
          const numero = parseFloat(data);
      
          // Check if the conversion was successful (returns NaN if not a number)
          if (!isNaN(numero)) {
            let resultado = numero.toFixed(2);
            return resultado;
          } else {
            return "N/A"; // Or any other default value
          }
        }},
        { title: "Mayoreo",        data: 9,  render: function(data){
          // Convert the string to a number using parseFloat()
          const numero = parseFloat(data);
      
          // Check if the conversion was successful (returns NaN if not a number)
          if (!isNaN(numero)) {
            let resultado = numero.toFixed(2);
            return resultado;
          } else {
            return "N/A"; // Or any other default value
          }
        }},
        { title: "Sucursal",       data: 13      },
        { title: "Stock",          data: 15         },
        { title: "Imagen",         data: null, render: function(data,type,row) {
          return '<img src="./src/img/logos/'+ data[5] +'.jpg" style="width: 60px; border-radius: 8px">';
          }},
        {
          data: null,
          className: "celda-acciones",
          visible: visible_value, 
          render: function (row) { 
            
            return '';//'<div style="display: flex"><button type="button" onclick="editarStock('+row[11]+','+ id_sucursal +');" id="'+row[11]+'" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br>'; //<button type="button" onclick="borrarRegistro('+row[11]+','+ id_sucursal +');" id="'+row[11]+'" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>
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
        // Add more column definitions as needed
    ],
      paging: true,
      searching: true,
      scrollY: "300px",
      //info: true,
      dom: 'Blfrtip',
      responsive: true,
      lengthChange: false,
      order:[10, "desc"],
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
      
    });// fin de la tabla

    table.buttons()
    .container()
    .appendTo("#inventario-pedro_wrapper .col-md-6:eq(0)");
    //table.appendTo("#inventario-pedro_wrapper .col-md-6:eq(0)");
    //table.columns( [10] ).visible( false );
    


    
 }
 let sucursal_id = getParameterByName('id');

 MostrarInventario(sucursal_id);

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
      console.log(code);
      cantidad = $("#cantidad").val();
      $.ajax({
        type: "POST",
        url: "./modelo/inventarios/agregar-llanta-inventario.php",
        data: {"code": code, "stock": cantidad, "sucursal_id": sucursal_id},
        //dataType: "dataType",
        success: function (response) {
          
          if (response==1) {
            Swal.fire(
              "Correcto",
              "Se agrego la llanta correctamente",
              "success"
            ).then((result) =>{

              if(result.isConfirmed){
                table.ajax.reload(null,false);
              }
              table.ajax.reload(null,false);
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

/**
 * @param String name
 * @return String
 */
 function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
  results = regex.exec(location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}



function ocultarSidebar(){
  let sesion = $("#emp-title").attr("sesion_rol");
  if(sesion == 4){
    $(".rol-4").addClass("d-none");

  }
};

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

function newexportactiodn(e, dt, button, config) {

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
          setTimeout(dt.ajax.reload, 0);
          // Prevent rendering of the full data to the DOM
          return false;
      });
  });
  // Requery the server with the new one-time export settings
  dt.ajax.reload();

}