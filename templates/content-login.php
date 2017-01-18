<?php
/**
 * The template part for displaying a login form
 *
 * @package lsx-login
 */
?>

<form method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" class="loginform" name="loginform">

	<h3><span class="genericon genericon-home"></span> <?php _e('Login','lsx-login'); ?></h3>
	<p><?php _e('Have an account? Log in.','lsx-login'); ?></p>

	<p class="login-username input-group input-group-lg">
		<label for="user_login"><?php _e('Username or email address','lsx-login'); ?></label>
		<input type="text" size="20" value="" class="input form-control user_login" name="log">
	</p>
	
	<p class="login-password input-group input-group-lg">
		<label for="user_pass"><?php _e('Password','lsx-login'); ?></label>
		<input type="password" size="20" value="" class="input form-control user_pass" name="pwd">
	</p>
	
	<p class="login-remember input-group input-group-lg">
		<input class="form-control rememberme" type="checkbox" value="forever" name="rememberme">
		<label for="rememberme"><?php _e('Remember Me','lsx-login'); ?></label>
	</p>
	
	<p class="login-submit input-group input-group-lg">
		<input type="submit" value="<?php _e('Log In','lsx-login'); ?>" class="btn btn-default" class="wp-submit" name="wp-submit">
		<input type="hidden" value="<?php echo apply_filters('lsx_login_redirect',home_url()); ?>" name="redirect_to">
		<input type="hidden" value="1" name="testcookie">
	</p>
</form>