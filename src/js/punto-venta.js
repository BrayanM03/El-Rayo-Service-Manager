traerAnchos();
const audio = new Audio("./src/sounds/success-sound.mp3");
const audio_error = new Audio("./src/sounds/error-sound.mp3");
audio.volume = 0.5;
audio_error.volume = 0.5;
toastr.options = {
  closeButton: true,
  debug: false,
  newestOnTop: false,
  progressBar: true,
  positionClass: "toast-bottom-right",
  preventDuplicates: false,
  onclick: null,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "5000",
  extendedTimeOut: "1000",
  showEasing: "swing",
  hideEasing: "linear",
  showMethod: "fadeIn",
  hideMethod: "fadeOut",
};
let rol_usuario = $("#emp-title").attr("sesion_rol");

const checkboxes = document.querySelectorAll('input[type="checkbox"]');

// Recorre cada checkbox y verifica si está marcado
checkboxes.forEach((checkbox) => {
  checkbox.addEventListener("change", () => {
    // Verifica si el checkbox está marcado o no
    buscarNeumaticoPuntoVenta();
  });
});

//Funciones para el punto de venta
/* document.querySelector('#cliente').setAttribute('title', 'loool');
document.querySelector('#cliente').value =775 */

//Funcion que carga el loader
function load(flag) {
  if (flag) {
    $("#contenedor-loader").removeClass("d-none"); //True desaparece animacion
  } else {
    $("#contenedor-loader").addClass("d-none");
  }
}

//Cargando medidas
function traerAnchos() {
  load(true);

  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/filtros-medidas.php",
    data: { tabla: "llantas", fuente: "Ancho" },
    dataType: "json",
    success: function (response) {
      if (response.estatus) {
        $("#ancho").empty();
        $("#ancho").append('<option value="">Seleccione un ancho</option>');
        response.medidas.forEach((element) => {
          $("#ancho").append(`<option value="${element}">${element}</option>`);
        });
        $("#ancho").selectpicker("refresh");
        setTimeout(() => {
          load(false);
        }, 700);
      }
    },
  });
}

function cargarAltos() {
  let ancho = $("#ancho").val();
  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/filtros-medidas.php",
    data: { tabla: "llantas", fuente: "Proporcion", ancho: ancho },
    dataType: "json",
    success: function (response) {
      if (response.estatus) {
        $("#alto").prop("disabled", false);
        $("#alto").removeClass("disabled form-field");
        $("#alto").addClass("selectpicker");
        $("#alto").addClass("w-100");
        $("#alto").attr("data-live-search", "true");
        $("#alto").attr("onchange", "cargarRines()");
        $("#alto").empty();
        $("#alto").append('<option value="">Seleccione un alto</option>');
        response.medidas.forEach((element) => {
          $("#alto").append(`<option value="${element}">${element}</option>`);
        });
        $("#alto").selectpicker("refresh");
        setTimeout(() => {
          load(false);
        }, 700);
      }
    },
  });
 
}

function cargarRines() {
  let ancho = $("#ancho").val();
  let alto = $("#alto").val();

  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/filtros-medidas.php",
    data: { tabla: "llantas", fuente: "Diametro", ancho: ancho, alto: alto },
    dataType: "json",
    success: function (response) {
      if (response.estatus) {
        $("#rin").prop("disabled", false);
        $("#rin").removeClass("disabled form-field");
        $("#rin").addClass("selectpicker");
        $("#rin").addClass("w-100");
        $("#rin").attr("data-live-search", "true");
        $("#rin").attr("onchange", "buscarNeumaticoPuntoVenta()");
        $("#rin").empty();
        $("#rin").append('<option value="">Seleccione un alto</option>');
        response.medidas.forEach((element) => {
          $("#rin").append(`<option value="${element}">${element}</option>`);
        });
        $("#rin").selectpicker("refresh");
        setTimeout(() => {
          load(false);
        }, 700);
      }
    },
  });
}

//Buscador automatico basado en filtros
function buscarNeumaticoPuntoVenta() {
  let ancho = $("#ancho").val();
  let alto = $("#alto").val();
  let diametro = $("#rin").val();
  let id_cliente = localStorage.getItem('id_cliente');

  if (ancho == "" || alto == "" || diametro == "") {
    Swal.fire({
      icon: "error",
      title: "Completa los filtros de medidas porfavor",
      confirmButtonText: "Enterado",
    });
    return false;
  }

  //filtros
  // Obtén todos los checkbox en el formulario (puedes especificar un contenedor o clase específica si es necesario)
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');

  // Inicializa un array para almacenar los ids de los checkboxes seleccionados
  let checkedIds = [];

  // Recorre cada checkbox y verifica si está marcado
  checkboxes.forEach((checkbox) => {
    if (checkbox.checked) {
      // Si está marcado, añade su id al array
      checkedIds.push(checkbox.id);
    }
  });

  load(true);
  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/buscar.php",
    data: { ancho, alto, diametro, filtros: checkedIds, id_cliente},
    dataType: "json",
    success: function (response) {
      $("#contenedor-resultados-llantas").empty();
      $("#titulo-busqueda").text("Resultados: ");
      if (response.estatus) {
        response.datos.forEach((element) => {
          if (element.url_principal == null) {
            src_imagen_llanta = "./src/img/neumaticos/NA.JPG";
          } else {
            src_imagen_llanta = `./src/img/neumaticos/${element.url_principal}`;
          }

          let precio = Intl.NumberFormat("es-MX", {
            style: "currency",
            currency: "MXN",
          }).format(element.precio_Venta);
          let precio_lista = Intl.NumberFormat("es-MX", {
            style: "currency",
            currency: "MXN",
          }).format(element.precio_lista);

          let precio_mayoreo = Intl.NumberFormat("es-MX", {
            style: "currency",
            currency: "MXN",
          }).format(element.precio_Mayoreo);
          if (element.promocion == 1) {
            precio_promocion = Intl.NumberFormat("es-MX", {
              style: "currency",
              currency: "MXN",
            }).format(element.precio_promocion);
            display_promo = "";
          } else {
            precio_promocion = 0;
            display_promo = "d-none";
          }
          let sucursal_producto = element.id_sucursal;
          let sucursal_usuario = $("#emp-title").attr("sesion_sucursal_id");
      
          if (sucursal_usuario != sucursal_producto && rol_usuario == 1) {
            display_btn_add = "d-none";
          } else if (
            sucursal_usuario != sucursal_producto &&
            rol_usuario != 1
          ) {
            display_btn_add = "d-none";
          } else if (sucursal_usuario == sucursal_producto) {
            display_btn_add = "d-flex";
          }
         

          let precio_final_desc;
          if(element.cliente_mayoreo){
            etiqueta_precio = 'Precio mayoreo';
            tipo_cliente = 'mayoreo'
            precio_final_desc=precio_mayoreo;
            background_color_precio = '#a03472';
          }else{
            etiqueta_precio = 'Precio normal desc.'
            precio_final_desc=precio
            background_color_precio = '#12a18e';
          }

          $("#contenedor-resultados-llantas").append(`
   <div class="card mb-3 card_busqueda" onclick="previsualizarNeumatico(${element.id})" style="border-radius: 10px; overflow: hidden;">
       <img class="${display_promo}" src="./src/img/promo-image.png" 
           style="width: 80px; position: absolute; z-index: 999; bottom: -0.8rem; right: 0.5rem;">
       <article class="tire-teaser">
           <div class="row">
               <!-- Imagen del neumático -->
               <div class="col-12 col-sm-4 col-md-2 text-center">
                   <a>
                       <img alt="Imagen del neumático" src="${src_imagen_llanta}" 
                            class="mt-4 img-fluid" style="max-width: 150px; border-radius: 10px;">
                   </a>
               </div>
               <!-- Información -->
               <div class="col-12 col-sm-8 col-md-10 tire-teaser__right mt-3 mb-3">
                   <div class="row">
                       <!-- Descripción -->
                       <div class="col-12 col-md-7 centrado_telefono_descripcion">
                           <h4><b>${element.Descripcion}</b></h4>
                       </div>
                       <!-- Botones de cantidad y agregar -->
                       <div class="col-12 col-md-4 m-2 altura_boton_agregar justificacion_der_telefono ${display_btn_add}">
                           <div class="d-flex align-items-center">
                               <button class="btn btn-info" style="border-radius: 10px 0px 0px 10px;" 
                                   onclick="aumentarCantidad(0,'cantidad_id_${element.Codigo}', ${element.id}, ${element.id_sucursal}, '${element.codigo}', event, 0)">
                                   <b>-</b>
                               </button>
                               <input type="number" id="cantidad_id_${element.Codigo}" class="form-control text-center" 
                                   value="1" style="width: 60px; border-radius: 0px;" onclick="event.stopPropagation();">
                               <button class="btn btn-info" style="border-radius: 0px 10px 10px 0px;" 
                                   onclick="aumentarCantidad(1,'cantidad_id_${element.Codigo}', ${element.id}, ${element.id_sucursal}, '${element.codigo}', event, 0)">
                                   <b>+</b>
                               </button>
                           </div>
                           <button class="btn btn-warning ml-2" style="border-radius: 10px;" 
                               onclick="agregarPreventa(${element.id}, '${element.Codigo}', ${element.id_sucursal}, 1, 'cantidad_id_${element.Codigo}', event, 0, 1, 0)">
                               Agregar
                           </button>
                       </div>
                   </div>
                   <!-- Detalles -->
                   <div class="row mt-2">
                       <div class="col-12 col-md-9">
                          <div class="justificacion_izq_telefono">
                           <span><b>Sucursal:</b> ${element.nombre}</span><br>
                           <span><b>Stock:</b> ${element.Stock}</span>
                           <span class="ml-3"><b>Codigo:</b> ${element.Codigo}</span>
                           </div>
                           <div class="row p-2 mt-2 tarjetas-precios" codigo="${element.Codigo}" id="tarjeta-codigo-${element.Codigo}">
                               <!-- Precio lista -->
                               <div class="margenes-col-precio text-center">
                                   <div id="tarjeta-lista" onclick="selectorPrecio(event, this, '${element.Codigo}')" style="background-color: #4682b4; color: white; border-radius: 8px;" class="boton_precio_estilos p-2">
                                       <span>Precio lista</span><br>
                                       <h3><b>${precio_lista}</b></h3>
                                       <img src="./src/img/checked.png" class="checked-icon-lista checked-icon d-none" id="icon-checked-lista-${element.Codigo}">
                                   </div>
                               </div>
                               <!-- Precio -->
                               <div  class="margenes-col-precio ml-2 text-center">
                                   <div id="tarjeta-precio" onclick="selectorPrecio(event, this, '${element.Codigo}')" style="background-color: ${background_color_precio}; color: white; border-radius: 8px;" class="boton_precio_estilos p-2">
                                       <span>${etiqueta_precio}</span><br>
                                       <h3><b>${precio_final_desc}</b></h3>
                                       <img src="./src/img/checked.png" class="checked-icon-precio checked-icon d-none" id="icon-checked-precio-${element.Codigo}">
                                       </div>
                               </div>
                               <!-- Promoción -->
                               <div class="margenes-col-precio ml-2 text-center ${display_promo}">
                                   <div id="tarjeta-promocion" onclick="selectorPrecio(event, this, '${element.Codigo}')" style="background-color: #FF7F50; color: white; border-radius: 8px;" class="boton_precio_estilos p-2">
                                       <span>Promoción</span><br>
                                       <h3><b>${precio_promocion}</b></h3>
                                       <img src="./src/img/checked.png" class="checked-icon-promo checked-icon d-none" id="icon-checked-promo-${element.Codigo}">
                                   </div>
                               </div>
                           </div>
                       </div>
                       <!-- Marca -->
                       <div class="col-12 col-md-3 text-center mt-3">
                           <div>
                               <span><b>Marca:</b> ${element.Marca}</span><br>
                               <img alt="Logo de la marca" src="./src/img/logos/${element.Marca}.jpg" 
                                    class="img-fluid" style="max-width: 100px; border-radius: 10px;">
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </article>
   </div>
`);

        });
      } else {
        $("#contenedor-resultados-llantas").append(`
                <div class="card mb-3 p-5" style="border-radius: 15px !important; box-shadow: rgba(0,0,0,0.1) 0 0 30px 0;">
                    ${response.mensaje}
                </div>
                `);
      }

      setTimeout(function () {
        load(false);
      }, 800);
    },
  });
}

