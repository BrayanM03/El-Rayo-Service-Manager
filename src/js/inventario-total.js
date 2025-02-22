$(document).ready(function() {

  let user_sesion = $("#emp-title").attr("sesion_rol");
  let id_usuario = $("#emp-title").attr("sesion_id");
  let rol_usuario = $('#emp-title').attr('sesion_rol');
  user_sesion = parseInt(user_sesion);
  if(user_sesion != 1 && id_usuario !=7 && user_sesion != 4){
    visible_value = false;
   
  }else{
    visible_value = true;
  }
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
        { title: "Codigo",    data: "id"    },
        { title: "Descripcion",    data: "descripcion"    },
        { title: "Marca",          data: "marca"          },
       /*  { title: "Modelo",         data: "modelo"         }, */
        { title: "Costo",          data: "costo", visible: visible_value, render: function(data){
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
        { title: "Precio lista",         data: "precio_lista",       render: function(data){
          // Convert the string to a number using parseFloat()
          const numero = parseFloat(data);
      
          // Check if the conversion was successful (returns NaN if not a number)
          if (!isNaN(numero)) {
            let resultado = numero.toFixed(2);
            return resultado;
          } else {
            return "N/A"; // Or any other default value
          }
        }  },
        { title: "Precio desc.",         data: "precio",       render: function(data){
          // Convert the string to a number using parseFloat()
          const numero = parseFloat(data);
      
          // Check if the conversion was successful (returns NaN if not a number)
          if (!isNaN(numero)) {
            let resultado = numero.toFixed(2);
            return resultado;
          } else {
            return "N/A"; // Or any other default value
          }
        }  },
        { title: "Precio Mayoreo", data: "mayoreo"     ,render: function(data){
          // Convert the string to a number using parseFloat()
          const numero = parseFloat(data);
      
          // Check if the conversion was successful (returns NaN if not a number)
          if (!isNaN(numero)) {
            let resultado = numero.toFixed(2);
            return resultado;
          } else {
            return "N/A"; // Or any other default value
          }
        }   },   
/*         { title: "id",             data: "id"        }, */
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
                             /*  $('#select'+row.id).attr("respuesta", response);
                              valorcillo = $('#select'+row.id).attr("respuesta");  */ 
                              valor = $('#select'+row.id).parent().next().text(response.stock); 
                              $("#stock-"+row.id).text(response.stock);         
                             
                          },
                         
                        });
                              
            });

           
            return '<span id="stock-'+row.id+'">'+row.stock+'</span>';
          },
        },
        //{ title: "Stock",          data: "stock"          },
      /*   { title: "Fecha",          data: "fecha"          }, */
        { title: "Imagen",         data: "marca", render: function(data,type,row) {
          
          return '<img onerror="this.src=`./src/img/neumaticos/NA.JPG`;" src="./src/img/logos/'+ data +'.jpg" style="width: 60px; border-radius: 8px">'; 
          }}, 
        { title: "llanta",         data: 'id', render: function(data,type,row) {
            
            return '<img onerror="this.src=`./src/img/neumaticos/NA.JPG`;" src="./src/img/neumaticos/llanta_'+ data +'_1.png" style="width: 60px; border-radius: 8px" >'; 
          }}, 
        {
          data: null,
          className: "celda-acciones",
          render: function (row) {
            
            if(rol_usuario ==1 || id_usuario == 24 | id_usuario == 27){
              return `<div style="display: flex"><button type="button" onclick="editarRegistro(${row.id});" id="${row.id}" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button></br></div>`; //<button type="button" onclick="borrarRegistro(${row.id});" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button>
            }else{
              return 'lol';
            //return '<div style="display: flex"><button type="button" onclick="editarRegistro('+row.id+');" id="'+ row.id +'" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button>';
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
       /*  { targets: [8], orderable: false } */
        // Add more column definitions as needed
    ],
      paging: true,
      searching: true,
      serverSide: true, // Enable server-side processing
      processing: true, // Show processing indicator
      pageLength: 10, // Number of rows per page
      lengthMenu: [10, 25, 50, 100],
      scrollY: "800px",
      info: true,
      dom: 'Blfrtip',
      responsive: true,
      order: [1, "desc"],
      lengthChange: true,
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

     //Enumerar las filas "index column"
     table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
                   
      } );
    } ).draw();

      

});


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