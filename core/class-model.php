<?php
class Model {
	protected $data = array();

	protected function load( $table ) {
		$file = $this->get_file( $table );

		if ( file_exists( $file ) ) {
			$contents = file_get_contents( $file );

			if ( $contents ) {
				$this->data = json_decode( $contents );
			}
		}
	}

	protected function save( $table, $data ) {
		$file    = $this->get_file( $table );
		$success = false;

		if ( false !== file_put_contents( $file, json_encode( $data ) ) ) {
			$this->data = $data;

			$success = true;
		}

		return $success;
	}

	private function get_file( $table ) {
		return sprintf(
			'%s/../data/%s.json',
			__DIR__,
			$table
		);
	}
}
