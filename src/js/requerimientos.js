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

  let rol_usuario = $("#emp-title").attr('sesion_rol')

  if(rol_usuario == 1 || rol_usuario == 4){
     texto_btn_requerimientos= 'Realizar movimiento'
     mostrar_btn_cancelar = true;
  }else{
     texto_btn_requerimientos= 'Aceptar'
     mostrar_btn_cancelar = false;

    
  }

  MostrarRequerimientos()
  function MostrarRequerimientos() {  
    //$.fn.dataTable.ext.errMode = 'none';
    ocultarSidebar();
  table = $('#requerimientos').DataTable({
      
    processing: true,
    serverSide: true,
    ajax: './modelo/requerimientos/historial-requerimientos.php',
    rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
        
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
        if(data[7] == 1){
          $(row).css('background-color','white')
        }/* else{
          $(row).css('background-color','#ffffbf')
        } */
      },
     
    columns: [   
    { title: "#",              data: null     },
    { title: "Folio",          data: 1        },
    { title: "Sucursal",   data: 2 },
    { title: "Fecha",    data: 3,  render: function(data, type, row) {
        if (data == null) {
          return '-';
        } else {
          const fechaFormateada = formatearFecha(data);
          return fechaFormateada;
        }
      }},
      { title: "Hora",    data: 4        },
      { title: "Usuario",        data: 5       },
      { title: "Comentario",        data: 8       },
      { title: "Estatus",    data: 7 , render: function(data, type, row){
        data = parseInt(data)
        switch(data){
          case 1:
          var estatus = 'Pendiente';
          var class_btn = 'warning'
          break;
          case 2:
          var estatus = 'Cancelado';
          var class_btn = 'danger'
          break;
          case 3:
          var estatus = 'Aprobado';
          var class_btn = 'info'
          break;
          case 4:
          var estatus = 'Entregada';
          var class_btn = 'success'
          break;
          case 5:
            var estatus = 'Aprobado parcialmente';
            var class_btn = 'primary'
            break;
          case 6:
              var estatus = 'Movida sistema';
              var class_btn = 'primary'
          break;  
          default:
            var estatus ='Sin información'
            var class_btn = 'secondary'
        }
        
          return `<div class="btn btn-${class_btn}" style="font-size: 12px !important;">${estatus}</div>`;
        
      }       },
   

    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        /* rol = $("#emp-title").attr("sesion_rol");
        if(rol == "1" || rol == '2'){ */
        return `<div class="btn btn-primary" onclick="verRequerimiento(${row[1]})">
        <i class="fa fa-eye"></i></div>`
      /*  }else{
        return '';
       } */
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
  
  //order: [1, "desc"],
  
  
  });
  //table.columns( [6] ).visible( true );
  $("table.dataTable thead").addClass("table-dark")
  
  }
  
  function ocultarSidebar(){
    let sesion = $("#emp-title").attr("sesion_rol");
  if(sesion == 4){
    $(".rol-4").addClass("d-none");
  
  }
  };

  function verRequerimiento(id_requerimiento){
    table.ajax.reload(null, false);
    $.ajax({
      type: "post",
      url: "./modelo/requerimientos/traer-requerimiento.php",
      data: {id_requerimiento},
      dataType: "JSON",
      success: function (response) {
        if(response.estatus){
          if(rol_usuario == 1 || rol_usuario == 4){
            if(!response.estatus_realizar_movimiento){
              texto_btn_requerimientos= 'Aceptar'
              mostrar_btn_cancelar = false;
            }else{
              texto_btn_requerimientos= 'Realizar movimiento'
              mostrar_btn_cancelar = true;
            }
         }
        
          Swal.fire({
            width: '1000px',
            title: 'Aprobar llantas',
            html:`
            <div class="container">
        <div class="row">
          <div class="col-12">
            
            <div class="form-group header-preview" style="border: 3px solid rgba(255,99,71,0.5); border-radius: 8px !important; padding:1em; background-color: whitesmoke;">
              <div class="row">
                <div class="col-9 text-left">
                  <label for="nombre"><b>Nombre del usuario:</b> </label>
                  <span id="nombre_usuario"></label>
                </div>
                <div class="col-3 text-left" style="border:2px solid #99EDC3; border-radius:7px; padding:1rem; background-color: white;">
                <label for="folio_mov"><b>Folio Movimiento:</b> </label>
                <span id="folio_mov">Sin movimiento</label>
              </div>
              </div>
              <div class="row">
                <div class="col-12 text-left">
                  <label><b>Sucursal: </b></label>
                  <span id="sucursal_usuario"></label>
                </div>
              </div>
              <div class="row">
                <div class="col-4 text-left">
                  <label for="fecha"><b>Fecha: </b></label>
                  <span id="fecha"></label>
                </div>
                <div class="col-3 text-left">
                  <label for=""><b>Hora: </b></label>
                  <span id="hora"></label>
                </div>
              </div>     
            </div>
            
            <div class="row">
                <div class="col-12">
                  <table id="detalle_requerimiento" class="table table-bordered" style="font-size:12px !important;"></div>
                </div>
            </div>  


          </div>
        </div>    
      </div>
            `,
            didOpen: () => {
        
                  $("#nombre_usuario").text(response.nombre_usuario);
                  $("#sucursal_usuario").text(response.sucursal);
                  const fecha_ft = formatearFecha(response.fecha); 
                  $("#fecha").text(fecha_ft);
                  $("#hora").text(response.hora);
                  if(response.ids_movimientos.length > 0){
                    $("#folio_mov").empty();
                   // Crear un Set para eliminar duplicados y luego convertirlo de nuevo a una matriz
                  let uniqueFolios = [...new Set(response.ids_movimientos)];

                  // Unir los folios únicos con comas y añadir un punto al final
                  let folios = uniqueFolios.join(', ') + '.';

                  // Agregar los folios al elemento con el id folio_mov
                  $("#folio_mov").append(folios);

                  }
                  rol = $("#emp-title").attr("sesion_rol");
                  
                  //Conversion de arreglo de objectos a arreglos de arrays
                  response.data = response.data.length == 0 ? [] : response.data;
                  const data_convertida = response.data.map((objeto) => {

                    if(objeto.id_movimiento == null) { objeto.id_movimiento == ''; var enlace_id_movimiento=''}else{
                      var enlace_id_movimiento = `<a target="_blank" href="./modelo/movimientos/remision-salida.php?id=${objeto.id_movimiento}">${objeto.id_movimiento}</a>`;
                    }
                      switch (objeto.estatus) {
                        case '1':
                        var estatus = 'Pendiente';
                        var class_btn = 'warning'
                        var aprobar_btns= `<div style="display:flex;"><div class='btn btn-success mr-2' title='Aprobar' onclick='procesarLlanta(${objeto.id},1)'><i class='fas fa-check'></i></div>
                        <div class='btn btn-danger' title='Cancelar' onclick='procesarLlanta(${objeto.id},2)'><i class='fas fa-ban'></i></div>
                        </div>`;
                        break;
                        case '2':
                        var estatus = 'Cancelado';
                        var class_btn = 'danger'
                        var aprobar_btns= `<div style="display:flex;"><div class='btn btn-primary mr-2' title='Regresar' onclick='procesarLlanta(${objeto.id},3)'><i class='fas fa-undo'></i></div>
                        </div>`;
                        break;
                        case '3':
                        var estatus = 'Aprobado';
                        var class_btn = 'info'
                        var aprobar_btns= `<div style="display:flex;"><div class='btn btn-primary mr-2' title='Aprobar' onclick='procesarLlanta(${objeto.id},4)'><i class='fas fa-undo'></i></div>
                        </div>`;
                        break;
                        case '4':
                        var estatus = 'Enviado';
                        var class_btn = 'primary'
                        var aprobar_btns ='';
                        break;
                        case '5':
                          var estatus = 'Recibido';
                          var class_btn = 'primary'
                          var aprobar_btns ='';
                        break;  
                        case '6':
                          var estatus = 'Entregada';
                          var class_btn = 'success'
                          var aprobar_btns ='';
                        break;  
                        case '7':
                          var estatus = 'No entregada';
                          var class_btn = 'dark'
                          var aprobar_btns ='';
                        break; 
                        case '8':
                          var estatus = 'No recibida';
                          var class_btn = 'dark'
                          var aprobar_btns ='';
                        break;  
                        case '9':
                          var estatus = 'Movida por sistema';
                          var class_btn = 'primary'
                          var aprobar_btns ='';
                        break;  
                        /* var aprobar_btns= `<div style="display:flex;"><div class='btn btn-primary mr-2' title='Aprobar' onclick='procesarLlanta(${objeto.id},5)'><i class='fas fa-undo'></i></div>
                        </div>`; */
                        break;
                        default:
                          var estatus ='Sin información'
                          var class_btn = 'secondary'
                          var aprobar_btns='';
                      }
                      let boton_est =  `<div class="btn btn-${class_btn}" style="font-size: 12px !important;">${estatus}</div>`
                      if(rol != '1' && rol != '4'){
                        aprobar_btns='';
                      }
                   

                    return [
                    objeto.cantidad,
                    objeto.descripcion,
                    objeto.marca,
                    objeto.sucursal_remitente,
                    objeto.sucursal_destino,
                    boton_est,
                    enlace_id_movimiento,
                    aprobar_btns

                ]});
        
                  table_requerimiento = $('#detalle_requerimiento').DataTable({
                    columns: [   
                    { title: 'Cantidad' },
                    {title:  'Descripcion'},
                    { title: 'Marca'},
                    { title: 'Ubicación'},
                    { title: 'Destino'},
                    { title: 'Estatus' },
                    { title: 'ID Movimiento' },
                    { title: 'Acción' },
                    ],
                    data: data_convertida,
                  });


            
            },

            confirmButtonText: texto_btn_requerimientos,
            showCancelButton:  mostrar_btn_cancelar,
            cancelButtonText: 'Cancelar'
          }).then(function(r){
            if(rol_usuario ==1||rol_usuario ==4){
              if(response.estatus_realizar_movimiento){
              if(r.isConfirmed){
                Swal.fire({
                  icon:'warning',
                  html: `
                    <div class="container">
                        <div class="row">
                          <div class="col-12 col-md-12">
                              <h3>¿Deseas generar el movimiento?</h3>
                              <p> Las llantas aprobadas generaran unn nuevo movimiento</p>
                          </div>
                        </div>
                    </div>
                  `,
                  showCancelButton: true,
                  confirmButtonColor: 'tomato',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Realizar'
                }).then(function(rr){
                  if(rr.isConfirmed){
                    $.ajax({
                      type: "post",
                      url: "./modelo/requerimientos/aprobar-llanta.php",
                      data: {id_requerimiento},
                      dataType: "json",
                      success: function (response) {
                        if(response.estatus){
                          Swal.fire({
                            icon:'success',
                            html: `<h3>${response.mensaje}</h3>`
                          })
                          table.ajax.reload(null, false);
                        }else{
                          Swal.fire({
                            icon:'error',
                            html: `<h3>${response.mensaje}</h3>`
                          })
                          table.ajax.reload(null, false);
                        }
                      }
                    });
                  }
                })
              }}
            }else{
              console.log('No administrador');
            }
          });
        }
      }
    });
  }

  function formatearFecha(fechaStr) {
    // Separar la cadena de fecha en año, mes y día
    const [ano, mes, dia] = fechaStr.split('-');

    // Crear un objeto Date a partir de los componentes de la fecha
    const fecha = new Date(ano, mes - 1, dia); // El mes se cuenta desde 0, por eso restamos 1

    // Obtener el día, mes y año
    const diaNumero = fecha.getDate();
    const nombreMes = obtenerNombreMes(fecha.getMonth());
    const anoNumero = fecha.getFullYear();

    // Crear la cadena formateada
    const fechaFormateada = `${diaNumero} de ${nombreMes} ${anoNumero}`;

    return fechaFormateada;
}

