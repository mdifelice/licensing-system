<?php
require_once __DIR__ . '/../core/class-controller.php';
require_once __DIR__ . '/../models/class-customers-model.php';
require_once __DIR__ . '/../models/class-licenses-model.php';

class Customers_Controller extends Controller {
	protected $licenses,
			  $websites;

	protected function __construct() {
		$this->customers_model = new Customers_Model();
		$this->licenses_model  = new Licenses_Model();
	}

	public function index( $error = null ) {
		if ( $error ) {
			$this->set_message( $error, 'danger' );
		}

		$this->view( 'index' );
	}

	public function login() {
		$email    = $this->get_post_parameter( 'email' );
		$password = $this->get_post_parameter( 'password' );

		try {
			$user = $this->customers_model->get( $email );

			if ( null === $user ) {
				throw new Exception( 'Unknown email.' );
			}

			if ( $user->password_hash === $this->customers_model->hash( $password ) ) {
				throw new Exception( 'Unknown password.' );
			}

			$this->set_session( $user );

			$this->redirect( 'customers', 'index' );
		} catch ( Exception $e ) {
			$this->set_message( $e->getMessage(), 'danger' );

			$this->index();
		}
	}

	public function logout() {
		$this->unset_session();

		$this->redirect( 'customers', 'index', 'Thanks for using the system.' );
	}

	public function licenses() {
		$this->licenses = $this->licenses_model->list();

		$this->view( 'licenses' );
	}

	public function buy_license( $license_id ) {
		try {
			$license_id = intval( $license_id );

			$license = $this->licenses_model->get( $license_id );

			if ( ! $license ) {
				throw new Exception( 'Invalid license.' );
			}

			if ( ! $this->is_license_enabled( $license_id ) ) {
				throw new Exception( 'Cannot buy this license.' );
			}

			$error = $this->update_customer( 'license', $license_id );

			if ( $error ) {
				throw new Exception( $error );
			}

			$this->redirect(
				'customers',
				'licenses',
				sprintf(
					'Thanks for purchasing the license %s!',
					$license->name
				),
				'success'
			);
		} catch ( Exception $e ) {
			$this->set_message( $e->getMessage(), 'danger' );

			$this->licenses();
		}
	}

	public function websites() {
		$this->view( 'websites' );
	}

	public function add_website() {
		try {
			$website = filter_var( $this->get_post_parameter( 'website' ), FILTER_VALIDATE_URL );

			if ( ! $website ) {
				throw new Exception( 'Invalid URL.' );
			}

			if ( ! $this->can_buy_website() ) {
				throw new Exception( 'Your license does not allow you more websites.' );
			}

			$websites = $this->get_session_data( 'websites' );

			if ( in_array( $website, $websites, true ) ) {
				throw new Exception( 'Repeated website.' );
			}

			$websites[] = $website;

			$error = $this->update_customer( 'websites', $websites );

			if ( $error ) {
				throw new Exception( $error );
			}

			$this->redirect(
				'customers',
				'websites',
				sprintf(
					'Thanks for adding the website %s!',
					$website
				),
				'success'
			);
		} catch ( Exception $e ) {
			$this->set_message( $e->getMessage(), 'danger' );

			$this->websites();
		}
	}

	protected function is_license_enabled( $license_id ) {
		$enabled = false;
		$license = $this->licenses_model->get( $license_id );

		if ( $license ) {
			$customer_license_id = $this->get_session_data( 'license' );
			$customer_license    = $this->licenses_model->get( $customer_license_id );

			if ( ! $customer_license ) {
				$enabled = true;
			} else {
				if ( $license->price > $customer_license->price ) {
					$enabled = true;
				}
			}
		}

		return $enabled;
	}

	protected function can_buy_website() {
		$can_buy    = false;
		$license_id = $this->get_session_data( 'license' );
		$license    = $this->licenses_model->get( $license_id );

		if ( $license ) {
			$websites = $this->get_session_data( 'websites' );

			if ( $websites ) {
				$total_websites = count( $websites );
			} else {
				$total_websites = 0;
			}

			if ( ! $license->sites || $total_websites + 1 <= $license->sites ) {
				$can_buy = true;
			}
		}

		return $can_buy;
	}

	protected function update_customer( $field, $data ) {
		$error = null;

		try {
			$session = $this->get_session();

			if ( ! $session ) {
				throw new Exception( 'Cannot retrieve current session.' );
			}

			$session->$field = $data;

			if ( ! $this->customers_model->update( $this->get_session_data( 'email' ), $session ) ) {
				throw new Exception( 'Cannot save changes.' );
			}

			$this->set_session( $session );
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		return $error;
	}
}