//Funcion que previsualiza información detallada del producto neumatico
function previsualizarNeumatico(id_llanta) {
  let body = document.getElementsByTagName("body")[0];
  body.style.overflow = "hidden";
  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/informacion-neumatico.php",
    data: { id_llanta },
    dataType: "json",
    success: function (response) {
      $("#contenedor-loader").removeClass("d-none");
      $("#contenedor-loader-2").addClass("d-none");
      let kg_max_2 =
        response.datos.kg_max_2 == null
          ? ""
          : "/" + response.datos.kg_max_2 + "kg";
      $("#contenedor-loader").append(`
                    <div class="card animate__animated animate__fadeInDown p-5 prevista_neumatico mt-5">
                        <div class="row justify-content-end" style="margin-top:-40px !important; margin-right:-40px">
                            <div class="col-4 col-md-2 text-right">
                                <h2 class="boton-close-preview-neumatico" onclick="cerrarModalPrevista()"><b><i class="fas fa-window-close"></i></b></h2>
                            </div>
                        </div>
                        <div class="row mt-4">
                                <div class="col-12 col-md-8">
                                    <div id="contenedor-imagenes-preview"></div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h4><b>${response.datos.descripcion} ${response.datos.marca}</b></h4>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6">
                                            Aplicación de la llanta<br>
                                            <i class="fas fa-road"></i> ${response.datos.nombre_aplicacion}
                                        </div>
                                        <div class="col-12 col-md-6">
                                            Tipo de carga<br>
                                            <i class="fas fa-truck"></i> ${response.datos.nombre_tipo_carga}
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6">
                                            Rango de carga<br>
                                            <i class="fas fa-truck-loading"></i> ${response.datos.kg_max_1} kg ${kg_max_2}
                                        </div>
                                        <div class="col-12 col-md-6">
                                            Rango de velocidad<br>
                                            <i class="fas fa-clock"></i></i> ${response.datos.velocidad_max} 
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6">
                                            Presión maxima<br>
                                            <i class="fas fa-clock"></i> ${response.datos.psi}
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <span><b>Marca:</b> ${response.datos.marca}</span>
                                            <img alt="" src="./src/img/logos/${response.datos.marca}.jpg" style="width:96px; border-radius:10px">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-12">
                                            <b>Descripción del uso:</b><br>
                                            ${response.datos.descripcion_aplicacion}
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-12">
                                            <b>Descripción del tipo de vehiculo:</b><br>
                                            ${response.datos.descripcion_tipo_carga}
                                        </div>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                `);

      let element = response.datos.urls ?? {
        url_principal: null,
        url_frontal: null,
        url_perfil: null,
        url_piso: null,
      };

      let url_principal = element.url_principal ?? "NA.JPG";
      let url_frontal = element.url_frontal ?? "NA.JPG";
      let url_perfil = element.url_perfil ?? "NA.JPG";
      let url_piso = element.url_piso ?? "NA.JPG";

      $("#contenedor-imagenes-preview").append(`
        <div class="row justify-content-center">
            <div class="col-3 col-md-4 mb-3">
                <img class="img-fluid small" src="./src/img/neumaticos/${url_principal}" alt="Vista principal" />
            </div>
            <div class="col-3 col-md-4 mb-3">
                <img class="img-fluid" src="./src/img/neumaticos/${url_frontal}" alt="Vista frontal" />
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-3 col-md-4 mb-3">
                <img class="img-fluid" src="./src/img/neumaticos/${url_perfil}" alt="Vista lateral" />
            </div>
            <div class="col-3 col-md-4 mb-3">
                <img class="img-fluid" src="./src/img/neumaticos/${url_piso}" alt="Vista del piso" />
            </div>
        </div>
    `);
    },
  });
}

//Cerrar modal prevista
function cerrarModalPrevista() {
  let body = document.getElementsByTagName("body")[0];
  body.style.overflow = "";
  $("#contenedor-loader").addClass("d-none");
  $("#contenedor-loader").empty();
  $("#contenedor-loader").append(`
    <div id="contenedor-loader-2">
    <div class="option-card text-center loader-principal">
        <dotlottie-player src="https://lottie.host/9ff4fc94-43ef-467b-aaf1-dafe7abaf53b/GFRYdl5GrJ.json" background="transparent" speed="1" style="width: 250px; height: 250px" loop autoplay></dotlottie-player>
    </div>  
    <span class="span-text-loader">Cargando
    </span>
    <div class="dots ml-2">
            <div class="dot">&#9679;</div>
            <div class="dot">&#9679;</div>
            <div class="dot">&#9679;</div>
        </div>
</div>
    `);
}

