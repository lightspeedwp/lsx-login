<?php
/*
Plugin Name: LSX Login
Plugin URI: {add_in}
Description: Force users to login to view your site. Primarily built for the LSX theme
Author: Warwick
Author URI: http://wordpress.org/
Version: 1.0
Text Domain: lsx-login
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

require 'inc/template-tags.php';

/**
 * Main plugin class.
 *
 * @package Lsx_Restrict_Access
 * @author  Warwick
 */

class Lsx_Login {
	
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
		
		//Force a 1 column layout
		add_filter( 'lsx_layout', array($this,'logout_layout_filter') , 1 , 100 );
		
		//Enqueue the scrips
		add_action( 'wp_enqueue_scripts', array($this,'scripts') );
		
		//ajax handlers
		add_action( 'wp_ajax_lsx_login', array( $this, 'do_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_lsx_login', array( $this, 'do_ajax_login' ) );		
		
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
	 * Enqueue scripts and styles.
	 *
	 */
	function scripts() {
		if(!is_user_logged_in()){
			wp_enqueue_script('lsx_login_script', plugin_dir_url(__FILE__) . 'assets/js/lsx-login.js', array('masonry'), null, false);
			$param_array = array(
					'ajax_url' 			=> admin_url('admin-ajax.php'),
					'empty_username'	=> __('The username field is empty.','lsx-login'),
					'empty_password'	=> __('The password field is empty.','lsx-login')
			);
			wp_localize_script( 'lsx_login_script', 'lsx_login_params', $param_array );
		}		
	}	
	
	/**
	 * Register a new primary menu for the logged out view
	 */
	public function register_menus() {
		//TODO - Call all current menu locations and register a logged out version for them
		register_nav_menus( array(
			'primary_logged_out' => __( 'Primary Menu (logged out)', 'lsx-login' )
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
			if('' == locate_template( array( 'template-login.php' )) && file_exists( plugin_dir_path( __FILE__ ) . "templates/template-login.php" )) {
				$template = plugin_dir_path( __FILE__ ) ."templates/template-login.php";
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
	
	
	
	/**
	 * generate the login form
	 *
	 */
	public function login_form() {
			
		//if there isnt, then display our template.
		if('' == locate_template( array( 'content-login.php' )) && file_exists( plugin_dir_path( __FILE__ ) . "templates/content-login.php" )) {
			include plugin_dir_path( __FILE__ ) ."templates/content-login.php";
		}else{
			get_template_part( 'content', 'login' );
		}
			
	}

	/**
	 * generate the login form
	 *
	 */
	public function password_reset_form() {
		//if there isnt, then display our template.
		if('' == locate_template( array( 'content-password-reset.php' )) && file_exists( plugin_dir_path( __FILE__ ) . "templates/content-password-reset.php" )) {
			include plugin_dir_path( __FILE__ ) ."templates/content-password-reset.php";
		}else{
			get_template_part( 'content', 'password-reset' );
		}	
	}
	
	/**
	 * Forces the Logged out page to be 1 Column
	 *
	 */
	function logout_layout_filter($layout) {
	
		if(!is_user_logged_in()){
			$layout = '1c';
		}
		return $layout;
	}

	
	/**
	 * Either logs the user in, or prompts an error.
	 *
	 */
	public function do_ajax_login() {
		
		
		if(isset($_POST['method']) && 'login' == $_POST['method']){
			
			$result = array();
			if(isset($_POST['log']) && username_exists($_POST['log'])){
				$user = wp_signon();
				
				if ( ! is_wp_error( $user ) ) {
					$result['success']  = 1;
				}else{
					$result['success']  = 3;
					//TODO Fix this encapsulation
					$result['message']  = __('The password you entered for the username '.$_POST['log'].' is incorrect.','lsx-login');					
				}				
				
			}else{
				$result['success']  = 2;
				$result['message']  = __('Invalid Username','lsx-login');
			}
			
			echo json_encode( $result );
		}else{
			echo false;
		}
		die();
	}
	
	
}
$lst_login = Lsx_Login::get_instance();

/*
function lsx_nav_menu(){
	$nav_menu = get_theme_mod('nav_menu_locations',false);

    if(false != $nav_menu && 0 != $nav_menu['primary'] && 0 != $nav_menu['primary_logged_out']){ ?>
		<nav class="navmenu navmenu-fixed-right offcanvas" role="navigation">
	    	<?php
	    	if(is_user_logged_in()){
				wp_nav_menu( array(
					'menu' => $nav_menu['primary'],
					'depth' => 2,
					'container' => false,
					'menu_class' => 'nav navbar-nav',
					'walker' => new lsx_bootstrap_navwalker())
				);
			}else{
					wp_nav_menu( array(
					'menu' => $nav_menu['primary_logged_out'],
					'depth' => 2,
					'container' => false,
					'menu_class' => 'nav navbar-nav',
					'walker' => new lsx_bootstrap_navwalker())
					);
			}
			?>
	   	</nav>
    <?php } elseif(is_customize_preview()) { ?>
    		<nav class="primary-navbar collapse navbar-collapse" role="navigation">
    			<div class="alert alert-info" role="alert"><?php _e('Create a menu and assign it here via the "Navigation" panel.','lsx');?></div>
    		</nav>
    </div>
  	<?php }
}*/