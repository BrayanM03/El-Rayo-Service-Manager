function MostrarEmpleados() { 
  
  setEskeleton()
  setTimeout(function(){
    $.ajax({
      type: "post",
      url: "./modelo/empleados/lista-empleados.php",
      data: "data",
      dataType: "json",
      success: function (response) {
        if(response.data.empleados.estatus){
          $("#card-employe-body").css('justify-content', 'start')
          $("#card-employe-body").css('height', 'auto')
          $("#card-employe-body").empty().append(` <div class="row w-100" id="employee-grid"></div>`)
          let empleados = response.data.empleados.data;
          console.log(empleados);
          let container = document.getElementById("employee-grid");
          container.innerHTML = ""; // Limpiar el contenedor antes de agregar nuevos elementos
  
          empleados.forEach(empleado => {
              index_puesto=response.data.puestos.data.find(pst => pst[0]== empleado.id_puesto)
              let sucursal_empleado = response.data.sucursales.data.find(suc => suc[0] == empleado.id_sucursal)
              let card = `
                  <div class="col-md-3 mb-2 mt-3">
                      <div class="card employee-card p-3 text-center" 
                      onclick="expandCard(${empleado.id}, '${empleado.nombre}', '${empleado.apellidos}', '${empleado.correo}', '${empleado.telefono}', './src/img/fotos_empleados/E${empleado.id}.${empleado.extension}', '${index_puesto[1]}', '${sucursal_empleado[2]}', ${empleado.salario_base}, '${empleado.direccion.replace(/(\r\n|\n|\r)/gm, "")}', '${empleado.fecha_nacimiento}','${empleado.fecha_ingreso}', ${empleado.estatus}, ${empleado.cv}, ${empleado.curp}, ${empleado.comprobante_domicilio}, ${empleado.nss}, ${empleado.identificacion}, ${empleado.contrato}, ${empleado.bancarios})">
                        <div class="row">
                          <div class="col-12 text-center">
                            <img src="./src/img/fotos_empleados/E${empleado.id}.${empleado.extension}?v=${Math.random()}>" 
                            onerror="this.src='./src/img/neumaticos/NA.JPG'" class="employee-img mb-3 border" alt="Empleado">
                          </div>
                        </div>
                          
                          <h5 style="color:black;"><b>${empleado.nombre} ${empleado.apellidos}</b></h5>
                          <p class="text-muted">${index_puesto[1]}</p>
                          <span class="text-dark"><b>${sucursal_empleado[2]}</b></span>
                          <p><strong>Tel:</strong> ${empleado.telefono}</p>
                          <p><strong>Estatus:</strong> <span style="color: ${empleado.estatus ? 'green' : 'tomato'};">${empleado.estatus ? 'Activo' : 'Inactivo'}</span></p>
                      </div>
                  </div>
              `;
              container.innerHTML += card;
          });
        }else{
          let container = document.getElementById("employee-grid");
          container.innerHTML = ""
          let card = `
          <div class="card-body text-center" id="card-employe-body" style="height:30rem; display:flex; flex-direction:column; justify-content:center; align-items:center">
              <div class="row">
                  <img src="./src/img/leaf.png" alt="hojas" style="width: 10rem;"><br>
              </div>
              <div class="row mt-3">
                  <span style="font-size: 20px;">Sin empleados registrados</span>
              </div>
          </div>
              `;
              container.innerHTML += card;
        }
      }
    });
  },1000)
 
  
  }


  function expandCard(id_empleado, nombre, apellidos, correo, telefono, imgSrc, puesto, sucursal, salario_base, direccion, fecha_nacimiento, fecha_ingreso, estatus, cv, curp, domicilio, nss, ine, contrato, bancario) {
    const salarioformatoMoneda = parseFloat(salario_base).toLocaleString('es-MX', {
      style: 'currency',
      currency: 'MXN',
    });

    if(correo==''){correo='Sin correo'}
    if(direccion==''){direccion='Sin dirección'}
    if(telefono==''){telefono='Sin teléfono'}
    document.getElementById("expandedName").textContent = `${nombre} ${apellidos}`;
    document.getElementById('puesto-empleado').value = puesto
    document.getElementById("expandedDetails").innerHTML = `
    <strong>Sucursal:</strong> ${sucursal} <br>
    <strong>Correo:</strong> ${correo} <br> 
    <strong>Teléfono:</strong> ${telefono} <br> 
    <strong>Salario base:</strong> ${salarioformatoMoneda} <br>
    <strong>Dirección:</strong> ${direccion} <br>
    `;
    document.getElementById("expandedImg").src = imgSrc;
    document.getElementById("expandedCard").classList.remove("hidden");
    document.getElementById("overlay").classList.remove("hidden");
    document.getElementById("link-editar-empleado").href = 'editar-empleado.php?id='+id_empleado

    let ft_fecha_ingreso;
    let ft_fecha_nacimiento;

    if(fecha_ingreso != ''&& fecha_ingreso != 'null'){
        const date_ing = new Date(fecha_ingreso);
        ft_fecha_ingreso = date_ing.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: '2-digit' });
    }else{
       ft_fecha_ingreso = 'No registrada'
    }

    if(fecha_nacimiento !=''&& fecha_nacimiento != 'null'){
      const date_nac = new Date(fecha_nacimiento);
      ft_fecha_nacimiento = date_nac.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: '2-digit' });
    }else{
      ft_fecha_nacimiento = 'No registrada'
    }
    console.log(ft_fecha_ingreso);
    document.getElementById('fecha_nacimiento').textContent = ft_fecha_nacimiento
    document.getElementById('fecha_ingreso').textContent = ft_fecha_ingreso

    let estatus_empleado = estatus ==1 ? 'Activo': 'Inactivo'
    document.getElementById('estatus-empleado').textContent = estatus_empleado

    //Seteando documentos
    let clase_cv = cv == 1 ? 'icon-hover' : 'doc_vacio'
    let clase_curp = curp == 1 ? 'icon-hover' : 'doc_vacio'
    let clase_domicilio = domicilio == 1 ? 'icon-hover' : 'doc_vacio'
    let clase_nss = nss == 1 ? 'icon-hover' : 'doc_vacio'
    let clase_ine = ine == 1 ? 'icon-hover' : 'doc_vacio'
    let clase_contrato = contrato == 1 ? 'icon-hover' : 'doc_vacio'
    let clase_bancarios = bancario == 1 ? 'icon-hover' : 'doc_vacio'


    $.ajax({
      type: "post",
      url: "./modelo/empleados/obtener-datos-empleado.php",
      data: { id_empleado },
      dataType: "json",
      success: function (response) {
        if (response.estatus) {
          const docs = response.data.documentos; // asumo que solo usas uno
    
          const documentos = [
            { id_documento: '6',tipo: 'ine', existe: ine, ruta: 'ine', id: 'link_ine' },
            { id_documento: '2',tipo: 'cv', existe: cv, ruta: 'cv', id: 'link_cv' },
            { id_documento: '4',tipo: 'domicilio', existe: domicilio, ruta: 'domicilio', id: 'link_domicilio' },
            { id_documento: '3',tipo: 'curp', existe: curp, ruta: 'curp', id: 'link_curp' },
            { id_documento: '5',tipo: 'nss', existe: nss, ruta: 'nss', id: 'link_imss' },
            { id_documento: '7',tipo: 'contrato', existe: contrato, ruta: 'contrato', id: 'link_contrato' },
            { id_documento: '8',tipo: 'bancario', existe: bancario, ruta: 'bancario', id: 'link_bancario' }
          ];
    
          documentos.forEach(({ existe, ruta, id, id_documento }) => {
           
            const $link = $("#" + id);
            if (existe == 1) {
              const doc = docs.find(d => d[1] == id_documento);
              const path = `src/docs/${ruta}/D${id_empleado}.${doc[3]}`;
              $link.attr("href", path).attr("target", "_blank").css("pointer-events", "auto").css("color", "");
            } else {
              $link.attr("href", "#").removeAttr("target")/* .on("click", e => e.preventDefault()); */
              $link.css("pointer-events", "none").css("color", "gray");
            }
          });
        }
      }
    });
    

    document.getElementById('doc_cv').className=''
    document.getElementById('doc_cv').classList.add(clase_cv)
    document.getElementById('doc_domicilio').classList.add(clase_domicilio)
    document.getElementById('doc_curp').classList.add(clase_curp)
    document.getElementById('doc_imss').classList.add(clase_nss)
    document.getElementById('doc_ine').classList.add(clase_ine)
    document.getElementById('doc_contrato').classList.add(clase_contrato)
    document.getElementById('doc_bancario').classList.add(clase_bancarios)

}

document.getElementById("closeCard").addEventListener("click", function() {
    document.getElementById("expandedCard").classList.add("hidden");
    document.getElementById("overlay").classList.add("hidden");
});


function setEskeleton(){
  $("#card-employe-body").css('justify-content', 'start')
  $("#card-employe-body").css('height', 'auto')
  empleados =[1,2,3,4,5,6,7,8]
  $("#card-employe-body").empty().append(` <div class="row w-100" id="employee-grid"></div>`)
  let container = document.getElementById("employee-grid");
  empleados.forEach(empleado => {
    let card = `
        <div class="col-md-3 mb-2 mt-3">
            <div class="card_eskeleton loading employee-card p-3 text-center">
              <div class="row">
                <div class="col-12 text-center">
                  <img src="./src/img/neumaticos/NA.JPG" class="employee-img img_exq mb-3 border" alt="Empleado">
                </div>
              </div>
                
              <div class="content">
                <h4></h4>
                <div class="description">
                  
                </div>
              </div>
            </div>
        </div>
    `;
    container.innerHTML += card;
});
}