<?php
/*
 * Plugin Name: LSX Login
 * Plugin URI:  https://www.lsdev.biz/product/lsx-login/
 * Description:	The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.
 * Version:     2.0.0
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
define('LSX_LOGIN_VER',  '2.0.0' );

/* ======================= Below is the Plugin Class init ========================= */

require_once( LSX_LOGIN_PATH . 'classes/class-core.php' );

/**
 * Returns and instance of the LSX Sharing plugin.
 *
 * @return object \lsx\sharing\Sharing();
 */
function lsx_login() {
	global $lsx_login;
	if ( null === $lsx_login ) {
		$lsx_login = new \LSX\Login\Core();
		$lsx_login->init();
	}
    return $lsx_login;
}
add_action( 'plugins_loaded', 'lsx_login' );
