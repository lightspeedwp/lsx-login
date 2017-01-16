<?php
/*
 * Plugin Name: LSX Login
 * Plugin URI:  https://www.lsdev.biz/product/lsx-login/
 * Description:	The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.
 * Version:     1.0.3
 * Author:      LightSpeed
 * Author URI:  https://www.lsdev.biz/
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: lsx-login
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('LSX_LOGIN_PATH', plugin_dir_path( __FILE__ ) );
define('LSX_LOGIN_CORE', __FILE__ );
define('LSX_LOGIN_URL',  plugin_dir_url( __FILE__ ) );
define('LSX_LOGIN_VER',  '1.0.3' );

/* ======================= The API Classes ========================= */

if(!class_exists('LSX_API_Manager')){
	require_once('classes/class-lsx-api-manager.php');
}

/**
 * Runs once when the plugin is activated.
 */
function lsx_login_activate_plugin() {
	$lsx_to_password = get_option('lsx_api_instance',false);
	if(false === $lsx_to_password){
		update_option('lsx_api_instance',LSX_API_Manager::generatePassword());
	}
}
register_activation_hook( __FILE__, 'lsx_login_activate_plugin' );

/**
 *	Grabs the email and api key from the LSX Login Settings.
 */
function lsx_login_options_pages_filter($pages){
	$pages[] = 'lsx-settings';
	$pages[] = 'lsx-to-settings';
	return $pages;
}
add_filter('lsx_api_manager_options_pages','lsx_login_options_pages_filter',10,1);

function lsx_login_api_admin_init(){
	global $lsx_login_api_manager;
	
	if(class_exists('Tour_Operator')) {
		$options = get_option('_lsx-to_settings', false);
	}else{
		$options = get_option('_lsx_settings', false);
		if (false === $options) {
			$options = get_option('_lsx_lsx-settings', false);
		}
	}

	$data = array('api_key'=>'','email'=>'');

	if(false !== $options && isset($options['api'])){
		if(isset($options['api']['lsx-login_api_key']) && '' !== $options['api']['lsx-login_api_key']){
			$data['api_key'] = $options['api']['lsx-login_api_key'];
		}
		if(isset($options['api']['lsx-login_email']) && '' !== $options['api']['lsx-login_email']){
			$data['email'] = $options['api']['lsx-login_email'];
		}
	}

	$instance = get_option( 'lsx_api_instance', false );
	if(false === $instance){
		$instance = LSX_API_Manager::generatePassword();
	}

	$api_array = array(
		'product_id'	=>		'LSX Login',
		'version'		=>		'1.0.3',
		'instance'		=>		$instance,
		'email'			=>		$data['email'],
		'api_key'		=>		$data['api_key'],
		'file'			=>		'lsx-login.php'
	);
	$lsx_login_api_manager = new LSX_API_Manager($api_array);
}
add_action('admin_init','lsx_login_api_admin_init');

/* ======================= Below is the Plugin Class init ========================= */

require_once( LSX_LOGIN_PATH . 'classes/class-lsx-login.php' );
