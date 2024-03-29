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
//session_start();
// DB table to use
$table = 'vista_ventas';

// Table's primary key
$primaryKey = 'id';
/* $id_sucursal = $_SESSION['id_sucursal'];
if($_SESSION['rol']!= 1 && $_SESSION['id_usuario'] != 7){
	$where = 'id_sucursal ='.$id_sucursal;
}else{
	$where ='';
} */
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'id', 'dt' => 0 ),
	array( 'db' => 'Fecha',  'dt' => 1 ), 
	array( 'db' => 'sucursal', 'dt' => 2 ),
	array( 'db' => 'nombre', 'dt' => 3 ),
	array( 'db' => 'Nombre_Cliente', 'dt' => 4 ),
	array( 'db' => 'Cantidad',  'dt' => 5 ),
	array( 'db' => 'Total',   'dt' => 6 ),
	array( 'db' => 'tipo',   'dt' => 7 ),
	array( 'db' => 'estatus',   'dt' => 8),
	array( 'db' => 'folio',   'dt' => 9),
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
include_once '../credenciales.php';
$sql_details = $credenciales_db;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( '../ssp.class.php' );

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns)
);


