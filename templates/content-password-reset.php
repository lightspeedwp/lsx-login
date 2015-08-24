<?php
/**
 * The template part for displaying a password form
 *
 * @package lsx-login
 */
?>


<form method="post" action="<?php home_url('/wp-login.php?action=lostpassword'); ?>" class="lostpasswordform" name="lostpasswordform">

	<h3><span class="genericon genericon-refresh"></span><?php _e('Reset Password','lsx-login'); ?></h3>

	<p class="login-username input-group input-group-lg">
		<label for="user_login"><?php _e('Username or E-mail:','lsx-login'); ?></label>
		<input type="text" size="20" value="" class="input form-control user_login" name="user_login">
	</p>
	
	<input type="hidden" value="/" name="redirect_to">
	
	<p class="submit input-group input-group-lg">
		<input type="submit" value="<?php _e('Get New Password','lsx-login'); ?>" class="btn btn-default" class="wp-submit" name="wp-submit">
	</p>

</form>

