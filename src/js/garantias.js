contador_general=0;
function buscarRay() {
  let folio = $("#folio").val();
  let btn_vender = $("#btn-buscar");
  const loader = `<lottie-player src="./src/img/load.json" background="transparent"  speed="1"  style="width: 20px; height: 20px; color:#e9bd15" loop autoplay></lottie-player>`;
  btn_vender.empty().append(loader);

  if (folio == undefined || folio.length <= 0) {
    Swal.fire({
      icon: "error",
      text: "Porfavor, escribe un folio",
    });
    btn_vender.empty().append(`<i class="fas fa-fw fa-search"></i>`);
  } else {
    $.ajax({
      type: "post",
      url: "./modelo/garantias/buscar-folio.php",
      data: { folio },
      dataType: "JSON",
      success: function (response) {
        if (response.estatus) {
          $("#nombre_cliente").val(response.nombre_cliente);
          $("#nombre_cliente").attr('id_cliente', response.id_cliente);
          $("#sucursal").val(response.id_sucursal);
          $("#sucursal").attr('id_sucursal', response.id_sucursal);
          let tbody = $("#detalle-garantia");
          tbody.empty();
          let contador = 1;
          response.data.forEach((element) => {
            tbody.append(`
                            <tr id="fila_llanta_${element.id}">
                                <td>${contador}</td>
                                <td>${element.id}</td>
                                <td><input value="${element.Cantidad}" class="form-control" id="input_llanta_${element.id}}" type="number"></td>
                                <td>${element.Descripcion}</td>
                                <td>${element.Marca}</td>
                                <td>${element.Precio}</td>
                                <td><input placeholder="Escribe el DOT de la llanta" value="" class="form-control" id="dot_llanta_${element.id}"></td>
                                <td><div class="btn btn-danger" onclick="retirarLlanta(${element.id})"><span class="fa fa-trash"></span></div></td>
                            </tr>
                        `);
            contador++;            
          });
          setButtonRegister(1);
        } else {
          Swal.fire({
            icon: "error",
            html: response.mensaje,
          });
        }
        btn_vender.empty().append(`<i class="fas fa-fw fa-search"></i>`);
      },
    });
  }
}

function retirarLlanta(id_llanta) {
  let tr = $("#fila_llanta_" + id_llanta);
  tr.remove();
  let tbody = $("#detalle-garantia");
  let num_rows = tbody.find("tr").length;
  if (num_rows == 0) {
    tbody.empty();
    tbody.append(`
        <tr>
           <td colspan="6" class="text-center">
                <small>No hay elementos en la tabla</small>
           </td>
        </tr>`);
    setButtonRegister(0);
  }
}

function setButtonRegister(flag) {
  let btn_garantia = $("#btn-reg-garantia");
  if (flag == 1) {
    btn_garantia.removeClass("btn-secondary disabled");
    btn_garantia.addClass("btn-success");
    btn_garantia.prop("disabled", false);
  } else {
    btn_garantia.removeClass("btn-success");
    btn_garantia.addClass("btn-secondary disabled");
    btn_garantia.prop("disabled", true);
  }
}

