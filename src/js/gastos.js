toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
  
MostrarGastos();
const meses = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
  ];

  
  function MostrarGastos() {  
    //$.fn.dataTable.ext.errMode = 'none';
    ocultarSidebar();
    table = $('#gastos').DataTable({
      
    processing: true,
    serverSide: true,
    ajax: './modelo/gastos/historial-gastos.php',
    rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
  
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
      },
     
    columns: [   
    { title: "#",              data: null   },
    { title: "Folio",          data: 0      },
    { title: "Fecha",          data: 1      },
    { title: "Categoria",      data: 2      },
    { title: "Descripción",    data: 3      }, 
    { title: "Monto",          data: 4      },
    { title: "Forma pago",     data: 5      },
    { title: "Usuario",        data: 6      },
    { title: "No. factura",    data: 7      },
    { title: "Sucursal",       data: 10      },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        rol = $("#titulo-hv").attr("rol");
     
        if(row[8] == 1){
          if(rol == "1" ){
            return `
            <div style="display: flex; width: auto;">
                <button onclick="editarGasto(${row[0]}, '${row[9]}');" title="Editar reporte" type="button" class="buttonPDF btn btn-warning" style="margin-right: 8px">
                    <span class="fa fa-edit"></span><span class="hidden-xs"></span>
                </button>
                <button onclick="traerTicket(${row[0]}, '${row[9]}');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">
                    <span class="fa fa-file-pdf"></span><span class="hidden-xs"></span>
                </button>
            </div>
            `;
            
        }else if(rol == '2'){
          return `
            <div style="display: flex; width: auto;">
                <button onclick="traerTicket(${row[0]}, '${row[9]}');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">
                    <span class="fa fa-file-pdf"></span><span class="hidden-xs"></span>
                </button>
            </div>
            `;
        }else{
            return '';
        }
        }else{
          if(rol == "1" ){
            return `
            <div style="display: flex; width: auto;">
                <button onclick="editarGasto(${row[0]}, '${row[9]}');" title="Editar reporte" type="button" class="buttonPDF btn btn-warning" style="margin-right: 8px">
                    <span class="fa fa-edit"></span><span class="hidden-xs"></span>
                </button>
            </div>
            `;
          }else{
            return '';
          }
          
        }
       
        
         },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  ordering: "enable",
  multiColumnSort: true,
  order: [[1, "desc"]],
  'columnDefs': [
    { 'orderData':[2], 'targets': [1] },
    {
        'targets': [2],
        'visible': false,
        'searchable': false
    },
  ],
  //order: [1, "desc"],
  
  
  });
  //table.columns( [6] ).visible( true );
  $("table.dataTable thead").addClass("table-info")
  
  }
  
  function ocultarSidebar(){
    let sesion = $("#emp-title").attr("sesion_rol");
  if(sesion == 4){
    $(".rol-4").addClass("d-none");
  
  }
  };
  
  function traerTicket(id, extension){
    window.open(`./src/docs/gastos/GA${id}.${extension}`);
  }
  
  var tooltipSpan = document.getElementById('tooltip-span');
  
  window.onmousemove = function (e) {
      var x = e.clientX,
          y = e.clientY;
     // tooltipSpan.style.top = (y + 20) + 'px';
     // tooltipSpan.style.left = (x + 20) + 'px';
  };
  
  
  function agregarGasto(){  
    Swal.fire({
      title: 'Agregar nuevo gasto',
      width: 800,
      text: "Ingresa la información del gasto",
      html: `
        <div class="container">
          <div class="row">
            <div class="col-12">
              
              <div class="form-group header-preview">
                <div class="row">
                  <div class="col-12 col-md-4 text-left">
                    <label for="sucursal-gasto">Sucursal:</label>
                    <select id="sucursal-gasto" class="form-control"></select>
                  </div>
                  <div class="col-12 col-md-4 text-left">
                    <label for="monto-gasto">Monto:</label>
                    <input id="monto-gasto" class="form-control" type="number" placeholder="0.00">
                  </div>
                </div>  
                <div class="row mt-2">
                  <div class="col-12 col-md-4 text-left">
                    <label for="categoria-gasto">Categoria:</label>
                    <select id="categoria-gasto" class="form-control"></select>
                  </div>
                  <div class="col-12 col-md-4 text-left">
                    <label for="factura-gasto">No. Factura:</label>
                    <input id="factura-gasto" class="form-control" type="text" placeholder="Folio">
                  </div>
                  <div class="col-12 col-md-4 text-left">
                    <label for="fecha-gasto">Fecha:</label>
                    <input id="fecha-gasto" class="form-control" type="date">
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-12 col-md-12 text-left">
                    <label>Descripción:</label>
                    <textarea id="descripcion-gasto" class="form-control" placeholder="Escribe la descripción del gasto..."></textarea>
                  </div>
                </div>

                <div class="row mt-2">
                  <div class="col-12 col-md-12 text-left">
                    <label>Comprobante:</label>
                    <input type="file" class="form-control" id="comprobante-gasto">
                    <div class="row mt-2 justify-content-center">
                        <div class="col-12 col-md-5 text-center">
                            <div id="area-canvas">
                                  <canvas id="thumbnailCanvas" width="100" height="150"></canvas>
                                  <img src="" height="200" id="gasto-imagen">
                                </div>
                            </div>
                        </div>
                  </div>
                </div>
              </div> 
  
            </div>
          </div>    
        </div>
      `,
      showCancelButton: true,
      showConfirmButton:true,
      showDenyButton: false,
      showCloseButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#dc3545',
      denyButtonColor: '#5DC1B9',
      confirmButtonText: 'Registrar',  
      //denyButtonText: 'Venta a credito',
      cancelButtonText: 'Cancelar',
      showLoaderOnConfirm: true,
      didOpen: () => {
        const fechaActual = new Date().toISOString().split('T')[0];
        $("#fecha-gasto").val(fechaActual);
        $.ajax({
          type: "post",
          url: "./modelo/gastos/traer-info-modal.php",
          data: {},
          dataType: "JSON",
          success: function (response) {
              if(response.estatus){
               let select_categorias = $("#categoria-gasto");
               response.data.categorias.forEach(element => {
                select_categorias.append(`<option value="${element.id}">${element.nombre}</option>`);
                
               });

               let sucursal_gastos = $("#sucursal-gasto");
               response.data.sucursales.forEach(element => {
                sucursal_gastos.append(`<option value="${element.id}">${element.nombre}</option>`);
               });

               if(response.data.id_sesion_rol !=1){
                sucursal_gastos.val(response.data.id_sesion_sucursal)
                sucursal_gastos.prop('disabled', true)
               }
              }
          }
        });
        let input_comprobante = $("#comprobante-gasto");
        input_comprobante.on('change',() => {
          cargarComprobanteRegistro();
        })
      },
      preConfirm: async () => {
        let desc = $("#descripcion-gasto").val()
        let cate = $("#categoria-gasto").val()
        let fecha = $("#fecha-gasto").val()
        let monto = $("#monto-gasto").val()
        if(desc.trim() == '' || cate == '' || fecha == '' || monto == ''){
          Swal.showValidationMessage(
            `Falta un dato en el formulario`
          );
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
  
    }).then((respuesta) => {
      if(respuesta.isConfirmed){
        let desc = $("#descripcion-gasto").val()
        let cate = $("#categoria-gasto").val()
        let fecha = $("#fecha-gasto").val()
        let folio_factura = $("#factura-gasto").val()
        let id_sucursal = $("#sucursal-gasto").val()
        let monto = $("#monto-gasto").val()
        let comprobante_gasto = document.getElementById('comprobante-gasto'); 
        var file =  comprobante_gasto.files[0];

        var formData = new FormData();
        formData.append('comprobante', file);
        formData.append('descripcion', desc);
        formData.append('fecha', fecha);
        formData.append('categoria', cate);
        formData.append('folio_factura', folio_factura);
        formData.append('id_sucursal', id_sucursal);
        formData.append('monto', monto);
      
        swal({
          title: 'Now loading',
          allowEscapeKey: false,
          allowOutsideClick: false,
          timer: 2000,
          onOpen: () => {
            swal.showLoading();
          }
        })
        
        $.ajax({
          type: "post",
          url: "./modelo/gastos/registrar-gasto.php",
          data: formData,
          contentType: false,
          processData: false,
          dataType: "JSON",
          success: function (response2) {
            if(response2.estatus){
              Swal.fire({
                icon: 'success',
                title: response2.mensaje
              })
            }else{
              Swal.fire({
                icon: 'error',
                title: response2.mensaje
              })
            }
            table.ajax.reload(null, false)
          }
        });
      }else if(respuesta.isDenied){
        abonarApartado(id_apartado);
      }
      
    })
  }
  
  function realizarVentaApartado(metodos_pago, id_apartado){
    $.ajax({
      type: "post",
      url: "./modelo/apartados/realizar-venta-apartados.php",
      data: {"id_apartado": id_apartado, "metodos_pago": metodos_pago},
      dataType: "JSON",
      success: function (response) {
        if(response.estatus){
       Swal.fire({
          title: 'Exito',
          text: response.mensaje,
          html: `Folio de venta: RAY${response.id_venta}`,
          icon: 'success',
          showCancelButton: false,
          confirmButtonColor: '#28a745',
          confirmButtonText: 'Aceptar', 
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          }
        })
        }
        
      }
    });
  }
  
  const audio_2 = new Audio("./src/sounds/success-sound.mp3");
  audio_2.volume = 0.5;

  function cargarComprobanteRegistro(){
    let input_comprobante = document.getElementById('comprobante-gasto');
    let file = input_comprobante.files[0];
    let area_canvas = $("#area-canvas");

    if (file && file.type === 'application/pdf') {
    // Ruta del PDF desde donde se obtendrá la miniatura
    var pdfURL = URL.createObjectURL(file);//'./src/docs/gastos/Folio RAY440.pdf'; // Reemplaza con la ruta correcta a tu archivo PDF
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    // Obtiene el elemento canvas
    area_canvas.empty().append(`<canvas id="thumbnailCanvas" width="100" height="150"></canvas>`)
    var canvas = document.getElementById('thumbnailCanvas');

    // Carga el PDF
    pdfjsLib.getDocument(pdfURL).promise.then(function(pdfDoc) {
      // Obtiene la primera página del PDF (página 1)
      var pageNumber = 1;

      // Renderiza la página en el canvas
      pdfDoc.getPage(pageNumber).then(function(page) {
        var viewport = page.getViewport({ scale: 0.2 });
        var context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        var renderContext = {
          canvasContext: context,
          viewport: viewport,
        };
        
        
        page.render(renderContext).promise.then(function() {
     
        });
      });
    });
   // reader.readAsDataURL(file);
  }else if (file.type.startsWith('image/')){
    // Si es una imagen (cualquier tipo de imagen)
    var reader = new FileReader();
    area_canvas.empty().append(`<img src="" height="200" id="gasto-imagen">`)
    let img_preview = document.getElementById('gasto-imagen')
  
      reader.onloadend = function () {
        img_preview.src = reader.result;
      }
    
      if (file) {
        reader.readAsDataURL(file);
       // clearCanvas()
      } else {
        img_previewpreview.src = "";
      }

      Swal.resetValidationMessage()
     
  }else{
    area_canvas.empty()
    Swal.showValidationMessage(
      `Tipo de archivo no admitido`
    );
  }
  }

  function clearCanvas() {
    var canvas = document.getElementById('thumbnailCanvas');
    var context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
  }

  function editarGasto(id, file_type){
    
    if(file_type=='NA'){
      eliminar_comprobante = false;
    }

    $.ajax({
      type: "post",
      url: "./modelo/gastos/traer-info-gasto.php",
      data: {"id": id},
      dataType: "JSON",
      success: function (response) {
        if(response.estatus){
          Swal.fire({
            title: 'Editar gasto',
            width: 800,
            text: "Puedes editar la información del gasto",
            html: `
              <div class="container">
                <div class="row">
                  <div class="col-12">
                    
                    <div class="form-group header-preview">
                      <div class="row">
                        <div class="col-12 col-md-4 text-left">
                          <label for="sucursal-gasto">Sucursal:</label>
                          <select id="sucursal-gasto" class="form-control"></select>
                        </div>
                        <div class="col-12 col-md-4 text-left">
                          <label for="monto-gasto">Monto:</label>
                          <input id="monto-gasto" class="form-control" value="${response.data.monto}" type="number" placeholder="0.00">
                        </div>
                      </div>  
                      <div class="row mt-2">
                        <div class="col-12 col-md-4 text-left">
                          <label for="categoria-gasto">Categoria:</label>
                          <select id="categoria-gasto" class="form-control"></select>
                        </div>
                        <div class="col-12 col-md-4 text-left">
                          <label for="factura-gasto">No. Factura:</label>
                          <input id="factura-gasto" class="form-control" value="${response.data.no_factura}" type="text" placeholder="Folio">
                        </div>
                        <div class="col-12 col-md-4 text-left">
                          <label for="fecha-gasto">Fecha:</label>
                          <input id="fecha-gasto" class="form-control" type="date" value="${response.data.fecha}">
                        </div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-12 col-md-12 text-left">
                          <label>Descripción:</label>
                          <textarea id="descripcion-gasto" class="form-control" placeholder="Escribe la descripción del gasto...">${response.data.descripcion}</textarea>
                        </div>
                      </div>
      
                      <div class="row mt-2">
                        <div class="col-12 col-md-12 text-left">
                          <label>Comprobante:</label>
                          <input type="file" class="form-control" id="comprobante-gasto">
                          <div class="row mt-4 justify-content-center">
                              <div class="col-12 col-md-5 text-center">
                                  <div id="area-canvas">
                                        <canvas id="thumbnailCanvas" width="100" height="150"></canvas>
                                        <img src="" height="200" id="gasto-imagen">
                                      </div>
                                  </div>
                              </div>
                        </div>
                      </div>
                    </div> 
        
                  </div>
                </div>    
              </div>
            `,
            showCancelButton: true,
            showConfirmButton:true,
            showDenyButton: false,
            showCloseButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            denyButtonColor: '#5DC1B9',
            confirmButtonText: 'Actualizar',  
            //denyButtonText: 'Venta a credito',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            didOpen: () => {
              
              $.ajax({
                type: "post",
                url: "./modelo/gastos/traer-info-modal.php",
                data: {},
                dataType: "JSON",
                success: function (response2) {
                    if(response2.estatus){
                     let select_categorias = $("#categoria-gasto");
                     response2.data.categorias.forEach(element => {
                      select_categorias.append(`<option value="${element.id}">${element.nombre}</option>`);
                      
                     });
      
                     let sucursal_gastos = $("#sucursal-gasto");
                     response2.data.sucursales.forEach(element => {
                      sucursal_gastos.append(`<option value="${element.id}">${element.nombre}</option>`);
                     });

                     $("#sucursal-gasto").val(response.data.id_sucursal);
                     $("#categoria-gasto").val(response.data.id_categoria_gasto);
                    }
                }
              });
              cargarComprobanteEdicion(id, file_type);
              let input_comprobante = $("#comprobante-gasto");
              input_comprobante.on('change',() => {
                cargarComprobanteRegistro();
              })
              
            },
            preConfirm: async () => {
              let desc = $("#descripcion-gasto").val()
              let cate = $("#categoria-gasto").val()
              let fecha = $("#fecha-gasto").val()
              let monto = $("#monto-gasto").val()
              if(desc.trim() == '' || cate == '' || fecha == '' || monto ==null){
                Swal.showValidationMessage(
                  `Falta un dato en el formulario`
                );
              }
            },
            allowOutsideClick: () => !Swal.isLoading(),
        
          }).then((respuesta) => {
            if(respuesta.isConfirmed){
              let desc = $("#descripcion-gasto").val()
              let cate = $("#categoria-gasto").val()
              let fecha = $("#fecha-gasto").val()
              let folio_factura = $("#factura-gasto").val()
              let id_sucursal = $("#sucursal-gasto").val()
              let monto = $("#monto-gasto").val()
              let comprobante_gasto = document.getElementById('comprobante-gasto'); 
              var file =  comprobante_gasto.files[0];
              var formData = new FormData();
              formData.append('extension', file_type);
              formData.append('id_gasto', id);
              formData.append('comprobante', file);
              formData.append('descripcion', desc);
              formData.append('fecha', fecha);
              formData.append('categoria', cate);
              formData.append('folio_factura', folio_factura);
              formData.append('id_sucursal', id_sucursal);
              formData.append('monto', monto);
              formData.append('eliminar_comprobante', eliminar_comprobante);
            
              $.ajax({
                type: "post",
                url: "./modelo/gastos/actualizar-gasto.php",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function (response2) {
                  if(response2.estatus){
                    Swal.fire({
                      icon: 'success',
                      title: response2.mensaje
                    })
                  }else{
                    Swal.fire({
                      icon: 'error',
                      title: response2.mensaje
                    })
                  }
                  table.ajax.reload(null, false)
                }
              });
            }else if(respuesta.isDenied){
              abonarApartado(id_apartado);
            }
            
          })
        }else{
          Swal.fire({
            icon: 'error',
            text: response.mensaje
          })
        }
        
      }
    });

    
  }

  function cargarComprobanteEdicion(id, file_type){

    let area_canvas = $("#area-canvas");

    if (file_type == 'pdf') {
    // Ruta del PDF desde donde se obtendrá la miniatura
    var pdfURL = `./src/docs/gastos/GA${id}.${file_type}`;//'./src/docs/gastos/Folio RAY440.pdf'; // Reemplaza con la ruta correcta a tu archivo PDF
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    // Obtiene el elemento canvas
    area_canvas.empty().append(`<canvas id="thumbnailCanvas" width="100" height="150"></canvas>
    <div class="delete-thumbnail" onclick="deleteThumb()">x</div>
    `)
    var canvas = document.getElementById('thumbnailCanvas');

    // Carga el PDF
    pdfjsLib.getDocument(pdfURL).promise.then(function(pdfDoc) {
      // Obtiene la primera página del PDF (página 1)
      var pageNumber = 1;

      // Renderiza la página en el canvas
      pdfDoc.getPage(pageNumber).then(function(page) {
        var viewport = page.getViewport({ scale: 0.2 });
        var context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        var renderContext = {
          canvasContext: context,
          viewport: viewport,
        };
        
        page.render(renderContext).promise.then(function() {
     
        });
      });
    });
   // reader.readAsDataURL(file);
  }else if (file_type=='jpg' || file_type=='png' || file_type == 'jpeg' || file_type == 'JPG'){
    // Si es una imagen (cualquier tipo de imagen)
    var reader = new FileReader();
    area_canvas.empty().append(`<img src="" height="200" id="gasto-imagen">
    <div class="delete-thumbnail" onclick="deleteThumb()">x</div>`)
    let img_preview = document.getElementById('gasto-imagen')
    img_preview.src = `./src/docs/gastos/GA${id}.${file_type}`;
    Swal.resetValidationMessage()
     
  }else{
    area_canvas.empty()
    
  }
  }

  function deleteThumb(){
    let area_canvas = $("#area-canvas");
    area_canvas.empty()
    toastr.success('Eliminado, click en actualizar para guardar los cambios' ); 
    eliminar_comprobante = true;
  }