// Función para obtener el nombre del mes
function obtenerNombreMes(numeroMes) {
    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    return meses[numeroMes];
}

function procesarLlanta(id_detalle, tipo){
  if(tipo == 1){
    /* Swal.fire({
      icon:'question',
      html: `
        <div class="container">
            <div class="row">
              <div class="col-12 col-md-12">
                  <h3>¿Deseas aprobar la llanta?</h3>
                  <p>La llanta se movera al inventario correspondiente</p>
              </div>
            </div>
        </div>
      `,
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Aprobar y mover'
    }).then(function(res){ 
        if(res.isConfirmed){*/
          $.ajax({
            type: "post",
            url: "./modelo/requerimientos/aprobar-llanta-2.php",
            data: {id_detalle, tipo},
            dataType: "JSON",
            success: function (response) {
              if(response.estatus){
                icon = response.estatus == true ? 'success' : 'error';
                Swal.fire({
                  icon: icon,
                  title: response.mensaje
                }).then(()=>{
                  verRequerimiento(response.id_requerimiento)
                })
              }
              
            }
          });
       /*  }else{
          verRequerimiento(response.id_requerimiento)
        }
    }) */
  }else if(tipo == 2){
    /* Swal.fire({
      icon:'warning',
      html: `
        <div class="container">
            <div class="row">
              <div class="col-12 col-md-12">
                  <h3>¿Deseas desaprobar esta llanta?</h3>
                  <p>La llanta no se movera al inventario</p>
              </div>
            </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: 'tomato',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Desaprobar'
    }).then(function(res){
          if(res.isConfirmed){ */
            $.ajax({
              type: "post",
              url: "./modelo/requerimientos/aprobar-llanta-2.php",
              data: {id_detalle, tipo},
              dataType: "JSON",
              success: function (response) {
                icon = response.estatus == true ? 'success' : 'error';
                Swal.fire({
                  icon: icon,
                  title: response.mensaje
                }).then((r)=>{
                  if(r){
                    verRequerimiento(response.id_requerimiento)
                  }
                })
              }
            });
         /*  }else{
            verRequerimiento(response.id_requerimiento)
          }
    }) */
  }else if(tipo == 3){
    Swal.fire({
      icon:'warning',
      html: `
        <div class="container">
            <div class="row">
              <div class="col-12 col-md-12">
                  <h3>¿Deseas deshacer este cambio?</h3>
                  <p>Se actualizará estatus de la llanta</p>
              </div>
            </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#38d0c0',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Desaprobar'
    }).then(function(res){
          if(res.isConfirmed){ 
            $.ajax({
              type: "post",
              url: "./modelo/requerimientos/aprobar-llanta-2.php",
              data: {id_detalle, tipo},
              dataType: "JSON",
              success: function (response) {
                icon = response.estatus == true ? 'success' : 'error';
                Swal.fire({
                  icon: icon,
                  title: response.mensaje
                }).then((r)=>{
                  if(r){
                    verRequerimiento(response.id_requerimiento)
                  }
                })
              }
            });
          }else{
            verRequerimiento(response.id_requerimiento)
          }
    })
  }else if(tipo ==4){
    Swal.fire({
      icon:'question',
      html: `
        <div class="container">
            <div class="row">
              <div class="col-12 col-md-12">
                  <h3>¿Deseas deshacer este cambio?</h3>
                  <p>La llanta se regresará a su inventario original</p>
              </div>
            </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#38d0c0',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Deshacer'
    }).then(function(res){
          if(res.isConfirmed){
            $.ajax({
              type: "post",
              url: "./modelo/requerimientos/aprobar-llanta-2.php",
              data: {id_detalle, tipo},
              dataType: "JSON",
              success: function (response) {
                icon = response.estatus == true ? 'success' : 'error';
                Swal.fire({
                  icon: icon,
                  title: response.mensaje
                }).then((r)=>{
                  if(r){
                    verRequerimiento(response.id_requerimiento)
                  }
                })
              }
            });
          }else{
            verRequerimiento(response.id_requerimiento)
          }
    })
  }
 
}