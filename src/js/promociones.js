function MostrarPromociones(id_sucursal) { 
    ocultarSidebar();
      let user_sesion = $("#emp-title").attr("sesion_rol");
      let id_usuario = $("#emp-title").attr("sesion_id");
      user_sesion = parseInt(user_sesion);
      if(user_sesion != 1  && id_usuario !=7 && user_sesion != 4){
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
          ajax:'./modelo/configuraciones/configuracion_promociones/traer-promociones.php?id_sucursal='+id_sucursal,
              
          columns: [   
          { title: "#",              data: 0             },
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
          { title: "Promoción",        data: 10,  render: function(data){
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
        
          { title: "Imagen",         data: null, render: function(data,type,row) {
            return '<img src="./src/img/logos/'+ data[5] +'.jpg" style="width: 60px; border-radius: 8px">';
            }},
          {
            data: null,
            className: "celda-acciones",
            visible: visible_value, 
            render: function (row) { 
              
              return `<div class="btn btn-danger" onclick="eliminarPromocion(${row[0]})"><span class="fa fa-trash"></span><span class="hidden-xs"></span></div>`;
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
  
   MostrarPromociones(sucursal_id);
  
   function agregarPromocion(){
    let id_llanta =  $("#select2-busquedaLlantas-container").attr("id_llanta");
    let precio_promocion = $("#precio_promocion").val();

    $.ajax({
      type: "post",
      url: "./modelo/configuraciones/configuracion_promociones/actualizar_promocion.php",
      data: {id_llanta, precio_promocion},
      dataType: "json",
      success: function (response) {
        let icon = response.estatus == true ? 'success' : 'error';
        Swal.fire(
          {icon:icon, title:response.mensaje, confirmButtonText: 'Entendido'}
        )

        table.ajax.reload(null, false);
      }
    });
   }
  
   function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
  }
  
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
          searchTerm: params.term, // search term
          page: params.page || 1,
          rol: params.rol
        };
        },
      
        cache: true

    },
    processResults: function (data, params) {
      params.page = params.page || 1;   
      return {
        results: data.results,
        pagination: {
            more: (params.page * 10) < data.total_count // Verificar si hay más resultados para cargar
          }
      };
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
          "<div class='select2-result-repository clearfix' desc='"+repo.descripcion+" marca='"+repo.marca +
          " id='"+repo.marca+" costo='"+repo.costo +" id='tyre' precio='"+repo.precio+" idcode='"+repo.id+"'>" +
          "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
          "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.marca + ".jpg' /></div>" +
            "<div class='col-md-10 select2-contenedor'>" +
            "<div class='select2_modelo'>Modelo "+ repo.modelo +"</div>" +
            "<div class='select2_description'>" + repo.descripcion + "</div>" +
            "</div>" +
            "</div>" +
            "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
            "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.marca+"</div>" +
              "<div class='select2_costo'><i class='fa fa-dollar-sign'></i> "+repo.costo+" (Costo) </div>" +
              "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio +" (precio)</div>" + 
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
      console.log(repo);
      if(repo.promocion ==1){
          $("#btn-promocion").removeClass().addClass('btn btn-success').html('Actualizar')
      }else{
        $("#btn-promocion").removeClass().addClass('btn btn-primary').html('Agregar promoción')
      }
      $("#select2-busquedaLlantas-container").attr("id_llanta", repo.id);

      return repo.text || repo.descripcion;
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

  function eliminarPromocion(id_llanta){

      Swal.fire({
        icon: 'question',
        html: `¿Desea eliminar esta promoción`,
        confirmButtonText: 'Eliminar',
        showCloseButton: true
      }).then(function(response){
        if(response.isConfirmed){
          $.ajax({
            type: "post",
            url: "./modelo/configuraciones/configuracion_promociones/eliminar-promocion.php",
            data: {id_llanta},
            dataType: "json",
            success: function (response) {
              let icon = response.estatus == true ? 'success' : 'error';
              Swal.fire(
                {icon:icon, title:response.mensaje, confirmButtonText: 'Entendido'}
              )
              table.ajax.reload(null, false);
            }
          });
        }
      })
  }