<?php
/**
 * Template Tags
 *
 * @package   LSX Login
 * @author    LightSpeed
 * @license   GPL3
 * @link      
 * @copyright 2016 LightSpeed
 */

/**
 * Outputs the login form for the login page
 *
 * @return		string
 *
 * @package 	lsx-login
 * @subpackage	template-tags
 * @category 	login
 */
function lsx_login_form(){
	$lsx_login = LSX_Login::get_instance();
	$lsx_login->login_form();
}


/**
 * Outputs the reset your password form for the login page
 *
 * @return		string
 *
 * @package 	lsx-login
 * @subpackage	template-tags
 * @category 	reset-password
 * */
function lsx_password_reset_form(){
	$lsx_login = LSX_Login::get_instance();
	$lsx_login->password_reset_form();
}


/**
 * checks if the password reset confirmation form should be shown
 *
 * @return		string
 *
 * @package 	lsx-login
 * @subpackage	template-tags
 * @category 	conditional
 */
function lsx_is_password_confirmed(){
	$lsx_login = LSX_Login::get_instance();
	return $lsx_login->is_password_confirmed();
}

/**
 * Outputs the My Account Tabs
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	lsx-login
 * @subpackage	template-tags
 * @category 	my-account
 */
function lsx_my_account_tabs($before="",$after="",$echo=true){
	$tabs = array(
		'dashboard' => array(
			'label' => esc_html__('Dashboard','lsx-login'),
			'callback' => 'lsx_my_account_dashboard_tab',
		)
	);
	$tabs = apply_filters('lsx_my_account_tabs',$tabs);
	?>
	<div class="row my-account">
		<div class="col-sm-12 tab-heading">
			<?php foreach($tabs as $tab_key => $tab){ ?>
				<?php
					$link = get_permalink();
					if('dashboard' !== $tab_key) {
						$link .= $tab_key.'/';
					}
				?>
				<div class="col-sm-2"><a href="<?php echo esc_url($link); ?>" title="<?php echo esc_attr($tab['label']); ?>"><?php echo esc_attr($tab['label']); ?></a></div>
			<?php }	?>
		</div>

		<?php
		$query_tab = get_query_var('tab');
		if(false === $query_tab || '' === $query_tab) {
			$query_tab = 'dashboard';
		}

		foreach($tabs as $tab_key => $tab){
			if($tab_key !== $query_tab){ continue; }
			?>
			<div class="col-sm-12 tab-content tab-<?php echo $tab_key; ?>">
				<?php call_user_func($tab['callback']); ?>
			</div>
		<?php }	?>
	</div>
	<?php
}


/**
 * Outputs the content for the My Account tab
 *
 * @package 	lsx-login
 * @subpackage	template-tags
 * @category 	conditional
 *
 * @return void
 */
function lsx_my_account_dashboard_tab(){
	global $post;

	$lsx_login = LSX_Login::get_instance();

	echo '<div class="my-account-welcome">';
	echo wp_kses_post(apply_filters('the_content',$post->post_content));
	echo '</div>';

	do_action('lsx_my_account_dashboard_widgets');
}

/**
 * Returns the Public Content shown on the restricted template.
 * @return bool|mixed
 */
function lsx_restricted_page_content(){
	$restricted_content = get_post_meta( get_the_ID(), 'public_content', true );
	if ( '' === $restricted_content ) {
		$restricted_content = false;
	}
	return $restricted_content;
}