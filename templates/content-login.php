<?php
/**
 * The template part for displaying a login form
 *
 * @package lsx-login
 */
?>
<h3><span class="genericon genericon-home"></span><?php _e('Login','lsx-login'); ?></h3>
<form method="post" action="<?php home_url('/wp-login.php'); ?>" class="loginform" name="loginform">		
	<p class="login-username input-group input-group-lg">
		<label for="user_login"><?php _e('Username','lsx-login'); ?></label>
		<input type="text" size="20" value="" class="input form-control" id="user_login" name="log">
	</p>
	
	<p class="login-password input-group input-group-lg">
		<label for="user_pass"><?php _e('Password','lsx-login'); ?></label>
		<input type="password" size="20" value="" class="input form-control" id="user_pass" name="pwd">
	</p>
	
	<p class="login-remember input-group input-group-lg">
		<label><input class="form-control" type="checkbox" value="forever" id="rememberme" name="rememberme"> <?php _e('Remember Me','lsx-login'); ?></label>
	</p>
	
	<p class="login-submit input-group input-group-lg">
		<input type="submit" value="<?php _e('Log In','lsx-login'); ?>" class="button-primary form-control" id="wp-submit" name="wp-submit">
		<input type="hidden" value="/" name="redirect_to">
	</p>
</form>