function registrarGarantia() {
  let btn_garantia = $("#btn-reg-garantia");
  let estado_boton = btn_garantia.prop("disabled");
  let comentario = $("#comentario-garantia").val();
  let cliente = $("#nombre_cliente").val();
  let folio_factura = $("#folio_factura").val();
  let id_venta = $("#folio").val()
  let id_cliente =  $("#nombre_cliente").attr('id_cliente');
  let sucursal = $("#sucursal").val();
  let id_sucursal = $("#sucursal").attr('id_sucursal', );
  let comprobante_garantia = document.getElementById('comprobante-entrega'); 
  var file =  comprobante_garantia.files[0];

  if (estado_boton != undefined) {
    let tbody = document.getElementById("detalle-garantia");
    // Obtén todas las filas dentro del tbody
    let filas = tbody.getElementsByTagName("tr");
    let datos = [];
    // Itera a través de las filas
    for (let i = 0; i < filas.length; i++) {
      // Obtén una referencia a la fila actual
      let fila = filas[i];

      // Obtén todas las celdas de la fila actual
      let celdas = fila.getElementsByTagName("td");

      // Ahora puedes acceder a los datos en cada celda específica
      let numero = celdas[0].textContent; // Obtén el contenido de la primera celda (índice 0)
      let codigo = celdas[1].textContent;
      let cantidad = celdas[2].getElementsByTagName("input")[0].value; // Obtén el valor del input en la segunda celda
      let descripcion = celdas[3].textContent;
      let marca = celdas[4].textContent;
      let precio = celdas[5].textContent;
      let dot = celdas[6].getElementsByTagName("input")[0].value; // Obtén el valor del input en la segunda celda

      if(dot.length<1){
        toastr.error(`Ingresa un DOT en la llanta: ${descripcion}`, 'ERROR' ); 
        return false;
      }else{
        datos.push({
          codigo: codigo,
          numero: numero,
          cantidad: cantidad,
          descripcion: descripcion,
          marca: marca,
          precio: precio,
          dot: dot
        });
      }
    }
      var datosJSON = JSON.stringify(datos);
      var formData = new FormData();
      formData.append('cliente', cliente);
      formData.append('comentario', comentario);
      formData.append('datos', datosJSON);
      formData.append('folio_factura', folio_factura);
      formData.append('sucursal', sucursal);
      formData.append('id_sucursal', id_sucursal);
      formData.append('id_cliente', id_cliente);
      formData.append('id_venta', id_venta);
      formData.append('comprobante', file);

    $.ajax({
      type: "post",
      url: "./modelo/garantias/registrar.php",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "JSON",
      success: function (response) {
        if(response.estatus){
            Swal.fire({
                icon: 'success',
                html: response.mensaje,
                confirmButtonText: 'Entendido',
                allowOutsideClick: false,
            }).then(function(){
              window.location.reload();
            })
        }
      },
    });
  }else{
    toastr.error('La tabla esta vacia, busque una venta' ); 

  }
}

