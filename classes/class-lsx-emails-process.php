<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class LSX_Login_Email_Process extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'lsx_emailer';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $user ) {
		$options = $this->get_options();

		if ( ! has_filter( 'wp_mail_content_type', array( $this, 'welcome_users_email_content_type' ) ) ) {
			add_filter( 'wp_mail_content_type', array( $this, 'welcome_users_email_content_type' ) );
		}

		if ( ! has_filter( 'wp_mail_from', array( $this, 'welcome_users_email_from' ) ) ) {
			add_filter( 'wp_mail_from', array( $this, 'welcome_users_email_from' ) );
		}

		if ( ! has_filter( 'wp_mail_from_name', array( $this, 'welcome_users_email_from_name' ) ) ) {
			add_filter( 'wp_mail_from_name', array( $this, 'welcome_users_email_from_name' ) );
		}

		$subject = __( 'Welcome to LSX', 'lsx' );

		if ( isset( $options ) && ! empty( $options['login']['welcome_email_subject'] ) ) {
			$subject = $options['login']['welcome_email_subject'];
		}

		$message = '';

		if ( isset( $options ) && ! empty( $options['login']['welcome_email_message'] ) ) {
			$message = $options['login']['welcome_email_message'];
		}

		$email = $user->user_email;
		$first_name = $user->first_name;
		$last_name = $user->last_name;
		$my_account = $this->get_my_account_url();

		if ( empty( $first_name ) && empty( $last_name ) ) {
			$first_name = $user->display_name;
		}

		$message = str_replace( '{first_name}', $first_name, $message );
		$message = str_replace( '{last_name}', $last_name, $message );
		$message = str_replace( '{my_account}', $my_account, $message );
		$message = preg_replace( '/\n/', '<br>', $message );

		error_log( $first_name . ' ' . $last_name . ' <' . $email . '>' );
		// $mail = wp_mail( $email, $subject, $message );

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
	}

	public function welcome_users_email_content_type( $content_type ) {
		$content_type = 'text/html';
		return $content_type;
	}

	public function welcome_users_email_from( $from ) {
		$options = $this->get_options();

		if ( isset( $options ) && ! empty( $options['login']['welcome_email_from_email'] ) ) {
			$from = $options['login']['welcome_email_from_email'];
		}

		return $from;
	}

	public function welcome_users_email_from_name( $from_name ) {
		$options = $this->get_options();

		if ( isset( $options ) && ! empty( $options['login']['welcome_email_from_name'] ) ) {
			$from_name = $options['login']['welcome_email_from_name'];
		}

		return $from_name;
	}

	public function get_options() {
		if ( class_exists( 'Tour_Operator' ) ) {
			$options = get_option( '_lsx-to_settings', false );
		} else {
			$options = get_option( '_lsx_settings', false );

			if ( false === $options ) {
				$options = get_option( '_lsx_lsx-settings', false );
			}
		}

		return $options;
	}

	public function get_my_account_url() {
		$options = $this->get_options();
		$my_account_id = false;

		if ( isset( $options['login']['my_account_id'] ) ) {
			$my_account_id = $options['login']['my_account_id'];
		}

		if ( false !== $my_account_id ) {
			$my_account_page = get_post( $my_account_id );
			$account_slug = $my_account_page->post_name;
		} else {
			$account_slug = 'my-account';
		}

		return site_url( '/' . $account_slug . '/' );
	}

}
