<?php
/*
 * Plugin Name: LSX Login
 * Plugin URI:  https://www.lsdev.biz/product/lsx-login/
 * Description:	The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.
 * Version:     1.2
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

define('LSX_LOGIN_PATH',  plugin_dir_path( __FILE__ ) );
define('LSX_LOGIN_CORE',  __FILE__ );
define('LSX_LOGIN_URL',  plugin_dir_url( __FILE__ ) );
define('LSX_LOGIN_VER',  '1.0.0' );

require 'inc/template-tags.php';
require 'inc/class-login-widget.php';

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
	$pages[] = 'to-settings';
	return $pages;
}
add_filter('lsx_api_manager_options_pages','lsx_login_options_pages_filter',10,1);

function lsx_login_api_admin_init(){
	global $lsx_login_api_manager;
	
	if(class_exists('Tour_Operator')) {
		$options = get_option('_to_settings', false);
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
		'version'		=>		'1.0.1',
		'instance'		=>		$instance,
		'email'			=>		$data['email'],
		'api_key'		=>		$data['api_key'],
		'file'			=>		'lsx-login.php'
	);
	$lsx_login_api_manager = new LSX_API_Manager($api_array);
}
add_action('admin_init','lsx_login_api_admin_init');


/**
 * Main plugin class.
 *
 * @package Lsx_Login
 * @author  Warwick
 */

class Lsx_Login {
	
