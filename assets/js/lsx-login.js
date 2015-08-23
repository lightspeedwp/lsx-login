
jQuery(document).ready(function($) {
	jQuery('.loginform').submit(function(event){
		
		event.preventDefault();
		var formObj = $(this);
		
		//remove all error and validation fields
		$(this).find('.error').each(function(event){
			$(this).remove();
		});
		
		
		var username = $(this).find('input#user_login').val();
		if('' == username){
			$(this).find('input#user_login').parent('p').append('<span class="error">'+lsx_login_params.empty_username+'</span>');
			return false;
		}
		
		var password = $(this).find('input#user_pass').val();
		if('' == password){
			$(this).find('input#user_pass').parent('p').append('<span class="error">'+lsx_login_params.empty_password+'</span>');
			return false;
		}		
			
		var remember = '';
	    if ( $(this).find('input[name="rememberme"]:checked' ).size() > 0 ) {
	    	remember = $(this).find('input[name="rememberme"]:checked').val();
	    }
	    
	    var redirect = $(this).find('input[name="redirect_to"]' ).val();
	    var params = {
			action: 		'lsx_login',
			log:		 	username,
			pwd:		 	password,
			rememberme: 	remember,
			method:			'login'
		};	 
	    
	    
	    //Gery out the form field
	    $(this).addClass('loading');
	    
	    //Load the spinner
		$(this).prepend('<img style="display:none;" src="'+lsx_login_params.ajax_spinner+'" class="spinner" />');
		var adjustHeight = $(this).height()/2 - 64;
		var adjustWidth = $(this).height()/2 - 64;
		$(this).find('.spinner').css('margin-top',adjustHeight+'px');
		$(this).find('.spinner').css('margin-left',adjustWidth+'px');
		$(this).find('.spinner').show();
		
		$.ajax({
			url: lsx_login_params.ajax_url,
			data: params,
			type: 'POST',
			success: function( response ) {
				
				if(false != response){
					var result = $.parseJSON( response );
					$('.spinner').remove();
					$('.loginform.loading').removeClass('loading');
					
					if(result.success == '3'){
						formObj.find('.login-password').append('<span class="error">'+result.message+'</span>');
					}else if(result.success == '2'){
						formObj.find('.login-username').append('<span class="error">'+result.message+'</span>');
					}else{
						window.location.href = redirect; 
					}
				}
			}

		});		
		
		return false;
	});
	
	$('input#user_login , input#user_pass').click(function(event){
		$(this).parent('p').find('.error').remove();
	});
	
});