tablaGarantias()
function tablaGarantias(){
     //$.fn.dataTable.ext.errMode = 'none';
     //ocultarSidebar();
     table = $('#garantias').DataTable({
       
     processing: true,
     serverSide: true,
     ajax: './modelo/garantias/historial-garantias.php',
     rowCallback: function(row, data, index) {
         var info = this.api().page.info();
         var page = info.page;
         var length = info.length;
         let estatus_dicaten = data.dictamen
         var columnIndex = 0; // Índice de la primera columna a enumerar
   
         $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
         console.log(data[9]);
         if(data[9] == 'entregado' || data[9] == 'procedente'){
           $(row).css('background-color','#c0f6b4')
          }else if(data[9] == 'pendiente'){
          $(row).css('background-color','#ffffbf')
        }else if(data[9]=='improcedente'){
          $(row).css('background-color','#FF6347')
          $(row).css('color','white')
        }else if(data[9]=='concluido'){
          $(row).css('background-color','gray')
          $(row).css('color','white')
        }
       },
      
     columns: [   
     { title: "#",              data: null   },
     { title: "Folio",              data: 0   },
     { title: "Cantidad",          data: 2      },
     { title: "DOT",      data: 4      },
     { title: "Descripcion",    data: 5      }, 
     { title: "Marca",          data: 6      },
     { title: "comentario inicial",     data: 7      },
     { title: "Analisis",        data: 8      },
     { title: "Dictamen",    data: 9     },
     { title: "Factura",       data: 12      },
     { title: "Sucursal",       data: 15      },
     { title: "Acción",
       data: null,
       className: "celda-acciones",
       render: function (row, data) {
         rol = $("#emp-title").attr("sesion_rol");
           if(rol == '1'){
             return `
             <button type="button" onclick="procesarGarantia(${row[0]});" title="Procesar garantia" class="buttonBorrar btn btn-success" style="margin-left: 8px">
             <span class="fa fa-check"></span><span class="hidden-xs"></span></button>
             <button type="button" onclick="pdfGarantia(${row[0]});" title="PDF garantia" class="buttonBorrar btn btn-danger">
             <span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button>
             <button type="button" onclick="compranteGarantia(${row[0]}, '${row[16]}');" title="comprobante garantia" class="buttonBorrar btn btn-info" style="margin-left: 8px">
             <span class="fa fa-file-image"></span><span class="hidden-xs"></span></button>
             `;
         }else{
             return `
             <button type="button" onclick="pdfGarantia(${row[0]});" title="PDF garantia" class="buttonBorrar btn btn-danger">
             <span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button>
             <button type="button" onclick="compranteGarantia(${row[0]}, '${row[16]}');" title="comprobante garantia" class="buttonBorrar btn btn-info" style="margin-left: 8px">
             <span class="fa fa-file-image"></span><span class="hidden-xs"></span></button>
             `;
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
   order: [8, "desc"],
   });
   //table.columns( [6] ).visible( true );
   $("table.dataTable thead").addClass("table-info")
   
}

function procesarGarantia(id_garantia){
    $.ajax({
        type: "POST",
        url: "./modelo/garantias/traer-garantia.php",
        data: {id_garantia},
        dataType: "JSON",
        success: function (response) {
          if(response.estatus){
            response.data.analisis = response.data.analisis == null ? '' : response.data.analisis;
            response.data.lugar_expedicion = response.data.lugar_expedicion == null ? '' : response.data.lugar_expedicion;
            Swal.fire({
              html:`
                  <div class="container">
                      <div class="row">
                          <div class="col-12">
                              <label>Cliente:</label>
                              <input class="form-control disabled" placeholder="cliente" id="cliente" value="${response.data.cliente}" disabled>
                          </div>
                      </div>   
                      <div class="row mt-3">
                          <div class="col-6">
                              <label>Dictamen:</label>
                              <select class="form-control" id="dictamen">
                                  <option value="pendiente">Pendiente</option>
                                  <option value="entregado">Entregado al proveedor</option>
                                  <option value="procedente">Procedente</option>
                                  <option value="improcedente">Improcedente</option>
                                  <option value="concluido">Concluido</option>
                              </select>
                          </div>
                          <div class="col-6">
                              <label>Fecha expedición:</label>
                              <input type="date" class="form-control" id="fecha_expedicion" value="${response.data.fecha_expedicion}">
                          </div>
                  </div>  
                  <div class="row mt-3">
                      <div class="col-12">
                          <label>Lugar de expedición:</label>
                          <input type="text" value="${response.data.lugar_expedicion}" class="form-control" id="lugar_expedicion" placeholder="Lugar expedición">
                      </div>
                  </div>
                  <div class="row mt-3">
                          <div class="col-12">
                              <label>Analisis</label>
                              <textarea class="form-control" placeholder="Determinación que tuvó la garantia de la llanta" id="analisis">${response.data.analisis}</textarea>
                          </div>
                  </div>
                  </div>    
              `,
              showConfirmButton: true,
              confirmButtonText: 'Actualizar',
              didOpen: ()=>{
                if(response.data.dictamen!=null){
                  $("#dictamen").val(response.data.dictamen)
                }else{
                  $("#dictamen").val('pendiente')
                }
                 
              }
          }).then(function(res){
            if(res.isConfirmed){
              let analisis = $("#analisis").val();
              let dictamen = $("#dictamen").val();
              let fecha_expedicion = $("#fecha_expedicion").val();
              let lugar_expedicion = $("#lugar_expedicion").val();
              $.ajax({
                type: "POST",
                url: "./modelo/garantias/actualizar.php",
                data: {analisis, dictamen, fecha_expedicion, lugar_expedicion, id_garantia},
                dataType: "JSON",
                success: function (response) {
                  if(response.estatus){
                    Swal.fire({
                      icon:'success',
                      html: response.mensaje
                    })

                    table.ajax.reload(null, false)
                  }
                }
              });
            }
          })
          }
          
        }
    });
}

function pdfGarantia(id_garantia){
  window.open("./modelo/garantias/generar-dictamen.php?id="+id_garantia)
}

function compranteGarantia(id_garantia, file_extension){
  window.open("./src/docs/garantias/GT"+id_garantia+'.'+file_extension)
}

function cargarComprobanteRegistro(){
  let input_comprobante = document.getElementById('comprobante-entrega');
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

function deleteThumb(){
  let area_canvas = $("#area-canvas");
  area_canvas.empty()
  toastr.success('Eliminado, click en actualizar para guardar los cambios' ); 
  eliminar_comprobante = true;
}

function cambiarGarantiaSinFolio(){
  let garantia_switch = $('#garantia-sin-folio').prop('checked');
  if(garantia_switch){
    $('#sucursal').prop('disabled', false);
    $('#contenedor-datos-llanta').removeClass('d-none')
   
  }else{
    $('#sucursal').prop('disabled', true);
    $('#sucursal').val('')
    $('#contenedor-datos-llanta').addClass('d-none')
  };
}

buscar();
function buscar() {
        
  $('#search').select2({
      placeholder: "Selecciona una llanta",
      theme: "bootstrap",
      minimumInputLength: 1,
      ajax: {
          url: "./modelo/ventas/buscar-llantas-nueva-venta.php" ,
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
          
           if(params.term == undefined){
            params.term = "";
          }else{
            var id_sucursal = $("#sucursal").val();
            var rol = $("#emp-title").attr('sesion_rol');
          }
        
           return {
             searchTerm: params.term, // search term
             id_sucursal: id_sucursal,
             page: params.page || 1,
             rol: rol
             
           };
          },
         
          cache: true

      }, processResults: function (data, params) {
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
          "<div style='' class='select2-result-repository clearfix' desc='"+repo.Descripcion+" marca='"+repo.Marca +
          "' costo='"+repo.precio_Inicial +" id='tyre"+repo.id+"' precio='"+repo.precio_Venta+" idcode='"+repo.id+"'>" +
          "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
          "<div class='col-md-2 justify-content-center'><img loading='lazy' class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.Marca + ".jpg' /></div>" +
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
          let btn_add = $('#btn-agregar-llanta');
          btn_add.attr('id_llanta', repo.id)
          btn_add.attr('descripcion', repo.Descripcion)
          btn_add.attr('marca', repo.Marca)
          btn_add.attr('precio', repo.precio_Venta)
          return repo.text || repo.Descripcion;
    
    }
}

function agregarLlantaSinVenta(){
  let tbody = $("#detalle-garantia");

  if(contador_general==0){
    tbody.empty()
  }
  let btn_add = $('#btn-agregar-llanta');
  let id_llanta = btn_add.attr('id_llanta')
  let descripcion = btn_add.attr('descripcion')
  let marca = btn_add.attr('marca')
  let precio = btn_add.attr('precio')
  let dot = $("#dot-llanta").val()
  let cantidad = $("#cantidad-llantas").val()
  contador_general++;
  let response =[{
    contador: contador_general,
    id: id_llanta,
    Descripcion: descripcion,
    Marca: marca,
    Precio: precio,
    Cantidad: cantidad
  }]
  response.forEach((element) => {
    tbody.append(`
    <tr id="fila_llanta_${element.id}">
    <td>${element.contador}</td>
    <td>${element.id}</td>
    <td><input value="${element.Cantidad}" class="form-control" id="input_llanta_${element.id}}" type="number"></td>
    <td>${element.Descripcion}</td>
    <td>${element.Marca}</td>
    <td>${element.Precio}</td>
    <td><input placeholder="Escribe el DOT de la llanta" value="" class="form-control" id="dot_llanta_${element.id}"></td>
    <td><div class="btn btn-danger" onclick="retirarLlanta(${element.id})"><span class="fa fa-trash"></span></div></td>
    </tr>
    `);         
  });
  setButtonRegister(1);
}

