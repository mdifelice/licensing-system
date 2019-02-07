<?php
require_once __DIR__ . '/../core/class-controller.php';

class Core_Test extends PHPUnit\Framework\TestCase {
	public function test_data_folder_is_writable() {
		$this->assertTrue( is_writable( __DIR__ . '/../data' ) );
	}
}
