<?php
require_once __DIR__ . '/../core/class-model.php';

class Customers_Model extends Model {
	public function __construct() {
		$this->load( 'customers' );
	}

	public function get( $email ) {
		$customer = null;

		foreach ( $this->data as $possible_customer ) {
			if ( $possible_customer->email === $email ) {
				$customer = $possible_customer;

				break;
			}
		}

		return $customer;
	}

	public function update( $email, $modified_customer ) {
		$new_data = array();

		foreach ( $this->data as $customer ) {
			if ( $customer->email === $email ) {
				$customer = $modified_customer;
			}

			$new_data[] = $customer;
		}

		return $this->save( 'customers', $new_data );
	}

	public function hash( $text ) {
		return md5( $text );
	}
}
