<?php
require_once __DIR__ . '/class-message.php';

abstract class Controller {
	private static $instance = null;

	private $message = null;

	public final static function load() {
		if ( null === self::$instance ) {
			$class = get_called_class();

			self::$instance = new $class;

			if ( isset( $_GET['message_text'] ) ) {
				$message_text = $_GET['message_text'];

				if ( isset( $_GET['message_type'] ) ) {
					$message_type = $_GET['message_type'];
				} else {
					$message_type = 'primary';
				}

				self::$instance->set_message( $message_text, $message_type );
			}

			if ( PHP_SESSION_NONE === session_status() ) {
				session_start();
			}
		}

		return self::$instance;
	}

	protected function is_authenticated() {
		return ! empty( $_SESSION['user'] );
	}

	protected function unset_session() {
		if ( isset( $_SESSION['user'] ) ) {
			unset( $_SESSION['user'] );
		}
	}

	protected function set_session( $user ) {
		$_SESSION['user'] = $user;
	}

	protected function get_session() {
		return isset( $_SESSION['user'] ) ? $_SESSION['user'] : null;
	}

	protected function get_session_data( $field ) {
		return isset( $_SESSION['user']->$field ) ? $_SESSION['user']->$field : null;
	}

	protected function set_session_data( $field, $data ) {
		if ( isset( $_SESSION['user'] ) ) {
			$_SESSION['user']->$field = $data;
		}
	}

	protected function view( $view = null ) {
		$section = preg_replace( '/_Controller$/', '', get_called_class() );
		$section = str_replace( '_', '-', $section );
		$section = strtolower( $section );

		$this->render( 'layout/header' );

		if ( $this->is_authenticated() ) {
			if ( $view ) {
				$this->render( $section . '/' . $view );
			}
		} else {
			$this->render( 'layout/login' );
		}

		$this->render( 'layout/footer' );
	}

	protected function set_message( $text, $type ) {
		$this->message = new Message( $text, $type );
	}

	protected function get_message() {
		return $this->message;
	}

	protected function get_post_parameter( $variable ) {
		return isset( $_POST[ $variable ] ) ? $_POST[ $variable ] : null;
	}

	protected function refresh( $message_text = null, $message_type = null ) {
		if ( isset( $_GET['section'] ) ) {
			$section = $_GET['section'];
		} else {
			$section = null;
		}

		if ( isset( $_GET['action'] ) ) {
			$action = $_GET['action'];
		} else {
			$action = null;
		}

		$this->redirect( $section, $action, $message_text, $message_type );
	}

	protected function redirect( $section = null, $action = null, $message_text = null, $message_type = null, $id = null ) {
		$url = $this->link( $section, $action, $message_text, $message_type, $id );

		header(
			sprintf(
				'Location: %s',
				$url
			)
		);

		exit;
	}

	protected function link( $section = null, $action = null, $message_text = null, $message_type = null, $id = null ) {
		$arguments = array();

		if ( $section ) {
			$arguments['section'] = $section;

			if ( $action ) {
				$arguments['action'] = $action;

				if ( $id ) {
					$arguments['id'] = $id;
				}
			}
		}

		if ( $message_text ) {
			$arguments['message_text'] = $message_text;

			if ( $message_type ) {
				$arguments['message_type'] = $message_type;
			}
		}

		$query = http_build_query( $arguments );
		$url   = ( $query ? '?' : '' ) . $query;

		return $url;
	}

	private function render( $view ) {
		$file = sprintf(
			'%s/../views/%s.phtml',
			__DIR__,
			$view
		);

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
}
