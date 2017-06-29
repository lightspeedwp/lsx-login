<?php
if ( ! class_exists( 'LSX_Login' ) ) {

	/**
	 * LSX Login Main Class
	 *
	 * @package   LSX Login
	 * @author    LightSpeed
	 * @license   GPL3
	 * @link      
	 * @copyright 2016 LightSpeed
	 */
	class LSX_Login {

		/**
		 * Plugin slug.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $plugin_slug = 'lsx-login';

		/**
		 * Plugin options.
		 *
		 * @var string
		 * @since 1.0.3
		 */
		public $options = null;
	
		/**
		 * Holds class instance.
		 *
		 * @var      object|Lsx_Login
		 */
		protected static $instance = null;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			require_once( LSX_LOGIN_PATH . 'includes/template-tags.php' );
			require_once( LSX_LOGIN_PATH . 'classes/class-lsx-login-widget.php' );

			if(class_exists('Tour_Operator')) {
				$this->options = get_option('_lsx-to_settings', false);
			}else{
				$this->options = get_option('_lsx_settings', false);
				if (false === $this->options) {
					$this->options = get_option('_lsx_lsx-settings', false);
				}
			}

			//Include the Settings Class
			add_action( 'init', array( $this, 'create_settings_page' ), 200 );
			add_action( 'init', array( $this, 'add_rewrite_rule' ) );

			add_filter( 'lsx_framework_settings_tabs', array( $this, 'register_tabs' ), 200, 1 );

			//Register our logged out menu location, make sure this is done at the very end so it doesnt mess with any currently set up menus. 
			add_action( 'after_setup_theme', array($this,'register_menus') , 100);
			
			//Call the logged out template
			add_action( 'template_include', array($this,'template_include'), 100 );
			
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

			//WooCommerce
			add_action( 'init', array( $this, 'woocommerce_add_rewrite_rule' ) );
			add_filter( 'query_vars', array( $this, 'woocommerce_add_query_vars' ), 0 );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'woocommerce_account_menu_items' ) );
			add_filter( 'woocommerce_get_myaccount_page_permalink', array( $this, 'woocommerce_get_myaccount_page_permalink' ) );
			
			//Allow email login
			add_filter( 'authenticate', array( $this, 'allow_email_login' ), 20, 3 );

			//User access
			add_filter( 'cmb_meta_boxes', array( $this, 'metaboxes' ) );

			//Primary menu display different automatically for logged users
			add_filter( 'wp_nav_menu_args', array( $this, 'modify_nav_menu_args' ) );

			//Shortcode to display useful links as dropdown menu related with the logged status
			add_shortcode( 'lsx_login_useful_items', array( $this, 'useful_links' ) );

			//Display the main modal (with forms to login and reset password)
			add_action( 'wp_footer', array( $this, 'main_modal' ) );

			//Redirect to My Account page after login
			add_action( 'lsx_login_redirect', array( $this, 'login_redirect' ) );
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
		public function scripts() {
			//if(!is_user_logged_in()){
				if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
					$min = '';
				} else {
					$min = '.min';
				}

				wp_enqueue_style( 'lsx_login', LSX_LOGIN_URL . 'assets/css/lsx-login.css', array(), LSX_LOGIN_VER );

				$enqueue_scripts = apply_filters('lsx_login_js_enqueue',true);

				if(true === $enqueue_scripts){
					wp_enqueue_script('lsx_login', LSX_LOGIN_URL . 'assets/js/lsx-login' . $min . '.js', array( 'jquery' ), LSX_LOGIN_VER, true );
					$param_array = array(
							'ajax_url' 			=> admin_url('admin-ajax.php'),
							'empty_username'	=> __('The username field is empty.','lsx-login'),
							'empty_password'	=> __('The password field is empty.','lsx-login'),
							'empty_reset'		=> __('Enter a username or e-mail address.','lsx-login'),
							'no_match'		    => __('Passwords do not match','lsx-login'),
							'ajax_spinner'		=> LSX_LOGIN_URL . "assets/images/ajax-spinner.gif",
							'theme_url' 		=> LSX_LOGIN_URL,
					);
					wp_localize_script( 'lsx_login', 'lsx_login_params', $param_array );
				}
			//}		
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
			$require_logged_user = apply_filters( 'lsx_login_require_logged_user', false );

			if ( false === $require_logged_user && isset( $this->options['login'] ) ) {
				if ( isset( $this->options['login']['lock_site'] ) && 1 === $this->options['login']['lock_site'] ) {
					$require_logged_user = true;
				}

				if ( false === $require_logged_user ) {
					$post_type = get_post_type();
					$key = 'lock_post_type_' . $post_type;

					if ( isset( $this->options['login'][$key] ) && 1 === $this->options['login'][$key] ) {
						$require_logged_user = true;
					}
				}
			}

			if ( false === $require_logged_user && is_singular() ) {
				$post_meta = get_post_meta( get_the_ID(), 'lock_single', true );

				if ( '1' === $post_meta ) {
					$require_logged_user = true;
				}
			}

			if ( true === $require_logged_user ) {
				add_action( 'wp_head', function() {
					echo "<meta name='robots' content='noindex,follow' />\n";
				} );
			}

			if ( true === $require_logged_user && !is_user_logged_in() && !apply_filters('lsx_login_disable_template',false) ) {
				
				//Check if there is a tempalte in the theme
				$template = locate_template( array( 'template-login.php' ));
				
				//if there isnt, then display our template.
				if('' == locate_template( array( 'template-login.php' )) && file_exists( LSX_LOGIN_PATH . "templates/template-login.php" )) {
					$template = LSX_LOGIN_PATH ."templates/template-login.php";
				}
			}else{

				if(isset($this->options['login']['my_account_id']) && get_the_ID() === $this->options['login']['my_account_id']){
					//Check if there is a tempalte in the theme
					$template = locate_template( array( 'template-my-account.php' ));

					//if there isnt, then display our template.
					if('' == locate_template( array( 'template-my-account.php' )) && file_exists( LSX_LOGIN_PATH . "templates/template-my-account.php" )) {
						$template = LSX_LOGIN_PATH . "templates/template-my-account.php";
					}
				}
			}
			return $template;
		}
		
		/**
		 * Add in a filter so the title is forced to a prompt.
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
		 * 
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
		    return $logout_url . '&redirect_to=' . esc_url( home_url() );
		}
		
		/**
		 * generate the login form
		 *
		 */
		public function login_form() {
			//if there isnt, then display our template.
			
			if('' == locate_template( array( 'content-login.php' )) && file_exists( LSX_LOGIN_PATH . "templates/content-login.php" )) {
				include LSX_LOGIN_PATH ."templates/content-login.php";
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
			
			if('' == locate_template( array( 'content-password-reset.php' )) && file_exists( LSX_LOGIN_PATH . "templates/content-password-reset.php" )) {
				include LSX_LOGIN_PATH ."templates/content-password-reset.php";
			}else{
				get_template_part( 'content', 'password-reset' );
			}	
		}
		
		/**
		 * Forces the Logged out page to be 1 Column
		 *
		 */
		public function logout_layout_filter($layout) {
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
				
				if(isset($_POST['log']) && (username_exists($_POST['log']) || is_email( $_POST['log'] ))){
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
		
		/**
		 * Resets the Users Password
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

		/**
		 * 
		 */
		public function is_password_confirmed() {
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
		
		/**
		 * 
		 */
		public function do_ajax_reset_confirmed() {
			if(isset($_POST['key']) && isset($_POST['login']) && isset($_POST['pass1']) && isset($_POST['pass2']) ){	 	
				$result = array();
				$user = check_password_reset_key( $_POST['key'], $_POST['login'] );

				if(!is_wp_error($user)){
					$result['success']  = 1;
					$result['message']  = __('Your password has been reset!','lsx-login');
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
		public function reset_password( $user, $new_pass ) {
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

			if ( ! array_key_exists( 'login', $tabs ) ) {
				$tabs['login'] = array(
					'page_title'        => '',
					'page_description'  => '',
					'menu_title'        => esc_html__( 'Login', 'lsx-login' ),
					'template'          => LSX_LOGIN_PATH . 'includes/settings/login.php',
					'default'           => $default
				);
				$default = false;
			}

			if ( ! array_key_exists( 'api', $tabs ) ) {
				$tabs['api'] = array(
					'page_title'        => '',
					'page_description'  => '',
					'menu_title'        => esc_html__( 'API', 'lsx-login' ),
					'template'          => LSX_LOGIN_PATH . 'includes/settings/api.php',
					'default'           => $default
				);

				$default = false;
			}

			return $tabs;
		}

		/**
		 * Add endpoints for my-account pages.
		 */
		public function add_rewrite_rule() {
			if ( class_exists( 'woocommerce' ) ) {
				return;
			}

			$account_slug = $this->get_my_account_page_slug();
			$endpoints = false;
			$endpoints = apply_filters('lsx_my_account_endpoints',$endpoints);
			add_rewrite_tag('%tab%', '([^&]+)');
			if(false !== $endpoints) {
				foreach ($endpoints as $endpoint) {
					add_rewrite_rule( $account_slug.'/'.$endpoint.'/?$', 'index.php?pagename='.$account_slug.'&tab='.$endpoint, 'top' );
				}
			}
		}

		/**
		 * Add endpoints for my-account pages.
		 */
		public function woocommerce_add_rewrite_rule() {
			if ( class_exists( 'woocommerce' ) ) {
				$endpoints = apply_filters( 'lsx_my_account_endpoints', array() );

				foreach ( $endpoints as $endpoint ) {
					add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
				}
			}
		}

		/**
		 * Add endpoints for my-account pages.
		 */
		public function woocommerce_add_query_vars( $vars ) {
			if ( class_exists( 'woocommerce' ) ) {
				$endpoints = apply_filters( 'lsx_my_account_endpoints', array() );

				foreach ( $endpoints as $endpoint ) {
					$vars[] = $endpoint;
				}
			}

			return $vars;
		}

		/**
		* Insert the new endpoint into the my-account menu.
		*/
		public function woocommerce_account_menu_items( $items ) {
			$tabs_from_lsx = apply_filters( 'lsx_my_account_tabs', array() );

			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );

			foreach ( $tabs_from_lsx as $key => $value ) {
				$items[$key] = $value['label'];
			}

			$items['customer-logout'] = $logout;

			foreach ( $tabs_from_lsx as $key => $value ) {
				if ( has_filter( 'woocommerce_account_' . $key . '_endpoint' ) ) {
					continue;
				}
				add_filter( 'woocommerce_account_' . $key . '_endpoint', array( $this, 'woocommerce_account_X_endpoint' ) );
			}

			return $items;
		}

		/**
		 * Callback to display the tab content
		 */
		public function woocommerce_account_X_endpoint() {
			global $wp;

			foreach ( $wp->query_vars as $key2 => $value2 ) {
				if ( 'pagename' === $key2 ) {
					continue;
				}

				$tabs_from_lsx = apply_filters( 'lsx_my_account_tabs', array() );

				if ( isset( $tabs_from_lsx[$key2] ) && isset( $tabs_from_lsx[$key2]['callback'] ) ) {
					$callback = $tabs_from_lsx[$key2]['callback'];

					if ( is_callable( $callback ) ) {
						call_user_func( $callback );
					}
				}
			}
		}

		/**
		 * Dashboard permalink.
		 * Without this function, WC can't find the correct Dashboard permalink.
		 */
		public function woocommerce_get_myaccount_page_permalink( $permalink ) {
			$account_slug = $this->get_my_account_page_slug();
			$permalink = site_url( '/' . $account_slug . '/' );
			return $permalink;
		}

		/**
		* Allow email login
		*/
		public function allow_email_login( $user, $username, $password ) {
			if ( is_email( $username ) ) {
				$user = get_user_by( 'email', $username );

				if ( $user ) {
					$username = $user->user_login;
				}
			}

			return wp_authenticate_username_password( null, $username, $password );
		}

		/**
		 * Define the metabox and field configurations.
		 *
		 * @param  array $meta_boxes
		 * @return array
		 */
		public function metaboxes( array $meta_boxes ) {
			$args = array(
				'public'   => true,
				//'_builtin' => false,
			);

			$post_types = get_post_types( $args, 'names' );

			if ( false !== ( $key = array_search( 'attachment', $post_types ) ) ) {
				unset( $post_types[$key] );
			}

			$fields = array(
				array( 'id' => 'lock_single',  'name' => esc_html__( 'Restrict access to this content', 'lsx-login' ), 'type' => 'checkbox' ),
			);

			$meta_boxes[] = array(
				'title'  => esc_html__( 'Login', 'lsx-login' ),
				'pages'  => $post_types,
				'fields' => $fields
			);

			return $meta_boxes;
		}

		/**
		 * Primary menu display different automatically for logged users
		 */
		public function modify_nav_menu_args( $args ) {
			if ( ! is_user_logged_in() ) {
				if ( 'primary' == $args['theme_location'] ) {
					$args['theme_location'] = 'primary_logged_out';
				}
			}

			return $args;
		}

		/**
		 * Shortcode to display useful links as dropdown menu related with the logged status
		 */
		public function useful_links( $atts ) {
			/*$atts = shortcode_atts( array(
				'foo' => 'no foo',
				'baz' => 'default baz',
			), $atts, 'useful_links' );*/

			$account_slug = $this->get_my_account_page_slug();

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();

				if ( ! empty( $current_user->user_firstname ) ) {
					$user_name = $current_user->user_firstname;
				} else {
					$user_name = $current_user->user_login;
				}

				$items = apply_filters( 'lsx_login_useful_items_logged_in', array(
					array(
						'label' => sprintf( esc_html__( 'Welcome, %s', 'lsx-login' ), $user_name ),
						'link'  => '#',
					),

					array(
						'label' => esc_html__( 'My Account', 'lsx-login' ),
						'link'  => site_url( '/' . $account_slug . '/' ),
					),

					array(
						'label' => 'Logout',
						'link'  => wp_logout_url( home_url( '/' ) ),
					),
				) );
			} else {
				$items = apply_filters( 'lsx_login_useful_items_logged_out', array(
					array(
						'label'                 => esc_html__( 'Login', 'lsx-login' ),
						//'link'                  => site_url( '/' . $account_slug . '/' ),
						'link'                  => '#',
						'extra_link_attributes' => 'href="#" data-toggle="modal" data-target="#login-modal"',
					),
				) );
			}

			$html = '';

			if ( count( $items ) > 0 ) {
				$dropdown = ( count( $items ) > 1 ) ? true : false;
				$html .= '<div class="lsx-login-useful-container ' . ( $dropdown ? 'dropdown' : '' ) . '">' . PHP_EOL;

				foreach ( $items as $key => $item ) {
					if ( true === $dropdown && 0 === $key ) {
						$html .= '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . $item['label'] . ' <span class="caret"></span></button>' . PHP_EOL;
						$html .= '<ul class="dropdown-menu">' . PHP_EOL;
					} elseif( false === $dropdown ) {
						$html .= '<li class="item-' . sanitize_title( $item['label'] ) . '"><a href="' . $item['link'] . '"' . ( isset( $item['extra_link_attributes'] ) ? $item['extra_link_attributes'] : '' ) . ' class="btn btn-primary">' . $item['label'] . '</a></li>' . PHP_EOL;
					} else {
						$html .= '<li class="item-' . sanitize_title( $item['label'] ) . '"><a href="' . $item['link'] . '"' . ( isset( $item['extra_link_attributes'] ) ? $item['extra_link_attributes'] : '' ) . '>' . $item['label'] . '</a></li>' . PHP_EOL;
					}
				}

				if ( true === $dropdown ) {
					$html .= '</ul>' . PHP_EOL;
				}

				$html .= '</div>' . PHP_EOL;
			}

			return $html;
		}

		/**
		 * Return My Account page slug
		 */
		public function get_my_account_page_slug() {
			$my_account_id = false;

			if ( isset( $this->options['login']['my_account_id'] ) ) {
				$my_account_id = $this->options['login']['my_account_id'];
			}

			if ( false !== $my_account_id ) {
				$my_account_page = get_post( $my_account_id );
				$account_slug = $my_account_page->post_name;
			} else {
				$account_slug = 'my-account';
			}

			return $account_slug;
		}

		/**
		 * Display the main modal (with forms to login and reset password)
		 */
		public function main_modal() {
			$tabs = apply_filters( 'lsx_login_modal_tabs', array(
				array(
					'label'    => esc_html__( 'Login', 'lsx-login' ),
					'callback' => 'lsx_login_form',
				),

				array(
					'label'    => esc_html__( 'Lost password', 'lsx-login' ),
					'callback' => 'lsx_password_reset_form',
				),
			) );
			?>
			<div id="login-modal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-body">
							<button class="close" type="button" data-dismiss="modal">×</button>

							<ul class="nav nav-tabs">
								<?php
									foreach ( $tabs as $key => $tab ) {
										echo '<li class="' . ( 0 === $key ? 'active' : '' ) . '"><a data-toggle="tab" href="#tab-' . sanitize_title( $tab['label'] ) . '">' . $tab['label'] . '</a></li>' . PHP_EOL;
									}
								?>
							</ul>

							<div class="tab-content">
								<?php
									foreach ( $tabs as $key => $tab ) {
										echo '<div id="tab-' . sanitize_title( $tab['label'] ) . '" class="tab-pane fade in ' . ( 0 === $key ? 'active' : '' ) . '">' . PHP_EOL;

										if ( isset( $tab['callback'] ) ) {
											$callback = $tab['callback'];

											if ( is_callable( $callback ) ) {
												call_user_func( $callback );
											}
										}

										echo '</div>' . PHP_EOL;
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Redirect to My Account page after login
		 */
		public function login_redirect( $url ) {
			$account_slug = $this->get_my_account_page_slug();
			$url = site_url( '/' . $account_slug . '/' );
			return $url;
		}

	}

	new LSX_Login;

}
