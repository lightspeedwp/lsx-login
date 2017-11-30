
jQuery(document).ready(function($) {

	jQuery(document).on('click', 'button[name="welcome_email_invite"]', function(e) {
		var $button = jQuery(this);

		$button.html(lsx_login_admin_params.text_sending);
		$button.attr('disabled', 'disabled');

		var roles = jQuery('input[name="welcome_email_user_roles[]"]:checked'),
			rolesSerialized = roles.serialize(),
			rolesSerializedArray = roles.serializeArray();

		jQuery.ajax({
			url: lsx_login_admin_params.ajax_welcome_users,
			dataType: 'json',
			data: rolesSerialized,
		})
		.done(function() {
			$button.html(lsx_login_admin_params.text_sent);
			$button.removeClass('button-primary');
			$button.addClass('button-secondary');
		})
		.fail(function() {
			$button.html(lsx_login_admin_params.text_send);
			$button.removeAttr('disabled');
		});
	});

});
