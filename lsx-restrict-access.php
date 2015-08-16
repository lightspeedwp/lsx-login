<?php
/*
Plugin Name: LSX Restrict Access
Plugin URI: {add_in}
Description: Force users to login to view your site. Primarily built for the LSX and TwentyFifteen theme
Author: Warwick
Author URI: http://wordpress.org/
Version: 1.0
Text Domain: lsx-restrict-access
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * Main plugin class.
 *
 * @package Lsx_Restrict_Access
 * @author  Warwick
 */

class Lsx_Restrict_Access {
	
	/**
	 * Holds class instance
	 *
	 * @var      object|Lsx_Restrict_Access
	 */
	protected static $instance = null;	
	
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {
		//Register our logged out menu location, make sure this is done at the very end so it doesnt mess with any currently set up menus. 
		add_action( 'after_setup_theme', array($this,'gts_setup') , 100);
	}
	
	/**
	 * Return an instance of this class.
	 * 
	 * @return    object|Lsx_Restrict_Access    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}	
	
	
	//Redirect the template
	
	//Redirect the user on login to the same page
	
	//Redirect the user to the page they were on / home.
	
	//Need to make sure the validation redirects back to the form when it fails.
	
	/**
	 * Register a new primary menu for the logged out view
	 */	
	function register_menus() {
		
		//TODO - Call all current menu locations and register a logged out version for them
		register_nav_menus( array(
			'primary_logged_out' => __( 'Primary Menu (logged out)', 'lsx-restrict-access' )
			)
		);
	}
	
	//Overwrite the LSX Nav function to call our logged out menu. 
	
}