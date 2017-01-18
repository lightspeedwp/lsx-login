<?php
/**
 * The template part for displaying a password form
 *
 * @package lsx-login
 */

//http://lh-gtsblog.feedmybeta.com/wp-login.php?action=rp&key=tSteZL7WPlWg9I7HdJSH&login=warwick

$is_key_confirmed = lsx_is_password_confirmed();

if(isset($_GET['action']) && 'rp' === $_GET['action'] && !is_wp_error($is_key_confirmed)){ ?>
	<form autocomplete="off" method="post" action="<?php home_url('/'); ?>" class="lostpasswordform" name="resetpassform">
		<input type="hidden" autocomplete="off" value="<?php echo $_GET['login'];?>" class="user_login" name="user_login">
		<input type="hidden" value="<?php echo $_GET['key'];?>" name="rp_key">
		
		<h3><span class="genericon genericon-refresh"></span> <?php _e('New Password','lsx-login'); ?></h3>
	
		<p class="input-group input-group-lg">
			<label for="pass1"><?php _e('New password','lsx-login'); ?></label>
			<input type="password" autocomplete="off" value="" size="20" class="input form-control pass1" name="pass1">
		</p>
		<p class="input-group input-group-lg">
			<label for="pass2"><?php _e('Confirm new password','lsx-login'); ?></label>
			<input type="password" autocomplete="off" value="" size="20" class="input form-control pass2" name="pass2">
		</p>
		
		<p class="description indicator-hint">
			<?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).','lsx-login'); ?>
		</p>
		<br class="clear">
	
		
		<p class="submit input-group input-group-lg">
			<input type="submit" value="<?php _e('Reset Password','lsx-login'); ?>" class="btn btn-default" class="wp-submit" name="wp-submit">
		</p>
	</form>	
<?php 	
}else{
?>

	<form method="post" action="<?php home_url('/'); ?>" class="lostpasswordform" name="lostpasswordform">
	
		<h3><span class="genericon genericon-refresh"></span> <?php _e('Reset Password','lsx-login'); ?></h3>
		<p><?php _e('Enter your username or email to reset your password.','lsx-login'); ?></p>
		
		<?php
		if(is_wp_error($is_key_confirmed)){
			?><p class="error"><?php _e('This key has expired, please try reset your password again.','lsx-login'); ?></p><?php 
		}
		?>
	
		<p class="login-username input-group input-group-lg">
			<label for="user_login"><?php _e('Username or email address','lsx-login'); ?></label>
			<input type="text" size="20" value="" class="input form-control user_login" name="user_login">
		</p>
		
		<input type="hidden" value="/" name="redirect_to">
		
		<p class="submit input-group input-group-lg">
			<input type="submit" value="<?php _e('Get New Password','lsx-login'); ?>" class="btn btn-default" class="wp-submit" name="wp-submit">
		</p>
	
	</form>
<?php }