//Aumentar cantidad en los productos
function aumentarCantidadServicio(
  tipo,
  id_input,
  id_llanta,
  id_sucursal,
  codigo,
  e = null,
  agregar_preventa = 0,
  promocion
) {
  if (e != null) {
    e.stopPropagation();
  }
  let valorActual = $(`#${id_input}`).val();
  valorActual = parseInt(valorActual);
  if (tipo == 0 && valorActual == 1) {
    toastr.warning("La cantidad no puede se 0", "Advertencia");
    return false;
  }
  if (tipo == 0 && valorActual > 1) {
    var nuevo_valor = valorActual - 1;
  }
  if (tipo == 1) {
    var nuevo_valor = valorActual + 1;
  }

  $(`#${id_input}`).val(nuevo_valor);
  if (agregar_preventa) {
    agregarPreventa(
      id_llanta,
      codigo,
      0,
      2,
      "cantidad_preventa_" + codigo,
      e,
      1,
      tipo,
      promocion,
      true //comparar con precio unitario de productos-preventa
    );
  }
}

function aumentarCantidad(
  tipo,
  id_input,
  id_llanta,
  id_sucursal,
  codigo,
  e = null,
  agregar_preventa = 0,
  promocion =0
) {
  if (e != null) {
    e.stopPropagation();
  }
  let valorActual = $(`#${id_input}`).val();
  valorActual = parseInt(valorActual);
  if (tipo == 0 && valorActual == 1) {
    toastr.warning("La cantidad no puede se 0", "Advertencia");
    return false;
  }
  if (tipo == 0 && valorActual > 1) {
    var nuevo_valor = valorActual - 1;
  }
  if (tipo == 1) {
    var nuevo_valor = valorActual + 1;
  }

  comprobarStock(nuevo_valor, id_llanta, id_sucursal)
    .then((respuesta_stock) => {
      if (respuesta_stock.estatus) {
        $(`#${id_input}`).val(nuevo_valor);
        if (agregar_preventa == 1) {
          agregarPreventa(
            id_llanta,
            codigo,
            id_sucursal,
            1,
            id_input,
            null,
            1,
            tipo,
            promocion,
            true
          );
        }
      } else {
        toastr.warning(respuesta_stock.mensaje, "Stock insuficiente");
      }
    })
    .catch((error) => {
      toastr.error("Ocurrio un error" + error, "Error");
    });
}

function comprobarStock(cantidad, id_llanta, id_sucursal) {
  return new Promise((resolve, reject) => {
    $.ajax({
      type: "post",
      url: "./modelo/punto_venta/comprobar-stock.php",
      data: { cantidad, id_llanta, id_sucursal },
      dataType: "json",
      success: function (response) {
        if (response.estatus) {
          resolve({
            estatus: response.estatus, // true o false
            mensaje: response.mensaje, // mensaje adicional
          }); // Stock suficiente
        } else {
          resolve({
            estatus: response.estatus, // true o false
            mensaje: response.mensaje, // mensaje adicional
          }); // No hay suficiente stock
        }
      },
      error: function (error) {
        reject(error); // Manejo de errores en la llamada AJAX
      },
    });
  });
}

function cargarPreventa() {
  $("#productos-preventa").empty().append(`
        <div class="row border mx-2">
            <div class="col-12 text-center">
                    <img src="./src/img/preload.gif" style="width:100px;">
                </div>
            </div>`);

  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/cargar-preventa.php",
    data: { data: "data" },
    dataType: "json",
    success: function (response) {
      $("#productos-preventa").empty();
      if (!response.estatus) {
        $("#contador-items-carrito").text(0)
        $("#productos-preventa").append(`
            <div class="row mt-5 mx-2">
                <div class="col-12 text-center">
                    <img src="./src/img/empty-box.png" style="width:100px;">
                   <h5 style="color:#82addc;" class="mt-3">No hay productos agregados<h5>
                </div>
            </div>`);

        $("#area-importe-procesar").addClass("d-none");

      } else {
        let numero_items = response.data.partidas.length;
        $("#contador-items-carrito").text(numero_items)

        $("#area-importe-procesar").removeClass("d-none");
        response.data.partidas.forEach((element) => {
          let precio = new Intl.NumberFormat().format(element.precio);
          let importe = new Intl.NumberFormat().format(element.importe);
          let cantidad_caracteres = importe.length;
          let font_size;
          if (cantidad_caracteres >= 6) {
            font_size = "1rem";
          } else {
            font_size = "1.5rem";
          }
          if (element.tipo == 1) {
            $("#productos-preventa").append(`
                    <div class="row mx-2">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <img src="./src/img/neumaticos/llanta_${element.id_llanta}_1.png" style="width:100px !important;"
                            onerror="this.src='./src/img/neumaticos/NA.JPG';" alt="Imagen de ${element.descripcion}"/>
                        </div>
                        <div class="col-12 col-md-8 p-2">
                            <div class="row">
                                <div class="col-12 p-2">
                                    <span> ${element.descripcion}</span><br>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-12 col-md-7 p-2" id="area-precio-unitario-${element.codigo}">
                                    <span><b>$${precio} c/u</b></span><br>
                                    <span><i class="fas fa-store"></i> ${element.sucursal_nombre}</span>
                                </div>
                                <div class="col-12 col-md-5 text-end p-2">
                                    <span>${element.marca}</span><br>
                                    <img alt="" src="./src/img/logos/${element.marca}.jpg" style="width:60px; border-radius:10px">
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-md-6 text-center">
                            <input type="text" class="form-control d-none" value="$2,595">
                            <div class="row">
                                <div onclick="generarToken('${element.codigo}')" class="btn btn-sm btn-warning mx-3" style="height: 95%; margin-top:5px"><i class="fas fa-lock"></i></div>
                                 <h4 style="color: tomato; margin-top:.5rem; font-size: ${font_size}"><b>$${importe}</b></h4>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div style="display:flex">
                                <div class="btn btn-info" onclick="aumentarCantidad(0,'cantidad_preventa_${element.codigo}', ${element.id_llanta}, ${element.id_sucursal}, '${element.codigo}', event,1, '${element.codigo}', ${element.promocion})" style="border-radius:10px 0px 0px 10px !important;" ><b>-</b></div>
                                    <input type="number" disabled id="cantidad_preventa_${element.codigo}" style="border-radius:0px !important; background-color:white !important;" class="form-control" placeholder="0" value="${element.cantidad}">
                                <div class="btn btn-info"  onclick="aumentarCantidad(1,'cantidad_preventa_${element.codigo}', ${element.id_llanta}, ${element.id_sucursal}, '${element.codigo}', event,1, '${element.codigo}', ${element.promocion})" style="border-radius:0px 10px 10px 0px !important;"><b>+</b></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <h3 class="boton-close-preview-neumatico" alt="Borrar producto" onclick="borrarProducto(${element.id})"><b><i class="fas fa-trash"></i></b></h3>
                        </div>
                    </div>
                </div>
                <hr>
                    `);
          } else if (element.tipo == 2) {
            $("#productos-preventa").append(`
                    <div class="row mx-2">
                    <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <img src="./src/img/services/${element.modelo}.png" style="width:80px;"
                            onerror="this.src='./src/img/neumaticos/NA.JPG';" alt="Imagen de ${element.descripcion}"/>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="row">
                                <div class="col-12 text-end p-2">
                                <span>${element.descripcion}</span><br>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-12 col-md-7 p-2" id="area-precio-unitario-${element.codigo}">
                                    <span><b>$${precio} c/u</b></span>
                                </div>
                                <div class="col-12 col-md-5 text-end p-2">
                                </div>
                             </div>
                        </div>
                    </div>
                   <div class="row mt-3">
                        <div class="col-12 col-md-6 text-center">
                            <input type="text" class="form-control d-none" value="$2,595">
                            <div class="row">
                                <div onclick="generarToken('${element.codigo}')" class="btn btn-sm btn-warning mx-3" style="height: 95%; margin-top:5px"><i class="fas fa-lock"></i></div>
                                 <h4 style="color: tomato; margin-top:.5rem; font-size: ${font_size}"><b>$${importe}</b></h4>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div style="display:flex">
                                <div class="btn btn-info" onclick="aumentarCantidadServicio(0,'cantidad_preventa_${element.codigo}', ${element.id_llanta}, ${element.id_sucursal}, '${element.codigo}', event,1, 0)" style="border-radius:10px 0px 0px 10px !important;" ><b>-</b></div>
                                    <input type="number" disabled id="cantidad_preventa_${element.codigo}" style="border-radius:0px !important; background-color:white !important;" class="form-control" placeholder="0" value="${element.cantidad}">
                                <div class="btn btn-info"  onclick="aumentarCantidadServicio(1,'cantidad_preventa_${element.codigo}', ${element.id_llanta}, ${element.id_sucursal}, '${element.codigo}', event,1, 0)" style="border-radius:0px 10px 10px 0px !important;"><b>+</b></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <h3 class="boton-close-preview-neumatico" alt="Borrar producto" onclick="borrarProducto(${element.id})"><b><i class="fas fa-trash"></i></b></h3>
                        </div>
                    </div>
                </div>
                </div>
                <hr>
                    `);
          }
        });
        let importe_total = new Intl.NumberFormat().format(
          response.data.importe
        );
        localStorage.setItem("importe_total", response.data.importe);
        $("#importe-total").text("$" + importe_total);
      }
    },
  });
}

