function traerModalEstadoCuenta(){

    Swal.fire({
        icon: 'info',
        html: `
        <div class="container">
            <div class="row">
            <div class="col-11">
            <label for="cliente">Cliente</label>
                    <select id="clientes-credito" class="form-control selectpicker" data-live-search="true">

                    </select>
                </div> 
            </div>
        </div>
        `,
        confirmButtonText:'Obtener',
        showCloseButton:true,
        didOpen: ()=>{
          $("#clientes-credito").selectpicker();
          $("#clientes-credito").on("shown.bs.select", function () {
        
              cargarClientesConCredito(); // Cargar la primera página
              $(".bs-searchbox input").on("keyup", function (e) {
                $("#clientes-credito").empty();
                currentPage = 1;
                cargarClientesConCredito(e.target.value, currentPage); // Nueva búsqueda, reiniciar la página
              });
          
              $(".dropdown-menu.inner.dropdown-menu").on("scroll", function () {
               
                const scrollPosition = $(this).scrollTop() + $(this).innerHeight();
                const scrollHeight = $(this)[0].scrollHeight;
          
                // Si el usuario está en la parte inferior y no se está cargando, cargar la siguiente página
                if (scrollPosition >= scrollHeight && !isLoading) {
                  currentPage++;
                  const query = $(".bs-searchbox input").val();
                  cargarClientesConCredito(query, currentPage);
                }
              });
          });
        }
    }).then((r)=>{
      if(r.isConfirmed){
        let id_cliente = $("#clientes-credito").val()
        window.open('./modelo/creditos/reporte-estado-cuenta.php?id_cliente='+ id_cliente, '_blank');
      }
    })
}

let currentPage = 1;
let isLoading = false;
async function cargarClientesConCredito(query = "", page = 1) {
    // Si ya está cargando, no haga otra solicitud
    if (isLoading) return;
    isLoading = true;

    const response = await fetch(
      `./modelo/creditos/clientes-con-credito.php?query=${query}&page=${page}`
    );
    const clientes = await response.json();

    clientes.data.forEach((cliente) => {
      const option = document.createElement("option");
      option.value = cliente.id;
      option.textContent = cliente.nombre_cliente;
      $("#clientes-credito").append(option);
    });

    $("#clientes-credito").selectpicker("refresh");
    isLoading = false;
}

