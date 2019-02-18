<?php
/*
 * Plugin Name: LSX Login
 * Plugin URI:  https://www.lsdev.biz/product/lsx-login/
 * Description:	The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.
 * Version:     1.0.5
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
define('LSX_LOGIN_VER',  '1.0.5' );

if(!function_exists('cmb_init')){
	if (is_file(LSX_LOGIN_PATH.'vendor/Custom-Meta-Boxes/custom-meta-boxes.php')) {
		require LSX_LOGIN_PATH.'vendor/Custom-Meta-Boxes/custom-meta-boxes.php';
	}
}

/* ======================= Below is the Plugin Class init ========================= */

require_once( LSX_LOGIN_PATH . 'classes/class-lsx-login.php' );
