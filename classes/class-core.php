<?php
/**
 * The core file
 *
 * @package LSX\Login
 */

namespace LSX\Login;

/**
 * The main file loading the rest of the files
 *
 * @package   FISA_Enhancements
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2023 LightSpeed
 */
class Core {

	/**
	 * Contructor
	 */
	public function __construct() {
	}

	/**
	 * Loads the actions we need.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_block_core_loginout' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_block' ) );
	}

	public function register_block() {
		wp_enqueue_script(
			'lsx-login-variations',
			LSX_LOGIN_URL . 'build/index.js'
		);
	}
	
	
	/**
	 * Renders the `lsx/loginout` block on server.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the login-out link or form.
	 */
	public function render_block_core_loginout( $attributes ) {
		// Build the redirect URL.
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
		$classes  = is_user_logged_in() ? 'logged-in' : 'logged-out';
		$contents = wp_loginout(
			isset( $attributes['redirectToCurrent'] ) && $attributes['redirectToCurrent'] ? $current_url : '',
			false
		);
	
		// If logged-out and displayLoginAsForm is true, show the login form.
		if ( ! is_user_logged_in() && ! empty( $attributes['displayLoginAsForm'] ) ) {
			// Add a class.
			$classes .= ' has-login-form';
	
			// Get the form.
			$contents = wp_login_form( array( 'echo' => false ) );
		}
	
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );
	
		return '<div ' . $wrapper_attributes . '>' . $contents . '</div>';
	}
	
	/**
	 * Registers the `lsx/loginout` block on server.
	 */
	public function register_block_core_loginout() {
		register_block_type_from_metadata(
			LSX_LOGIN_PATH . 'block/block.json',
			array(
				'render_callback' => 'render_block_core_loginout',
			)
		);
	}	
}
