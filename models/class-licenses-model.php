<?php
require_once __DIR__ . '/../core/class-model.php';

class Licenses_Model extends Model {
	public function __construct() {
		$this->load( 'licenses' );
	}

	public function list() {
		return $this->data;
	}

	public function get( $license_id ) {
		$license = null;

		foreach ( $this->data as $possible_license ) {
			if ( $possible_license->id === $license_id ) {
				$license = $possible_license;

				break;
			}
		}

		return $license;
	}
}