	/**
	 * Holds class instance
	 *
	 * @var      object|Lsx_Login
	 */
	protected static $instance = null;	
	
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {

		if(class_exists('Tour_Operator')) {
			$options = get_option('_to_settings', false);
		}else{
			$options = get_option('_lsx_settings', false);
			if (false === $options) {
				$options = get_option('_lsx_lsx-settings', false);
			}
		}
		$this->options = $options;

		//Include the Settings Class
		add_action( 'init', array( $this, 'create_settings_page' ), 200 );
		add_filter( 'lsx_framework_settings_tabs', array( $this, 'register_tabs' ), 200, 1 );

		//Register our logged out menu location, make sure this is done at the very end so it doesnt mess with any currently set up menus. 
		add_action( 'after_setup_theme', array($this,'register_menus') , 100);
		
		//Call the logged out template
		add_action( 'template_include', array($this,'template_include'), -1 );
		
		//Overwrite the title of the pages
		//add_filter( 'the_title', array($this,'the_title_filter') , 2 , 20 );
		
		//Redirect the user on succeful logout	
		add_filter( 'logout_url', array($this,'logout_redirect'), 10, 2 );
		
		//Force a 1 column layout
		add_filter( 'lsx_layout', array($this,'logout_layout_filter') , 1 , 100 );
		
		//Enqueue the scrips
		add_action( 'wp_enqueue_scripts', array($this,'scripts') ,100 );
		
		//ajax handlers
		add_action( 'wp_ajax_lsx_login', array( $this, 'do_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_lsx_login', array( $this, 'do_ajax_login' ) );	

		add_action( 'wp_ajax_lsx_reset', array( $this, 'do_ajax_reset' ) );
		add_action( 'wp_ajax_nopriv_lsx_reset', array( $this, 'do_ajax_reset' ) );		
		
		add_action( 'wp_ajax_lsx_reset_confirmed', array( $this, 'do_ajax_reset_confirmed' ) );
		add_action( 'wp_ajax_nopriv_lsx_reset_confirmed', array( $this, 'do_ajax_reset_confirmed' ) );

		
		add_filter( 'password_reset_expiration', array( $this, 'force_expiration_time' ) ,1 ,100 );
		
		add_action( 'rss_tag_pre', array($this,'remove_title_from_rss'),100);
		
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

	public function force_expiration_time($date){
		return strtotime(date('+7 days'));
	}
	
	
	/**
	 * Enqueue scripts and styles.
	 *
	 */
	function scripts() {
		if(!is_user_logged_in()){
			wp_enqueue_style('lsx_login_style', plugin_dir_url(__FILE__) . 'assets/css/lsx-login.css');

			$enqueue_scripts = apply_filters('lsx_login_js_enqueue',true);

			if(true === $enqueue_scripts){
				wp_enqueue_script('lsx_login_script', plugin_dir_url(__FILE__) . 'assets/js/lsx-login.js', array('jquery'), null, false);
				$param_array = array(
						'ajax_url' 			=> admin_url('admin-ajax.php'),
						'empty_username'	=> __('The username field is empty.','lsx-login'),
						'empty_password'	=> __('The password field is empty.','lsx-login'),
						'empty_reset'		=> __('Enter a username or e-mail address.','lsx-login'),
						'no_match'		=> __('Passwords do not match','lsx-login'),
						'ajax_spinner'		=> plugin_dir_url( __FILE__ ) . "assets/images/ajax-spinner.gif"
				);
				wp_localize_script( 'lsx_login_script', 'lsx_login_params', $param_array );
			}
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
		
		if(!is_user_logged_in()){
			remove_action( 'lsx_footer_before', 'lsx_add_footer_sidebar_area' );
		}
	}	
	
	
	/**
	 * Redirect the template
	 */	
	public function template_include($template) {
		if ( !is_user_logged_in() && !apply_filters('lsx_login_disable_template',false) ) {
			
			//Check if there is a tempalte in the theme
			$template = locate_template( array( 'template-login.php' ));
			
			//if there isnt, then display our template.
			if('' == locate_template( array( 'template-login.php' )) && file_exists( plugin_dir_path( __FILE__ ) . "templates/template-login.php" )) {
				$template = plugin_dir_path( __FILE__ ) ."templates/template-login.php";
			}
		}else{

			if(isset($this->options['login']['my_account_id']) && get_the_ID() === $this->options['login']['my_account_id']){
				//Check if there is a tempalte in the theme
				$template = locate_template( array( 'template-my-account.php' ));

				//if there isnt, then display our template.
				if('' == locate_template( array( 'template-my-account.php' )) && file_exists( plugin_dir_path( __FILE__ ) . "templates/template-my-account.php" )) {
					$template = plugin_dir_path(__FILE__) . "templates/template-my-account.php";
				}
			}
		}
		return $template;
	}
	
	//Add in a filter so the title is forced to a prompt.
	/**
	 * Redirect the template
	 */
	public function the_title_filter($title, $id) {
		
		global $wp_query;
		if(!is_user_logged_in() && is_main_query() && get_the_ID() == $id){
			$title = __('Please login to view this page!','lsx-login');
			$title = apply_filters('lsx_login_page_title',$title);
		}
		return $title;
	}
	
	/**
	 * Redirect the template
	 */
	public function remove_title_from_rss() {
		remove_filter( 'the_title', array($this,'the_title_filter' ));
	}	

		
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
	    return $logout_url.'&redirect_to=' . home_url();
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
				$user = $this->lsx_signon();
				
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

	/**
	 * Signs the user in
	 *
	 */
	public function lsx_signon($credentials = array(), $secure_cookie = '') {
		if ( empty($credentials) ) {
			if ( ! empty($_POST['log']) )
				$credentials['user_login'] = $_POST['log'];
			if ( ! empty($_POST['pwd']) )
				$credentials['user_password'] = $_POST['pwd'];
			if ( ! empty($_POST['rememberme']) )
				$credentials['remember'] = $_POST['rememberme'];
		}

		if ( !empty($credentials['remember']) )
			$credentials['remember'] = true;
		else
			$credentials['remember'] = false;

		/**
		 * Fires before the user is authenticated.
		 */
		do_action_ref_array( 'wp_authenticate', array( &$credentials['user_login'], &$credentials['user_password'] ) );

		if ( '' === $secure_cookie )
			$secure_cookie = is_ssl();

		/**
		 * Filter whether to use a secure sign-on cookie.
		 */
		$secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $credentials );

		global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie
		$auth_secure_cookie = $secure_cookie;

		add_filter('authenticate', 'wp_authenticate_cookie', 30, 3);

		$user = wp_authenticate($credentials['user_login'], $credentials['user_password']);

		if ( is_wp_error($user) ) {
			if ( $user->get_error_codes() == array('empty_username', 'empty_password') ) {
				$user = new WP_Error('', '');
			}

			return $user;
		}

		wp_set_auth_cookie($user->ID, $credentials['remember'], $secure_cookie);
		return $user;		
	}	
	
	/**
	 * Resets the Users Password
	 *
	 */
	public function do_ajax_reset() {
		global $wpdb;
	
		if(isset($_POST['method']) && 'reset' == $_POST['method']){
				
			$result = array();
			if(isset($_POST['log']) && (username_exists($_POST['log']) || email_exists($_POST['log']))){
				//Reset the password

				
				if ( strpos( $_POST['log'], '@' ) ) {
					$user_data = get_user_by( 'email', trim( $_POST['log'] ) );
				}else{
					$user_data = get_user_by( 'login', trim( $_POST['log'] ) );
				}
				
				if(false != $user_data){
					$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );
					
					//If the user is nto allowed to reset their password because they are spam users
					if ( ! $allow || is_wp_error( $allow ) ) {
						$result['success']  = 2;
						$result['message']  = __('Password reset is not allowed for this user','lsx-login');
					}else{
						
						
						$user_login = $user_data->user_login;
						$user_email = $user_data->user_email;
												
						// Generate something random for a password reset key.
						$key = wp_generate_password( 20, false );
						
						// Now insert the key, hashed, into the DB.
						if ( empty( $wp_hasher ) ) {
							require_once ABSPATH . WPINC . '/class-phpass.php';
							$wp_hasher = new PasswordHash( 8, true );
						}
						
						$hashed = $wp_hasher->HashPassword( $key );
						$expire = apply_filters( 'post_password_expires', time() + 10 * DAY_IN_SECONDS );
						
						$wpdb->update( $wpdb->users, array( 'user_activation_key' => $expire.':'.$hashed ), array( 'ID' => $user_data->ID ) );
						
						$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
						$message .= network_home_url( '/' ) . "\r\n\r\n";
						$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
						$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
						$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
						$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
						
						if ( is_multisite() ) {
							$blogname = $GLOBALS['current_site']->site_name;
						}else{
							$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
						}
						
						$title = sprintf( __('[%s] Password Reset'), $blogname );
						$title = apply_filters( 'retrieve_password_title', $title );
						$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
						$message = str_replace('wp-login.php','',$message);
						
						if ( $message && !mail( $user_email, wp_specialchars_decode( $title ), $message ) ){
							$result['success']  = 3;
							$result['message']  = __('The e-mail could not be sent.','lsx-login') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.','lsx-login');							
						}else{	
							$result['success']  = 1;
							$result['message']  = __('Check your e-mail for the confirmation link.','lsx-login');
							$result['email'] = $message;
						}
						
					}
				}
				
			}else{
				$result['success']  = 2;
				$result['message']  = __('Invalid username or e-mail.','lsx-login');
			}
				
			echo json_encode( $result );
		}else{
			echo false;
		}
		die();
	}

	
	public function is_password_confirmed(){
		$show_confirmation = false;
		if(isset($_GET['action']) && 'rp' == $_GET['action'] && isset($_GET['key']) && isset($_GET['login'])){
			
			//Check the details
			$user = check_password_reset_key( $_GET['key'], $_GET['login'] );
			/*print_r($_GET['key']);print_r('<br />');
			print_r($_GET['login']);print_r('<br />');
			print_r($user);print_r('<br />');*/
			if(!is_wp_error($user)){
				return $user;
			}else{
				return $user;
			}

		}
	}
	
	
	function do_ajax_reset_confirmed(){
		
		if(isset($_POST['key']) && isset($_POST['login']) && isset($_POST['pass1']) && isset($_POST['pass2']) ){	 	
			$result = array();
			
			$user = check_password_reset_key( $_POST['key'], $_POST['login'] );
			if(!is_wp_error($user)){
				$result['success']  = 1;
				$result['message']  = __('Password has been reset!','lsx-login');	
				$this->reset_password($user,$_POST['pass1']);
			}else{
				$result['success']  = 2;
				$result['message']  = __('An error has occured please contact the site administrator!','lsx-login');				
			}
			echo json_encode( $result );
			
		}else{
			echo false;
		}	
		
		die();		
	}
	
	
	/**
	 * Handles resetting the user's password.
	 *
	 * @param object $user The user
	 * @param string $new_pass New password for the user in plaintext
	 */
	function reset_password( $user, $new_pass ) {
		do_action( 'password_reset', $user, $new_pass );
	
		wp_set_password( $new_pass, $user->ID );
		update_user_option( $user->ID, 'default_password_nag', false, true );
	
		wp_password_change_notification( $user );
	}


	/**
	 * Returns the array of settings to the UIX Class
	 */
	public function create_settings_page() {
		if ( is_admin() ) {

			if ( ! class_exists( '\lsx\ui\uix' ) && ! class_exists( 'Tour_Operator' ) ) {
				include_once LSX_LOGIN_PATH . 'vendor/uix/uix.php';
				$pages = $this->settings_page_array();
				$uix = \lsx\ui\uix::get_instance( 'lsx' );
				$uix->register_pages( $pages );

			}
		}
	}

	/**
	 * Returns the array of settings to the UIX Class
	 */
	public function settings_page_array() {
		$tabs = apply_filters( 'lsx_framework_settings_tabs', array() );

		return array(
			'settings'  => array(
				'page_title'  =>  esc_html__( 'Theme Options', 'lsx-login' ),
				'menu_title'  =>  esc_html__( 'Theme Options', 'lsx-login' ),
				'capability'  =>  'manage_options',
				'icon'        =>  'dashicons-book-alt',
				'parent'      =>  'themes.php',
				'save_button' =>  esc_html__( 'Save Changes', 'lsx-login' ),
				'tabs'        =>  $tabs,
			),
		);
	}

	/**
	 * Register tabs
	 */
	public function register_tabs( $tabs ) {
		$default = true;

		if ( false !== $tabs && is_array( $tabs ) && count( $tabs ) > 0 ) {
			$default = false;
		}

		if ( ! array_key_exists( 'display', $tabs ) ) {
			$tabs['login'] = array(
				'page_title'        => '',
				'page_description'  => '',
				'menu_title'        => esc_html__( 'Login', 'lsx-login' ),
				'template'          => LSX_LOGIN_PATH . 'inc/settings/login.php',
				'default'           => $default
			);
			$default = false;
		}

		if ( ! array_key_exists( 'api', $tabs ) ) {
			$tabs['api'] = array(
				'page_title'        => '',
				'page_description'  => '',
				'menu_title'        => esc_html__( 'API', 'lsx-login' ),
				'template'          => LSX_LOGIN_PATH . 'inc/settings/api.php',
				'default'           => $default
			);

			$default = false;
		}

		return $tabs;
	}
	
}
global $lst_login;
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