function aplicarPromocion(id_producto,
  codigo,
  id_sucursal,
  tipo,
  id_input,
  e = null,
  ocultar_sidebar,
  sumar_restar_cantidad,
  promocion =0){
    if (e != null) {
      e.stopPropagation();
    }
if(promocion==0){
  agregarPreventa(
    id_producto,
    codigo,
    id_sucursal,
    tipo,
    id_input,
    e,
    ocultar_sidebar,
    sumar_restar_cantidad,
    0,
    false
  )
}else{
  Swal.fire({
    icon:'question',
    title:'¿Aplicar promoción?',
    showDenyButton:true,
    confirmButtonText: 'Si',
    denyButtonText: 'No'
  }).then(function(r){
    if(r.isConfirmed){
      agregarPreventa(
        id_producto,
        codigo,
        id_sucursal,
        tipo,
        id_input,
        e,
        ocultar_sidebar,
        sumar_restar_cantidad,
        promocion,
        false
      )

    }else if(r.isDenied){
      agregarPreventa(
        id_producto,
        codigo,
        id_sucursal,
        tipo,
        id_input,
        e,
        ocultar_sidebar,
        sumar_restar_cantidad,
        0,
        false
      )
    }
  })
}
}


function agregarPreventa(
  id_producto,
  codigo,
  id_sucursal,
  tipo, //1 producto - 2 servicio
  id_input,
  e = null,
  ocultar_sidebar,
  sumar_restar_cantidad,
  promocion =0,
  comparar_pu_preventa
) {
  if (e != null) {
    e.stopPropagation();
  }


  if(promocion ==0 && tipo ==1){
    let tarjeta_seleccionada = document.getElementById('tarjeta-codigo-'+codigo)
       precio_seleccionado =  tarjeta_seleccionada.hasAttribute('precio_seleccionado') ? tarjeta_seleccionada.getAttribute('precio_seleccionado') : 'false';
       tipo_precio_seleccionado = tarjeta_seleccionada.getAttribute('tipo_precio')
      }else{
        precio_seleccionado='true';
        tipo_precio_seleccionado='true'
    }
   

  if((tipo==1 && promocion!=1) && precio_seleccionado != 'true'){
    Swal.fire({
      icon: 'error',
      title: 'Elige un precio para el producto'
    })
    return false;
  }

  
  if(promocion==1 && tipo_precio_seleccionado=='true' && tipo ==1){
    tipo_precio='promo'
  }

  if(tipo==2){
    tipo_precio='precio'
  }

  let cantidad = $(`#${id_input}`).val() == undefined ? 1 : $(`#${id_input}`).val();

  if (tipo == 1) {
    ruta_agregar = "agregar-producto.php";
  } else if (tipo == 2) {
    ruta_agregar = "agregar-servicio.php";
  } else {
    toastr.error("El tipo de producto o servicio no fue especificado", "Error");
    return false;
  }

  $.ajax({
    type: "post",
    url: `./modelo/punto_venta/${ruta_agregar}`,
    data: {
      id_producto,
      cantidad,
      id_sucursal,
      tipo,
      ocultar_sidebar,
      sumar_restar_cantidad,
      promocion,
      comparar_pu_preventa,
      tipo_precio
    },
    dataType: "json",
    success: function (response) {
      if (response.estatus) {
        toastr.success(response.mensaje, "Advertencia");
        if (ocultar_sidebar == 0) {
          if (flag == 0) {
            mostrarSidebarCart();
          }
        }
        cargarPreventa();
        //$(`#${id_input}`).val('0');
      } else {
        if (response.tipo == "danger") {
          toastr.error(response.mensaje, "Error");
        } else if (response.tipo == "warning") {
          toastr.warning(response.mensaje, "Advertencia");
        }
      }
    },
  });
}

function borrarProducto(id_detalle) {
  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/borrar-producto.php",
    data: { id_detalle },
    dataType: "json",
    success: function (response) {
      if (response.estatus) {
        toastr.success(response.mensaje, "Advertencia");
        cargarPreventa();
      } else {
        if (response.tipo == "danger") {
          toastr.error(response.mensaje, "Error");
        } else if (response.tipo == "warning") {
          toastr.warning(response.mensaje, "Advertencia");
        }
      }
    },
  });
}

function generarToken(codigo) {
  Swal.fire({
    title: "Ingrese el token",
    icon: "info",
    html:
      "<label>Ingrese el token de acceso para poder cambiar el precio de la llanta</span><br><br>" +
      '<input id="token-validar" class="form-control" placeholder="Codigo">',
    showCancelButton: true,
    cancelButtonText: "Cerrar",
    cancelButtonColor: "#00e059",
    showConfirmButton: true,
    confirmButtonText: "Validar",
    cancelButtonColor: "#ff764d",
    focusConfirm: true,
    iconColor: "#36b9cc",
    backdrop: `
                      transparent
                      no-repeat
                      blur(10px)
                 `,
    didOpen: ()=>{
      const input_cod_token = document.getElementById("token-validar");
      input_cod_token.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault(); // Evita el comportamiento por defecto
        Swal.clickConfirm(); // Dispara manualmente la confirmación
      }
    });
    } ,             
    preConfirm: (respuesta) => {
      token_validar = $("#token-validar").val();
      if (token_validar == "") {
        Swal.showValidationMessage(`El valor no puede ir vacio`);
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      token_validar = $("#token-validar").val();
      //nuevoToken = Math.floor(Math.random() * (9999 - 1000) + 1000); // Eliminar `0.`

      $.ajax({
        type: "post",
        url: "./modelo/token.php",
        data: {
          "comprobar-token": token_validar,
         // "nuevo-token": nuevoToken,
          "tipo_token": 1,
        },
        dataType: "json",
        success: function (response) {
          if (response.estatus) {
            Swal.fire({
              title: "Token correcto",
              html: "<span>Ahora puedes cambiar el precio de la llanta</br></span>",
              icon: "success",
              cancelButtonColor: "#00e059",
              showConfirmButton: true,
              confirmButtonText: "Aceptar",
              cancelButtonColor: "#ff764d",
              showDenyButton: false,
              denyButtonText: "Reporte",
            });
            /* document.getElementById("precio").disabled = false;
            $("#precio-tok").attr("onclick", ""); */
            $(`#area-precio-unitario-${codigo}`).empty().append(`
                <div class="row">
                    <div class="col-8">
                        <input type="text" value="" id="nuevo-precio-token-${codigo}" placeholder="Precio" class="form-control">
                    </div>
                    <div class="col-4">
                        <div class="btn btn-success" onclick="setearPrecioToken('${codigo}')"><i class="fas fa-check"></i></div>
                    </div>
                </div>
            `);
          } else {
            Swal.fire({
              title: "Token incorrecto",
              html: "<span>El token que ingresaste es incorrecto.</br></span>",
              icon: "error",
              cancelButtonColor: "#00e059",
              showConfirmButton: true,
              confirmButtonText: "Aceptar",
              cancelButtonColor: "#ff764d",
              showDenyButton: false,
              denyButtonText: "Reporte",
            });
          }
        },
      });
    }
  });
}

function random(tipo_token) {
  if (tipo_token == 1) {
    token = Math.floor(Math.random() * (9999 - 1000) + 1000); // Eliminar `0.`
    $("#token-actual").empty().append(`
         <img src="src/img/preload.gif" style="width:80px;">
         `);
  } else if (tipo_token == 2) {
    token = generarCodigoAlfanumerico();

    $("#token-administrativo").empty().append(`
         <img src="src/img/preload.gif" style="width:80px;">
         `);
  }
  setTimeout(function () {
    $.ajax({
      type: "post",
      url: "./modelo/token.php",
      data: { token: token, tipo_token: tipo_token },
      dataType: "json",
      success: function (response) {
        if (response.estatus) {
          if (tipo_token == 1) {
            $("#token-actual").empty().text(response.token_op);
          } else if (tipo_token == 2) {
            $("#token-administrativo").empty().text(response.token_admin);
          } else {
            alert("Ocurrio un error, contacta al administrador de sistemas");
          }
        }
      },
    });
  }, 1300);
}

