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
    	
    	$current_user = get_currentuserinfo();
    	
    	$title = __('Welcome','lsx-login');
    	
    	if(class_exists('BuddyPress')){
    		
    		
    		
    	}else{
    		if($current_user->first_name){
    			
    		}
    	}
    	
    	return $title;
    } 

    /**
     * Gets the correct links
     *
     */
    public function get_links( ) {
    	 
    	$current_user = get_currentuserinfo();
    	 
    	if(class_exists('BuddyPress')){
    
    
    
    	}else{
    		if($current_user->first_name){
    			 
    		}
    	}
    	 
    	return $title;
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
			
			echo $before_title . $instance['title'] . $after_title;

			/*
			<ul class="pagenav sidebar_login_links">
			<li class="dashboard-link"><a href="http://lh-gtsblog.feedmybeta.com/wp-admin">Dashboard</a></li>
			<li class="profile-link"><a href="http://lh-gtsblog.feedmybeta.com/wp-admin/profile.php">Profile</a></li>
			<li class="logout-link"><a href="http://lh-gtsblog.feedmybeta.com/wp-login.php?action=logout&amp;redirect_to=%2F&amp;_wpnonce=2414b2d2d9&amp;redirect_to=http://lh-gtsblog.feedmybeta.com">Logout</a></li>
			</ul>
			*/

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