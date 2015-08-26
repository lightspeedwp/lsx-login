<?php
/**
 * Login Widget
 *
 * @package Lsx_Login
 * @author  Warwick
 */
class LSX_Login_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'description' => __( 'Displays the lsx login page.', 'lsx-login' ) );

		/* Create the widget. */
		parent::__construct( 'lsx_login_widget', __( 'LSX Login', 'lsx-login' ), $widget_ops );
    }
    
    
    /**
     * Replace the Username in the title
     *
     */
    public function get_title( ) {
    	global $current_user;
    	$title = __('Welcome','lsx-login');
    	
    		if('' !== $current_user->data->display_name){
    			$name = $current_user->data->display_name;
    		}else{
    			$name = $current_user->data->user_login;
    		}
    		$title .= ' '.$name;
    	
    	return $title;
    } 

    /**
     * Gets the correct links
     *
     */
    public function get_links( ) {
    	 

    	echo '<ul class="lsx-login-links">';
    	$links = array();
    	if(class_exists('BuddyPress')){
    		$links[] = '<li class="profile"><a href="'.bp_loggedin_user_domain().'">'.__('Profile','lsx-login').'</a></li>';
    		$links[] = '<li class="password"><a href="'.bp_loggedin_user_domain().'settings/">'.__('Change Password','lsx-login').'</a></li>';
    	}else{
			$links[] = '<li class="dashboard"><a href="'.admin_url().'">'.__('Dashboard','lsx-login').'</a></li>';
			$links[] = '<li class="profile"><a href="'.admin_url('/profile.php').'">'.__('Profile','lsx-login').'</a></li>';
    	}
    	$links[] = '<li class="logout"><a href="'.wp_logout_url().'">'.__('Logout','lsx-login').'</a></li>';
    	echo implode('',$links);
    	
    	echo '</ul>';
    }    

    /**
     * widget function.
     *
     * @param mixed $args
     * @param mixed $instance
     */
    public function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		// Logged in user
		if ( is_user_logged_in() ) {

			$title = $this->get_title();
			
			echo $before_title . $title . $after_title;

			$this->get_links();

		// Logged out user
		} else {

			if ( isset($instance['title'])) {
				echo $before_title . $instance['title'] . $after_title;
			}			
			
			lsx_login_form();
		}

		echo $after_widget;
    }

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance[ 'title' ] = strip_tags( stripslashes( $new_instance[ 'title' ] ) );
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	function form( $instance ) {

		if ( ! isset( $instance[ 'title' ] ) ) {
			$instance[ 'title' ] = 'Login';
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e('Title','lsx-login'); ?>:</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" placeholder="<?php _e('Login','lsx-login'); ?>" value="<?php echo esc_attr( $instance[ 'title' ] ); ?>" />
		</p>
		
		<?php
	}
}

function lsx_login_widget_init(){
	register_widget( 'LSX_Login_Widget' );
}
add_action('widgets_init','lsx_login_widget_init');