function generarCodigoAlfanumerico() {
  // Crear un conjunto de caracteres permitidos (letras y números)
  const caracteresPermitidos = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

  let codigo = "";
  for (let i = 0; i < 5; i++) {
    // Elegir un carácter aleatorio del conjunto
    const caracterAleatorio = caracteresPermitidos.charAt(
      Math.floor(Math.random() * caracteresPermitidos.length)
    );

    // Agregar el carácter al código
    codigo += caracterAleatorio;
  }

  return codigo;
}

function setearPrecioToken(codigo){
    let nuevo_precio_unitario = $(`#nuevo-precio-token-${codigo}`).val();
    $.ajax({
      type: "post",
      url: "./modelo/punto_venta/setear-nuevo-precio.php",
      data: {codigo, nuevo_precio_unitario},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          toastr.success(response.mensaje, "Actualizado");
          cargarPreventa()
        }else{
          toastr.error(response.mensaje, "Error");
        }
      }
    });
    
}

$("#cliente").selectpicker();
let currentPage = 1;
let isLoading = false;

async function cargarClientes(query = "", page = 1) {
        // Si ya está cargando, no haga otra solicitud
        if (isLoading) return;
        isLoading = true;

        const response = await fetch(
          `./modelo/punto_venta/busqueda-clientes.php?query=${query}&page=${page}`
        );
        const clientes = await response.json();

        clientes.data.forEach((cliente) => {
          const option = document.createElement("option");
          option.value = cliente.id;
          option.textContent = cliente.nombre_cliente;
          option.setAttribute("data-tipo", cliente.tipo_cliente);
          $("#cliente").append(option);
        });

        $("#cliente").selectpicker("refresh");
        isLoading = false;
}

 // Escuchar el evento de apertura del selectpicker para cargar la primera página
$("#cliente").on("shown.bs.select", function () {
        cargarClientes(); // Cargar la primera página
        var $bsSearchbox = $(this).parent().find(".bs-searchbox input");

        $bsSearchbox.off("keyup"); // Asegurarnos de no duplicar eventos
        $bsSearchbox.on("keyup", function (e) {
          $("#cliente").empty();
          currentPage = 1;
          cargarClientes(e.target.value, currentPage); // Nueva búsqueda, reiniciar la página
        });

        $(".dropdown-menu.inner.dropdown-menu").on("scroll", function () {
         
          const scrollPosition = $(this).scrollTop() + $(this).innerHeight();
          const scrollHeight = $(this)[0].scrollHeight;

          // Si el usuario está en la parte inferior y no se está cargando, cargar la siguiente página
          if (scrollPosition >= scrollHeight && !isLoading) {
            currentPage++;
            const query = $(".bs-searchbox input").val();
            cargarClientes(query, currentPage);
          }
        });
});

function setLocalStorageCliente(){
  setTimeout(function(){
    nombre_cliente =  $('#cliente').siblings('.dropdown-toggle').find('.filter-option-inner-inner').text();
    let tipo_cliente_actual =  $('#cliente').siblings('.dropdown-toggle').find('.filter-option-inner-inner').text();
    id_cliente = document.querySelector('#cliente').value
    const selectedOption = $("#cliente option:selected");
    const tipoCliente = selectedOption.data("tipo");
    
    localStorage.setItem("id_cliente", id_cliente);
    localStorage.setItem("nombre_cliente", nombre_cliente);
    localStorage.setItem('tipo_cliente', tipoCliente)

    let ancho = $("#ancho").val();
    let alto = $("#ancho").val();
    let rin = $("#ancho").val();
    if(ancho !='' || alto !='' || rin !=''){
      buscarNeumaticoPuntoVenta()
    }
  },100)
  
}

getLocalStorageCliente()
function getLocalStorageCliente(){
  let id_cliente = localStorage.getItem("id_cliente");
  let nombre_cliente = localStorage.getItem("nombre_cliente", id_cliente);

  document.querySelector('#cliente').setAttribute('title', nombre_cliente);
  document.querySelector('#cliente').value = parseInt(id_cliente)
  $('#cliente').selectpicker('refresh'); 
}

function configuracionDeVenta() {
      let id_cliente = localStorage.getItem("id_cliente");
      let nombre_cliente = localStorage.getItem("nombre_cliente");
      if(id_cliente==null||id_cliente==''){
        toastr.error('Selecciona un cliente', "Error");
        return false;
      }

      let importe=localStorage.getItem("importe_total");
      let importe_total = new Intl.NumberFormat().format(
        importe
      );

      Swal.fire({
        html: `
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <span style="color:#a0a0a0;"><b>Configuración de la venta</b></span><br>
                    <h4 class="mt-2"><b>${nombre_cliente}</b></h4>
                </div>    
            </div>

            <!---<div class="row mt-3">
                <div class="col-12">
                    <label for="cliente">Cliente</label>
                    <select id="cliente" class="form-control selectpicker" data-live-search="true">

                    </select>
                </div>    
            </div>-->
            <hr>
            <div class="row mt-4">
              <div class="col-12 col-md-6">
                    <label for="tipo-venta">Tipo de venta</label>
                    <select id="tipo-venta" class="form-control selectpicker" onchange="cargarTipoVenta()">
                        <option value="Normal">Contado</option>
                        <option value="Credito">Credito</option>
                        <option value="Apartado">Apartado</option>
                    </select>
                </div> 

                <div class="col-12 col-md-6">
                    <label for="forma-pago" id="label-formas-pago">Forma(s) de pago</label>
                    <select id="forma-pago" onchange="setearInputsFormaPago('${importe_total}')" class="form-control selectpicker" multiple>
                        <option value="0">Efectivo</option>
                        <option value="1">Tarjeta</option>
                        <option value="2">Transferencia</option>
                        <option value="3">Cheque</option>
                        <option value="4">Sin definir</option>
                    </select>
                </div> 
                   
            </div>
            <div id="area-inputs-formas-pago" class="row mt-4 d-none">

            </div>
            <div id="area-plazo-credito" class="row mt-4 d-none">

            </div>
            <div id="area-mensaje-creditos" class="row mt-4 d-none">

            </div>

            <div class="row mb-2 mt-4">
                <div class="col-12">
                    <label for="comentarios">Comentarios</label>
                    <textarea class="form-control" id="comentarios" placeholder="Escribe aqui un comentario..."></textarea>
                </div>  
            </div>
            <hr>
            <div class="row mt-4" id="area-importe-total">
                <div class="col-12">
                    <label for="comentarios">Total:</label>
                    <b><span style="color:gray" id="importe-total-confg"></span></b>
                </div>  
            </div>  
        </div>`,
    width: "700px",
    confirmButtonText: `Procesar venta`,
    preConfirm: ()=>{
      let sumatoria_forma_pago_valida = $("#importe-total-confg").attr("is-valid");
      let validacion_mensaje_error = $("#importe-total-confg").attr("mensaje_error");
   
      if(sumatoria_forma_pago_valida=='false'){
        return Swal.showValidationMessage(`
          ${validacion_mensaje_error}
      `);
      }
    },
    didOpen: () => {
      $("#importe-total-confg").attr("is-valid", "false")
      $("#importe-total-confg").attr("mensaje_error", 'Selecciona una forma de pago');
      $("#importe-total-confg").text("$" + importe_total);
      $('#forma-pago').selectpicker('render');
      $('#tipo-venta').selectpicker('render');
      let button_confirm = document.querySelector('.swal2-confirm');
      button_confirm.style.backgroundColor = '#858796';
      button_confirm.style.borderColor = '#858796';
    },
  }).then(function(r){
    if(r.isConfirmed){
      let venta_valida = $("#importe-total-confg").attr("is-valid")
      
      if(venta_valida=='true'){
        procesarVenta()
      }else{

      }
    }
  })
}
//configuracionDeVenta()
function setearInputsFormaPago(importe_){
  let button_confirm = document.querySelector('.swal2-confirm');
  let formas_pago = $('#forma-pago').val();
  let tipo_venta = $("#tipo-venta").val()
  let area_formas_pago = $("#area-inputs-formas-pago");
  $("#importe-total-confg").css('color', '#faa300')
  button_confirm.style.backgroundColor = '#858796';
  button_confirm.style.borderColor = '#858796';


  if(formas_pago.length>1 || (formas_pago.length == 1 && tipo_venta != 'Normal')){
    area_formas_pago.removeClass('d-none')
    $("#importe-total-confg").attr('is-valid', 'false')
    $("#importe-total-confg").attr("mensaje_error", 'Escribe los montos de las formas de pago');
    area_formas_pago.empty();
    let label_ingresa_montos = tipo_venta=='Credito' ? 'Ingresa los montos de pago del adelanto' : 'Ingresa los montos de pago'
    area_formas_pago.append(`
    <hr>
        <div class="col-md-12 mt-2 text-left">
            <b id="formas-pago-inputs-label">${label_ingresa_montos}:</b>
        </div>
    `);
    let formas_pago_arreglo = ['Efectivo', 'Tarjeta', 'Transferencia', 'Cheque', 'Sin definir']
    formas_pago.forEach(element => {
      area_formas_pago.append(`
        <div class="col-12 mt-2 col-md-6">
            <label>${formas_pago_arreglo[element]}</label>
            <input type="number" onkeyup="calcularMontos()" placeholder="0.00" class="form-control" id="${element}_id">
        </div> 
          `)
    });
  }else{
    if(formas_pago.length==1){
      if(tipo_venta=='Normal'){
        audio.play();      
        $("#importe-total-confg").css('color', '#1cc88a')
        $("#importe-total-confg").attr('is-valid', 'true')
        $("#area-importe-total").empty().append(`
        <div class="col-12">
        <label>Total:</label>
            <b><span style="color:#1cc88a" is-valid="true" id="importe-total-confg">$${importe_}</span></b>
        </div>
        `)
        button_confirm.style.backgroundColor = '#1cc88a';
        button_confirm.style.borderColor = '#1cc88a';
      }
     
    }else{
      $("#importe-total-confg").attr('is-valid', 'false')
      $("#importe-total-confg").attr("mensaje_error", 'Selecciona por lo menos una forma de pago');

    }
    let tiene_d_none = area_formas_pago.hasClass('d-none')
    if(!tiene_d_none){
      area_formas_pago.addClass('d-none');
    }
  }
 
}

