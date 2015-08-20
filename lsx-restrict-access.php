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
		add_action( 'after_setup_theme', array($this,'register_menus') , 100);
		
		//Call the logged out template
		add_action( 'template_include', array($this,'template_include'), -1 );
		
		//Redirect the user on succeful logout	
		add_filter( 'logout_url', array($this,'logout_redirect'), 10, 2 );
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
	
	/**
	 * Register a new primary menu for the logged out view
	 */
	public function register_menus() {
	
		//TODO - Call all current menu locations and register a logged out version for them
		register_nav_menus( array(
		'primary_logged_out' => __( 'Primary Menu (logged out)', 'lsx-restrict-access' )
		)
		);
	}	
	
	
	/**
	 * Redirect the template
	 */	
	public function template_include($template) {
		if ( !is_user_logged_in() ) {
			
			//Check if there is a tempalte in the theme
			$template = locate_template( array( 'template-login.php' ));
			
			//if there isnt, then display our template.
			if('' == locate_template( array( 'template-login.php' )) && file_exists( plugin_dir_path( __FILE__ ) . "template-login.php" )) {
				$template = plugin_dir_path( __FILE__ ) ."template-login.php";
			}
			
			//die(print_r($template));
		}
		return $template;		
	}
	
	//Add in a filter so the title is forced to a prompt.
		
	//Redirect the user on login to the same page
	//Redirect the user to the page they were on / home.
	
	//Need to make sure the validation redirects back to the form when it fails
	
	/**	
	 * Redirect a user to the homepage on logout.
	 * 
	 * @param string $logout_url URL to redirect to.
	 * @param string $redirect URL the user is going.	
	 */
	public function logout_redirect( $logout_url, $redirect ) {
	    return home_url( $logout_url.'?redirect_to=' . home_url() );
	}
}
$lst_restrict_access = Lsx_Restrict_Access::get_instance();