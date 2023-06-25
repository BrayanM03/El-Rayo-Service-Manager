<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
session_start();
// Table's primary key
$primaryKey = 'id';

$table = "vista_inventario";
$id_sucursal = $_GET['id_sucursal'];
$where = "id_sucursal = ". $id_sucursal; 
 

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'id', 'dt' => 0 ),
	array( 'db' => 'Ancho',  'dt' => 1 ),
	array( 'db' => 'Proporcion', 'dt' => 2 ),
	array( 'db' => 'Diametro', 'dt' => 3 ),
	array( 'db' => 'Descripcion', 'dt' => 4 ),
	array( 'db' => 'Marca',  'dt' => 5 ),
	array( 'db' => 'Modelo',   'dt' => 6 ),
	array( 'db' => 'precio_Inicial',   'dt' => 7 ),
	array( 'db' => 'precio_Venta',   'dt' => 8 ),
	array( 'db' => 'precio_Mayoreo',   'dt' => 9 ),
	array( 'db' => 'Fecha',   'dt' => 10 ),
	array( 'db' => 'id_Llanta',   'dt' => 11 ),
	array( 'db' => 'Codigo',   'dt' => 12 ),
	array( 'db' => 'Sucursal',   'dt' => 13 ),
	array( 'db' => 'id_sucursal',   'dt' => 14 ),
	array( 'db' => 'Stock',   'dt' => 15 ),
/* 	array(
		'db'        => 'start_date',
		'dt'        => 4,
		'formatter' => function( $d, $row ) {
			return date( 'jS M y', strtotime($d));
		}
	),
	array(
		'db'        => 'salary',
		'dt'        => 5,
		'formatter' => function( $d, $row ) {
			return '$'.number_format($d);
		}
	) */
);

 
// SQL server connection information
include_once 'credenciales.php';
$sql_details = $credenciales_db;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $where)
);