function calcularMontos(){
 
  let tipo_venta = $("#tipo-venta").val();
  var inputs_formas_pago = document.querySelectorAll("#area-inputs-formas-pago input[type=number]");  // Obtener todos los inputs
  var suma = 0;
  
  inputs_formas_pago.forEach(function(input) {
    var valor = parseFloat(input.value);
    if (!isNaN(valor)) {
      suma += valor;
    }
  });
  
  // Verificar si la suma es igual al precio_llanta y actualizar el badge
  var text_message = document.getElementById("text-message");
  let importe=parseFloat(localStorage.getItem("importe_total"));
  let importe_total = new Intl.NumberFormat().format(
    importe
  );
  let sumatoria_formateada_forma_pagos = new Intl.NumberFormat().format(
    suma
  );

  $("#area-importe-total").empty()
  if(inputs_formas_pago.length>1){
    if(tipo_venta=="Credito"){
      $("#area-importe-total").append(`
      <div class="col-4">
        <label>Suma:</label>
        <b><span style="color:#858796" id="sumatoria-formas-pago"></span></b>
      </div>  
      <div class="col-4">
        <label>Restante:</label>
        <b><span style="color:#858796" id="restante-formas-pago"></span></b>
      </div> 
      <div class="col-4">
          <label>Total:</label> 
          <b><span style="color:#faa300" id="importe-total-confg">$${importe_total}</span></b>
      </div>
    `)
    }else if(tipo_venta=='Apartado'){
      $("#area-importe-total").append(`
      <div class="col-12">
         <label>Minimo monto adelanto: <span id="minimo-adelanto-porcentaje"></span>
         <b><span style="color:#858796" id="minimo-adelanto"></span></b>
      </div>
      <div class="col-4">
        <label>Suma:</label>
        <b><span style="color:#858796" id="sumatoria-formas-pago"></span></b>
      </div>  
      <div class="col-4">
        <label>Restante:</label>
        <b><span style="color:#858796" id="restante-formas-pago"></span></b>
      </div> 
      <div class="col-4">
          <label>Total:</label> 
          <b><span style="color:#faa300" id="importe-total-confg">$${importe_total}</span></b>
      </div>
    `)
    }else{
      $("#area-importe-total").append(`
    <div class="col-6">
      <label>Suma:</label>
      <b><span style="color:#858796" id="sumatoria-formas-pago"></span></b>
    </div> 
    <div class="col-6">
        <label>Total:</label> 
        <b><span style="color:#faa300" id="importe-total-confg">$${importe_total}</span></b>
    </div>
  `)
    }
    
  }else{
    if(tipo_venta=='Apartado'){

      $("#area-importe-total").append(`
      <div class="col-12">
         <label>Minimo monto adelanto: <span id="minimo-adelanto-porcentaje"></span>
         <b><span style="color:#858796" id="minimo-adelanto"></span></b>
      </div>
      <div class="col-4">
        <label>Suma:</label>
        <b><span style="color:#858796" id="sumatoria-formas-pago"></span></b>
      </div>  
      <div class="col-4">
        <label>Restante:</label>
        <b><span style="color:#858796" id="restante-formas-pago"></span></b>
      </div> 
      <div class="col-4">
          <label>Total:</label> 
          <b><span style="color:#faa300" id="importe-total-confg">$${importe_total}</span></b>
      </div>
    `)
    }else{
      if(tipo_venta=='Credito'){
        $("#area-importe-total").append(`
        <div class="col-4">
          <label>Suma:</label>
          <b><span style="color:#858796" id="sumatoria-formas-pago"></span></b>
        </div>  
        <div class="col-4">
          <label>Restante:</label>
          <b><span style="color:#858796" id="restante-formas-pago"></span></b>
        </div> 
        <div class="col-4">
            <label>Total:</label> 
            <b><span style="color:#faa300" id="importe-total-confg">$${importe_total}</span></b>
        </div>
      `)
      }else{
        $("#area-importe-total").append(`
    <div class="col-12">
        <label>Total:</label> 
        <b><span style="color:#faa300" id="importe-total-confg">$${importe_total}</span></b>
    </div>
  `)
      }
      
    }
  }

  $("#sumatoria-formas-pago").attr('suma', suma).text(`$${sumatoria_formateada_forma_pagos}`)
  let restante = importe - suma
  restante_ft = new Intl.NumberFormat().format(
       restante
      );
  if (suma === importe) {
    let porcentaje = 0;

            if (importe >= 0 && importe <= 4999) {
                porcentaje = 20;
            } else if (importe >= 5000) {
                porcentaje = 15;
            } else {
              Swal.showValidationMessage(`Cantidad no valida`);
            }

        let adelanto_minimo = (importe * porcentaje) / 100;
        $("#minimo-adelanto-porcentaje").text(porcentaje+'%')
        let minimo_adelanto_ft = new Intl.NumberFormat().format(adelanto_minimo);
        $("#minimo-adelanto").text('$'+minimo_adelanto_ft)
        $("#restante-formas-pago").attr('restante', restante).text('$'+restante_ft).css('color', '#1cc88a')

    establecerEstatusEstiloConfiguracionVenta(true, '',1)

    audio.play();  
  }else if(suma > importe){
    $("#restante-formas-pago").text('Error').css('color', 'tomato')
    establecerEstatusEstiloConfiguracionVenta(false, 'La suma sobrepasa el importe total',3)
    audio_error.play();
  } else {
    if(tipo_venta=='Credito' || tipo_venta=='Apartado'){
      
      if(tipo_venta=='Credito'){
        $("#restante-formas-pago").text('$'+restante_ft)
        establecerEstatusEstiloConfiguracionVenta(true, '',1, restante)
        audio.play();  
      }else if(tipo_venta=='Apartado'){
        //Logica del apartado
        let porcentaje = 0;

            if (importe >= 0 && importe <= 4999) {
                porcentaje = 20;
            } else if (importe >= 5000) {
                porcentaje = 15;
            } else {
              Swal.showValidationMessage(`Cantidad no valida`);
            }

        let adelanto_minimo = (importe * porcentaje) / 100;
        $("#minimo-adelanto-porcentaje").text(porcentaje+'%')
        let minimo_adelanto_ft = new Intl.NumberFormat().format(adelanto_minimo);
        $("#minimo-adelanto").text('$'+minimo_adelanto_ft)
        $("#restante-formas-pago").attr('restante', restante).text('$'+restante_ft)
        if(adelanto_minimo >suma){
          $("#area-importe-total").append(`
          <div class="row m-auto justify-content-center">
          <div class="col-12 mt-3">
              <label>Token:</label> 
          </div>
          <div class="col-12 text-center mb-3">
              <input id="token-apartado-1" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
              <input id="token-apartado-2" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
              <input id="token-apartado-3" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
              <input id="token-apartado-4" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
          </div>
          <div class="col-12" id="mensaje-error-token-apartado">
            
          </div>
          </div>
        `)

        let button_confirm = document.querySelector('.swal2-confirm');
        button_confirm.style.backgroundColor = '#858796';
        button_confirm.style.borderColor = '#858796';
        controlCodeInputs('apartado','.form-control_code_apartado','#mensaje-error-token-apartado')
          establecerEstatusEstiloConfiguracionVenta(false, 'Ocupa un adelanto minimo, coloca la cantidad correcta o ingrese token',0, restante)
        }else{
          $("#restante-formas-pago").css('color', '#1cc88a')
          establecerEstatusEstiloConfiguracionVenta(true, '',1)

        }
      }
      return true;
      
    }else{
      establecerEstatusEstiloConfiguracionVenta(false, 'La suma de los montos no coinciden con el importe total',2)
    }
  }
  
}

