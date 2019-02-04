<?php
function call_action( $section, $action, $id, $error = null ) {
	$response  = null;
	$base_name = strtolower( $section ) . '-controller';
	$file      = __DIR__ . '/controllers/class-' . $base_name . '.php';

	try {
		if ( ! is_file( $file ) ) {
			throw new Exception( 'Invalid section.' );
		}

		require_once $file;

		$class  = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $base_name ) ) );
		$method = str_replace( '-', '_', strtolower( $action ) );

		$controller = call_user_func( array( $class, 'load' ) );

		if ( ! method_exists( $controller, $method ) ) {
			throw new Exception( 'Invalid action.' );
		}

		call_user_func( array( $controller, $method ), $id );
	} catch ( Exception $e ) {
		call_action( 'customers', 'index', $e->getMessage() );
	}
}

$section = null;
$action  = null;
$id      = null;

if ( isset( $_GET['section'] ) ) {
	$section = $_GET['section'];
}

if ( isset( $_GET['action'] ) ) {
	$action = $_GET['action'];
}

if ( isset( $_GET['id'] ) ) {
	$id = $_GET['id'];
}

call_action( $section, $action, $id );
