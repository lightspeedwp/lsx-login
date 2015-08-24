<?php
/**
 * Template Tags
 *
 * @package Lsx_Restrict_Access
 * @author  Warwick
 */

/**
 * Outputs the login form for the login page
 */
function lsx_login_form(){
	global $lst_login;
	
	$lst_login->login_form();
}


/**
 * Outputs the reset your password form for the login page
 */
function lsx_password_reset_form(){
	global $lst_login;
	
	$lst_login->password_reset_form();
}


/**
 * checks if the password reset confirmation form should be shown
 */
function lsx_is_password_confirmation(){
	global $lst_login;
	
	return $lst_login->is_password_confirmation();
}