function establecerEstatusEstiloConfiguracionVenta(estatus, mensaje, tipo, restante=0){

  let button_confirm = document.querySelector('.swal2-confirm');
    if(tipo==1){
      $("#importe-total-confg").css('color', '#1cc88a')
      $("#sumatoria-formas-pago").css('color', '#1cc88a')
      button_confirm.style.backgroundColor = '#1cc88a';
      button_confirm.style.borderColor = '#1cc88a';
    }else if(tipo==2){
      button_confirm.style.backgroundColor = '#858796';
      button_confirm.style.borderColor = '#858796';
      $("#sumatoria-formas-pago").css('color', '#858796')
      $("#importe-total-confg").css('color', '#858796')
    }else if (tipo==3){
      button_confirm.style.backgroundColor = '#858796';
      button_confirm.style.borderColor = '#858796';
      $("#sumatoria-formas-pago").css('color', 'tomato')
      $("#importe-total-confg").css('color', 'tomato')
    }
    $("#importe-total-confg").attr("mensaje_error",mensaje);
    $("#importe-total-confg").attr("is-valid", estatus)
}
 
function procesarVenta(){

  let formas_pagos = $('#forma-pago').val();
 
  let restante = $("#restante-formas-pago").attr('restante');
  let tipo_venta = $('#tipo-venta').val();
  let plazo = tipo_venta =='Credito' ? $("#plazo-credito").val() : null;
  let pagare =  tipo_venta =='Credito' ? $("#pagare").val() : null;
  let id_cliente = localStorage.getItem('id_cliente'); //
  let comentario = $("#comentarios").val()
  let importe=localStorage.getItem("importe_total");
  let metodos_formateado = formas_pagos.reduce(function(result, key) {
    if(formas_pagos.length==1 && tipo_venta=='Normal'){
      monto_total = parseFloat(importe);
    }else{
      monto_total = parseFloat($(`#${key}_id`).val());

    }
    result[key] = {"id_metodo":key, "metodo":key, "monto": monto_total};
    return result;
  }, {});

  if(tipo_venta!='Apartado'){
    $.ajax({
      type: "post",
      url: "./modelo/punto_venta/realiza-venta-contado.php",
      data: {metodos_formateado, tipo_venta, id_cliente, comentario, plazo, pagare},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          if(tipo_venta=='Normal'){
            window.open('./modelo/ventas/reporte-venta.php?id='+ response.folio, '_blank');
          }else if(tipo_venta =='Credito'){
            window.open('./modelo/creditos/generar-reporte-credito.php?id='+ response.folio, '_blank');
          }

          Swal.fire({
            title: 'Venta realizada',
            html: "<span>La venta se realizó con exito</br></span>"+
            "ID Venta: RAY" + response.folio,
            icon: "success",
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Aceptar', 
            cancelButtonColor:'#ff764d',
            showDenyButton: true,
            allowOutsideClick: false,
            denyButtonText: 'Reporte'
        }).then(function(r){
        if(r.isDenied){
          if(tipo_venta=='Normal'){
            window.open('./modelo/ventas/reporte-venta.php?id='+ response.folio, '_blank');
          }else if(tipo_venta =='Credito'){
            window.open('./modelo/creditos/generar-reporte-credito.php?id='+ response.folio, '_blank');

          }
          }
          cargarPreventa()
          localStorage.setItem('id_cliente', '');
          localStorage.setItem('nombre_cliente', 'Selecciona un cliente')
        })
        }else{
          toastr.warning(response.mensaje, "Advertencia");
        }
      }
    });
  }else{
    $.ajax({
      type: "post",
      url: "./modelo/apartados/realizar-apartado.php",
      data: {metodos_formateado, tipo_venta, id_cliente,comentario,plazo, restante},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          Swal.fire({
            title: 'Apartado realizado',
            html: "<span>El apartado se realizó con exito</br></span>"+
            "ID Apartado: AP" + response.folio,
            icon: "success",
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Aceptar', 
            cancelButtonColor:'#ff764d',
            showDenyButton: true,
            allowOutsideClick: false,
            denyButtonText: 'Reporte'
        }).then(function(r){
        if(r.isDenied){
          window.open('./modelo/apartados/reporte-apartado.php?id='+ response.folio, '_blank');
          }
          cargarPreventa()
          localStorage.setItem('id_cliente', '');
          localStorage.setItem('nombre_cliente', 'Selecciona un cliente')
        })
        }else{
          toastr.warning(response.mensaje, "Advertencia");
        }
      }
    });
  }
  $('#cliente').val('')
  $("#cliente").selectpicker('refresh')
}

