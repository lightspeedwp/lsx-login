
jQuery(document).ready(function($) {
	jQuery('.loginform').submit(function(event){
		
		event.preventDefault();
		
		//remove all error and validation fields
		jQuery(this).find('.error').each(function(event){
			jQuery(this).remove();
		});
		
		
		var username = jQuery(this).find('input#user_login').val();
		if('' == username){
			jQuery(this).find('input#user_login').parent('p').append('<span class="error">'+lsx_login_params.empty_username+'</span>');
			return false;
		}
		
		
		var password = jQuery(this).find('input#user_pass').val();
		if('' == password){
			jQuery(this).find('input#user_pass').parent('p').append('<span class="error">'+lsx_login_params.empty_password+'</span>');
			return false;
		}		
			
		var remember = '';
	    if ( jQuery(this).find('input[name="rememberme"]:checked' ).size() > 0 ) {
	    	remember = jQuery(this).find('input[name="rememberme"]:checked').val();
	    }
	    
	    var params = {
			action: 		'lsx_login',
			username:	 	username,
			password:	 	password,
			remember: 		remember,
			method:			'login'
		};	    
		
		jQuery.ajax({
			url: lsx_login_params.ajax_url,
			data: params,
			type: 'POST',
			success: function( response ) {
				alert(response);
			}

		});		
		
		return false;
	});
	
	jQuery('input#user_login').click(function(event){
		jQuery(this).parent('p').find('.error').remove();
	});
	
});