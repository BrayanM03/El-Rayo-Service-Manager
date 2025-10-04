

function MostrarCuentasPorPagar() {
    //$.fn.dataTable.ext.errMode = 'none';
    //ocultarSidebar();
    id_proveedor = getParameterByName('id_proveedor')
    table = $('#cuentas-por-pagar').DataTable({

        "bDestroy": true,
        processing: true,
        serverSide: true,
        ajax: './modelo/proveedores/estado-cuenta-tabla.php?id_proveedor=' + id_proveedor,
        rowCallback: function (row, data, index) {
            var info = this.api().page.info();
            var page = info.page;
            var length = info.length;
            var columnIndex = 0; // Índice de la primera columna a enumerar

            $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
            let vencida_ = estaVencida(data[16]);
            console.log('vencida: '+vencida_);
            if (data[11] == 4 && !vencida_) {
                $(row).css('background-color', '#eaefc8')
            }

            if(vencida_){
                $(row).css('background-color', '#ffd4ca')
            }
        },
        columns: [
            { title: "#", data: null },
            { title: "Folio", data: 0 },
            /*  { title: "descripcion",    data: 1, width: "30%"}, */
            {
                title: 'proveedor', data: 8, render: (data, row) => {
                    if (data == null) {
                        var prov = 'NA'
                    } else {
                        var prov = data;
                    }
                    return prov;
                }
            },
            { title: 'factura', data: 9, visible: true },
            {
                title: 'Estado fact.', data: 11, render(data, row) {
                    if (data == 1) {
                        estado_factura_ = 'Sin factura';
                    } else if (data == 2) {
                        estado_factura_ = 'Factura completa';
                    } else if (data == 3) {
                        estado_factura_ = 'Factura incompleta';
                    } else if (data == 4) {
                        estado_factura_ = '<span class="badge bg-success p-2 text-white">Factura pagada</span>';
                    }else if(data == 5){
                        estado_factura_ = '<span class="badge bg-danger p-2 text-white">Factura vencida</span>';
                      } else {
                        estado_factura_ = 'No aplica'
                    }
                    return estado_factura_;
                }
            },
            {
                title: "total", data: 12, render: function (data) {
                    return formatoMonedaMXN(data)
                }
            },
            {
                title: "pagado", data: 13, render: function (data) {
                    return formatoMonedaMXN(data)
                }
            },
            {
                title: "restante", data: 14, render: function (data) {
                    return formatoMonedaMXN(data)
                }
            },
            {
                title: "fecha", data: null, render: (data) => {
                 
                    return formatDate(data[3])
                }
            },
            { title: "hora", data: 4 },
            {
                title: "tipo", data: 6, render(data, row) {
                    if (data == 1) {
                        tipo = 'Movimiento';
                    } else if (data == 2) {
                        tipo = 'Ingreso';
                    } else if (data == 3) {
                        tipo = 'Retiro';
                    } else if (data == 4) {
                        tipo = 'Ingreso';
                    } else if (data == 5) {
                        tipo = 'Borrado';
                    } else {
                        tipo = data;
                    }
                    return tipo;
                }
            },
            { title: "usuario", data: 5 },
            {
                title: "fecha emision", data: null, render: (data) => {
                 
                    return formatDate(data[15])
                }
            },
            {
                title: "fecha vencido", data: null, render: (data) => {
                 
                    return formatDate(data[16])
                }
            },
            {
                title: "Accion",
                data: null,
                className: "celda-acciones",
                render: function (row, data) {
                    if (row[11] == 'patata') {
                        class_btn_check = 'btn-secondary disabled';
                        candado = 'ss';
                    } else {
                        candado = '';
                        class_btn_check = 'btn-primary';
                    }
                    if (row[6] == 1 || row[6] == 3 || row[6] == 4) {
                        return `
              <div style="display:flex;">
                  <div class="btn btn-danger mr-2" onclick="remisionSalida(${row[0]})"><i class="fas fa-file-pdf"></i></div>
                  <div class="btn ${class_btn_check}" onclic${candado}k="administrarCuenta(${row[0]}, ${row[6]})"><i class="fas fa-check" disabled></i></div>
              </div>
                  `;
                    } else if (row[6] == 2) {
                        return `
              <div style="display:flex;">
                 <div class="btn btn-danger mr-2" onclick="remisionIngreso(${row[0]})"><i class="fas fa-file-pdf"></i></div>
                 <div class="btn ${class_btn_check}" onclick="administrarCuenta(${row[0]},  ${row[6]})"><i class="fas fa-check"></i></div>
              </div>`;

                    } else {
                        return `<span>No disp</span>`;
                    }
                },
            },
        ],
        paging: true,
        searching: true,
        scrollY: "50vh",
        info: false,
        responsive: false,
        order: [1, "desc"],


    });
    //table.columns( [6] ).visible( true );
    $("table.dataTable thead").addClass("table-dark")
    $("table.dataTable thead").addClass("text-white")

   

}


MostrarCuentasPorPagar();

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function formatDate(fechaStr) {
    const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", 
                   "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
  
    const fecha = new Date(fechaStr);
    const dia = fecha.getDate();
    const mes = meses[fecha.getMonth()];
    const anio = fecha.getFullYear();
  
    return `${dia} ${mes} ${anio}`;
  }

function estaVencida(fechaStr) {
    console.log(fechaStr);
    // Convertir la fecha recibida a objeto Date
    const fechaParametro = new Date(fechaStr);
    
    // Tomar la fecha actual sin hora
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
  
    // Comparar fechas
    console.log(fechaParametro);
    if (fechaParametro < hoy) {
        console.log('esta vencida');
      return true; //"La fecha esta vencida
    } else if (fechaParametro.getTime() === hoy.getTime()) {
      return true;//"La fecha es hoy";
    } else {
      return false; //"La fecha aun no esta vencida
    }
  }

function formatoMonedaMXN(cantidad) {
    return new Intl.NumberFormat('es-MX', {
      style: 'currency',
      currency: 'MXN',
      minimumFractionDigits: 2
    }).format(cantidad);
  }

function estadoCuentaPdf(id_proveedor){
    window.open('./modelo/cuentas_pagar/reporte-estado-cuenta.php?id_proveedor='+id_proveedor, '_blank');
  }