<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once( dirname( __FILE__ ) . '/libraries/wp-async-request.php' );
}*/

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once( LSX_LOGIN_PATH . '/vendor/wp-background-processing/wp-background-process.php' );
}

class LSX_Login_Emails {

	protected static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->bg_process = new LSX_Login_Email_Process();

		add_action( 'wp_ajax_lsx_welcome_users', array( $this, 'add_emails' ) );
		# add_action( 'wp_ajax_nopriv_lsx_welcome_users', array( $this, 'add_emails' ) );
	}

	/**
	 * Main Instance.
	 *
	 * @return LSX_Login_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function add_emails() {
		$roles = $_GET['welcome_email_user_roles'];

		if ( ! empty( $roles ) ) {
			$users = get_users( array(
				'role__in' => $roles,
			) );

			foreach ( $users as $user ) {
				$this->bg_process->push_to_queue( $user );
				$this->bg_process->save()->dispatch();
			}
		}

		wp_send_json_success();
		die();
	}

}
