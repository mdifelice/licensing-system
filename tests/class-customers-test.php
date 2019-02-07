<?php
require_once __DIR__ . '/../controllers/class-customers-controller.php';

class Customers_Test extends PHPUnit\Framework\TestCase {
	private $controller;

	public function __construct() {
		$this->controller = Customers_Controller::load();
		$this->setOutputCallback( function() {
		} );

		parent::__construct();
	}

	public function test_logout() {
		$this->controller->logout();

		$this->assertTrue( ! isset( $_SESSION['user'] ) );
	}

	public function test_login_error() {
		$this->controller->login();

		$this->assertTrue( ! isset( $_SESSION['user'] ) );
	}

	public function test_login_ok() {
		$_POST['email']    = 'mdifelice@live.com.ar';
		$_POST['password'] = '123456';

		$this->controller->login();

		$this->assertEquals( @$_SESSION['user']->email, 'mdifelice@live.com.ar' );
	}

	public function test_buy_invalid_license() {
		$_SESSION['user']->license = 2;

		$this->controller->buy_license( 1 );

		$this->assertEquals( 2, $_SESSION['user']->license );
	}

	public function test_add_invalid_website() {
		$_SESSION['user']->websites = array(
			'https://www.google.com',
			'https://www.facebook.com',
			'https://www.twitter.com',
		);

		$_POST['website'] = 'https://9gag.com';

		$this->controller->add_website();

		$this->assertEquals( 3, count( $_SESSION['user']->websites ) );
	}
}
