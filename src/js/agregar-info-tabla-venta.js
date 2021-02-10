function agregarInfo(){
    //Funcion que se encargara de mover informacion del producto a una tabla para luego ser procesada como una venta

    idBotonLLanta    =   $("#agregar-producto").attr("idLlanta");
    descripcion      =   $("#description").val();
    modelo           =   $("#modelo").val();
    marca            =   $(".logo-marca").attr("marca");
    cantidad         =   $("#cantidad").val();
    precio           =   $("#precio").val();
    sucursal         =   $("select[id = sucursal] option:selected").text();
    subtotal         =   precio * cantidad;
    
    array = [idBotonLLanta, descripcion, modelo, cantidad, precio, sucursal, subtotal ];

    console.log(array);

    $("#tbody-venta").append("<tr class='producto-individual' "+
            "id='"+idBotonLLanta + "' "+
            "descripcion='"+ descripcion + "' " +
            "modelo='"+ modelo + "'"  +
            "precio-venta='"+ precio + "' " +
            "marca='"+ marca  + "'"  +
            "sucursal='"+ sucursal + "' "  +
            "cantidad='"+ cantidad +
            "'>"+
            "<td>" + idBotonLLanta + "</td>" +
            "<td>"+ descripcion + " modelo: " + modelo + +"</td>" +
            "<td>" + cantidad + "</td>" +
            "<td>$" + precio + "</td>" +
            "<td>$" + subtotal + "</td>" +
            //"<td><img class='logo-marca' src='./src/img/logos/" + value.Marca + ".jpg'></td>" +
            "<td></td>");

    /*if (select == "Pedro Cardenas" ) {
       
        $.ajax({
            type: "method",
            url: "url",
            data: {pedro: idBotonLLanta},
            success: function (response) {
                alert("Correcto, de Pedrito");
            }
        });

    } else if(select == "Sendero") {
        
        $.ajax({
            type: "method",
            url: "url",
            data: {sendero: idBotonLLanta},
            success: function (response) {
                alert("Correcto, de Snedero");
            }
        });


    }*/
}