let arreglo_creditos_vencidos;
let deuda_total;
function cargarTipoVenta(){
  $("#swal2-validation-message").css('display', 'none')
  let importe=localStorage.getItem("importe_total");
  let importe_total = new Intl.NumberFormat().format(
    importe
  );
  $("#area-inputs-formas-pago").empty()
  let button_confirm = document.querySelector('.swal2-confirm');
  button_confirm.style.backgroundColor = '#858796';
  button_confirm.style.borderColor = '#858796';
  let tipo_venta = $("#tipo-venta").val()
  let contenedor = $("#area-mensaje-creditos");

  //EL tipo de venta es un credito, se realiza una consulta para el estatus del cliente
  if(tipo_venta=='Credito'){
    let id_cliente = localStorage.getItem('id_cliente');
    $.ajax({
      type: "post",
      url: "./modelo/punto_venta/comprobar-datos-cliente.php",
      data: {id_cliente},
      dataType: "json",
      success: function (response) {
        $("#label-formas-pago").text('Forma(s) de pago adelanto')
        $("#formas-pago-inputs-label").text('Ingresa los montos de pago del adelanto:')
        $("#area-plazo-credito").removeClass('d-none').append(`
             <div class="col-6">
                  <label class="">Plazo</label>
                  <select class="form-control selectpicker" id="plazo-credito">
                        <option value="6">1 día</div>
                        <option value="1" select>1 semana</div>
                        <option value="2">15 días</div>
                        <option value="3">1 mes</div>
                        <option value="5">Sin definir</div>
                  </select>
             </div>
             <div class="col-6">
                  <label class="">Pagare</label>
                  <select class="form-control selectpicker" id="pagare">
                        <option value="1">Si</div>
                        <option value="2" select>No</div>
                  </select>
             </div>
        `)
        $("#plazo-credito").selectpicker('refresh')
        $("#pagare").selectpicker('refresh')
        if(response.estatus){ //Credito vencido o sin credito 
          $("#plazo-credito").prop('disabled',true)
          $("#pagare").prop('disabled',true)
          $("#forma-pago").prop('disabled',true)
          $("#area-inputs-formas-pago").empty()
          $("#forma-pago").val('')
          $("#forma-pago").selectpicker('refresh')
          $("#plazo-credito").selectpicker('refresh')
          $("#pagare").selectpicker('refresh')
          arreglo_creditos_vencidos=response.credito_vencidos;
          deuda_total = response.sumatoria_deuda;
          $("#area-importe-total").empty().append(`
          <div class="col-12">
          <label>Total:</label>
              <b><span is-valid="true" id="importe-total-confg">$${importe_total}</span></b>
          </div>
          `)
          $("#importe-total-confg").attr("mensaje_error", 'El cliente tiene credito vencido o no tiene credito, usa un token de credito');
          $("#importe-total-confg").attr("is-valid", "false")
          $("#importe-total-confg").css('color', '#858796')
          
          contenedor.removeClass('d-none')
          contenedor.empty().append(`
              <img src="src/img/preload.gif" style="width:100px; margin:auto;">
          `);
          setTimeout(function(){
            contenedor.empty();
            contenedor.append(`${response.mensaje}
      
                 <div class="col-12 mb-3">
                   <div class="btn btn-info m-auto" onclick="setearCreditosVencidos()">Ver creditos vencidos</div><br>
                </div>
                <div class="col-12">
                     <label for="token-credito">Token de credito:</label>
                     <div class="row">
                         <div class="col-12">
                            <input id="token-credito-1" autocomplete="off" class="form-control_code" placeholder="0"></input>
                            <input id="token-credito-2" autocomplete="off" class="form-control_code" placeholder="0"></input>
                            <input id="token-credito-3" autocomplete="off" class="form-control_code" placeholder="0"></input>
                            <input id="token-credito-4" autocomplete="off" class="form-control_code" placeholder="0"></input>
                            <input id="token-credito-5" autocomplete="off" class="form-control_code" placeholder="0"></input>
                         </div>
                         <div class="col-12" id="mensaje-error-token">
                          
                         </div>
                      
                     </div>
                </div>
            <div id="area-creditos-vencidos" class="row mt-4 d-none">

            </div>
            `);
            controlCodeInputs('credito', '.form-control_code', '#mensaje-error-token')
          },700)
        }else{
          $("#forma-pago").prop('disabled',false)
          $("#forma-pago").val('')
          $("#forma-pago").selectpicker('refresh')
        }
      }
    });
  }else if(tipo_venta =='Apartado'){
      $("#area-inputs-formas-pago").empty()
      $("#area-plazo-credito").empty()
      $("#pagare").empty()
      $("#area-mensaje-creditos").empty()
      $("#area-creditos-vencidos").empty()
      $("#label-formas-pago").text('Forma(s) de pago adelanto')
      $("#forma-pago").prop('disabled',false)
      $("#forma-pago").val('')
      $("#forma-pago").selectpicker('refresh')
      $("#area-importe-total").empty().append(`
    <div class="col-12">
    <label>Total:</label>
        <b><span mensaje_error="Selecciona una forma de pago" style="color:#858796" is-valid="false" id="importe-total-confg">$${importe_total}</span></b>
    </div>
    `)
  }else{
    let tienes_d_none= $("#area-plazo-credito").hasClass('d-none')
    if(!tienes_d_none){$("#area-plazo-credito").empty().addClass('d-none')}
    $("#label-formas-pago").text('Forma(s) de pago')
    $("#forma-pago").prop('disabled',false)
    $("#forma-pago").val('')
    $("#forma-pago").selectpicker('refresh')
   
    $("#area-importe-total").empty().append(`
    <div class="col-12">
    <label>Total:</label>
        <b><span style="color:#1cc88a" is-valid="true" id="importe-total-confg">$${importe_total}</span></b>
    </div>
    `)
    $("#formas-pago-inputs-label").text('Ingresa los montos de pago:')
    calcularMontos()
    contenedor.empty()
    $("#importe-total-confg").attr("mensaje_error", 'Selecciona una forma de pago');
    $("#importe-total-confg").attr("is-valid", "false")
           
    
  }
}

function setearCreditosVencidos(){

  let contenedor = $("#area-creditos-vencidos");
  contenedor.removeClass('d-none')
  contenedor.empty().append(`
  <div class="col-md-12">
    <img src="src/img/preload.gif" style="width:100px; margin:auto;">
  </div>
`);
setTimeout(function(){
  contenedor.empty();
  contenedor.append(`
  <table class="table table-responsive text-center" style="font-size:14px; margin:auto;">
      <thead>
          <tr>
              <th>Folio</th>
              <th>Fecha apertura</th>
              <th>Fecha vencimiento</th>
              <th>Monto</th>
              <th>Pagado</th>
              <th>Restante</th>
              <th>PDF</th>
          </tr>
      </thead>
      <tbody id="tbody-creditos-vencidos" style="height:5rem; overflow-y:scroll;"></tbody>
  </table>

      <div class="col-md-12 text-center"><span>Deuda total: <b style="color:tomato">$<span id="deuda-total-credito"></span></b></span></div>


  
`);
let tbody_cred = $("#tbody-creditos-vencidos");
arreglo_creditos_vencidos.forEach(element => {
  tbody_cred.append(`
  <tr>
              <th>${element.id_cred}</th>
              <th>${element.fecha_inicio}</th>
              <th>${element.fecha_final}</th>
              <th>${element.total}</th>
              <th>${element.pagado}</th>
              <th>${element.restante}</th>
              <th><button onclick="pdfCredito(${element.id_venta})" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span></th>
  </tr>
`)
});
$("#deuda-total-credito").text(deuda_total)
},700)
  
}

function pdfCredito(id) {
  window.open(
    "./modelo/creditos/generar-reporte-credito.php?id=" + id,
    "_blank"
  );
}

//Ni me acuerdo jaja, algo tiene que ver con los estilos en modo telefono
if (screen.width < 1445) {
  $("#area-resultados").removeClass("col-lg-10");
  $("#area-resultados").addClass("col-md-12");
} else {
}

function limpiarPreventa(){
  $.ajax({
    type: "post",
    url: "./modelo/punto_venta/limpiar-preventa.php",
    data: "data",
    dataType: "json",
    success: function (response) {
      if(response.estatus==true){
        toastr.success(response.mensaje, "Excelente");
        cargarPreventa();
      }else{
        toastr.error('No se pudo limpiar ocurrio un error', "Error");
      }
    }
  });
}


function selectorPrecio(e, this_e, codigo){
  e?.stopPropagation(); // Solo si 'e' no es null
  let id_tarjeta = this_e?.id || "Sin ID";
  let tarjetas_contenedor = this_e.closest(".tarjetas-precios");
  let iconos_checked = tarjetas_contenedor.querySelectorAll('.checked-icon')
  let tarjetas = tarjetas_contenedor.querySelectorAll('.boton_precio_estilos');
  let icon_checked;
  let tarjeta_seleccionada = document.getElementById('tarjeta-codigo-'+codigo)

  switch (id_tarjeta) {
    case 'tarjeta-lista':
        icon_checked = document.getElementById('icon-checked-lista-'+codigo);
        tipo_precio='lista'
        
      break;
    case 'tarjeta-precio':
      tipo_cliente=  localStorage.getItem("tipo_cliente");
      if(tipo_cliente==1){
      tipo_precio='mayoreo'
      }else{
        tipo_precio='precio'
      }

        icon_checked = document.getElementById('icon-checked-precio-'+codigo);
      break;
     
    case 'tarjeta-promocion':
        tipo_precio='promo'
        icon_checked = document.getElementById('icon-checked-promo-'+codigo);
      break;

    default:
      break;
  }

  if(this_e.classList.contains('activo')){
    tarjetas.forEach(tarjeta=>{
      tarjeta.style.backgroundColor=tarjeta.getAttribute('data-original');
      tarjeta.classList.remove('activo');
      tipo_precio=null;
    })
    iconos_checked.forEach(icono => {
      icono.classList.add('d-none');
  });
  tarjeta_seleccionada.setAttribute('precio_seleccionado', false);
  tarjeta_seleccionada.setAttribute('tipo_precio', false);
  }else{
        // Guardar el color original si no está almacenado
    tarjetas.forEach(tarjeta=>{
      if(!tarjeta.hasAttribute('data-original')){
        tarjeta.setAttribute('data-original', tarjeta.style.backgroundColor)
      }
    })
     // Convertir todos a gris
     tarjetas.forEach(tarjeta => {
          tarjeta.style.backgroundColor = "gray";
          tarjeta.classList.remove("activo");
          icon_checked.classList.add('d-none');
      });
      iconos_checked.forEach(icono => {
        icono.classList.add('d-none');
    });

  // Restaurar el color del clickeado y marcarlo como activo
  this_e.style.backgroundColor = this_e.getAttribute("data-original");
  this_e.classList.add("activo");
  icon_checked.classList.remove("d-none");
 
  tarjeta_seleccionada.setAttribute('precio_seleccionado', true);
  tarjeta_seleccionada.setAttribute('tipo_precio', tipo_precio);
  }

}