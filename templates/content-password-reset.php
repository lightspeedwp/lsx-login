<?php
/**
 * The template part for displaying a password form
 *
 * @package lsx-login
 */
?>


<form method="post" action="<?php home_url('/wp-login.php?action=lostpassword'); ?>" id="lostpasswordform" name="lostpasswordform">

	<h3><span class="genericon genericon-refresh"></span><?php _e('Reset Password','lsx-login'); ?></h3>

	<p class="input-group input-group-lg">
		<label for="user_login"><?php _e('Username or E-mail:','lsx-login'); ?><br>
		<input type="text" size="20" value="" class="input form-control" id="user_login" name="user_login"></label>
	</p>
	
	<input type="hidden" value="" name="redirect_to">
	
	<p class="submit input-group input-group-lg">
		<input type="submit" value="<?php _e('Get New Password','lsx-login'); ?>" class="button button-primary button-large form-control" id="wp-submit" name="wp-submit">
	</p>
</form>