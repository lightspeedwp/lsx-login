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
	
}