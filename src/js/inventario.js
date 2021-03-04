$(document).ready(function() {
  

 
   /* columnDefs = [{
      title: "Codigo",
      data:  "id"
    }, {
      title: "Descripción",
      data:  "Descripcion"
    }, {
      title: "Modelo",
      data:  "Modelo"
    },  {
      title: "Cantidad"
    }, {
      title: "Precio",
      data:  "precio_Inicial"
    }, {
      title: "Precio de venta",
      data:  "precio_Venta"
    }, {
      title: "Accion",
      data: null
    }];*/
    
    table = $('#inventario').DataTable({
      
      
        ajax: {
            method: "POST",
            url: "./modelo/traerInventario.php"
        },  
  
      columns: [   
        { 
            title: "Codigo",
            data: "id"},
        { data: "Descripcion" },
        { data: "Modelo"},
        { data: "precio_Inicial"},
        {
          data: null,
          className: "celda-acciones",
          render: function () {
            return '<button type="button" class="buttonEditar btn btn-warning"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button>';
          },
        },
      ],
      paging: true,
      searching: true,
      scrollY: "600px",
      info: true,
      responsive: true,
      //responsive: true,
     
    
      
    });

});