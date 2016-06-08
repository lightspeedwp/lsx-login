
jQuery(document).ready(function($) {
	jQuery('.loginform').submit(function(event){
		
		event.preventDefault();
		var formObj = $(this);
		
		//remove all error and validation fields
		$(this).find('.error').each(function(event){
			$(this).remove();
		});
		
		
		var username = $(this).find('input.user_login').val();
		if('' == username){
			$(this).find('input.user_login').parent('p').append('<div class="error">'+lsx_login_params.empty_username+'</div>');
			return false;
		}
		
		var password = $(this).find('input.user_pass').val();
		if('' == password){
			$(this).find('input.user_pass').parent('p').append('<div class="error">'+lsx_login_params.empty_password+'</div>');
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
					
					if(result.success == '1'){ window.location.href = redirect;  }
					
					if(result.success == '3'){
						formObj.find('.login-password').append('<div class="error">'+result.message+'</div>');
					}else if(result.success == '2'){
						formObj.find('.login-username').append('<div class="error">'+result.message+'</div>');
					}
				}
			}

		});		
		
		return false;
	});
	
	
	jQuery('form[name="lostpasswordform"]').submit(function(event){
		
		event.preventDefault();
		var formObj = $(this);
		
		//remove all error and validation fields
		$(this).find('.error').each(function(event){
			$(this).remove();
		});
		
		
		var username = $(this).find('input.user_login').val();
		if('' == username){
			$(this).find('input.user_login').parent('p').append('<div class="error">'+lsx_login_params.empty_reset+'</div>');
			return false;
		}

	    var params = {
			action: 		'lsx_reset',
			log:		 	username,
			method:			'reset'
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
					$('.lostpasswordform.loading').removeClass('loading');
					
					if(result.success == '3'){
						formObj.append('<p class="error">'+result.message+'</p>');
					}else if(result.success == '2'){
						formObj.find('.login-username').append('<div class="error">'+result.message+'</div>');
					}else if(result.success == '1'){
						formObj.find('p').each(function(event){
							$(this).remove();
						}); 
						formObj.append('<p class="success">'+result.message+'</p>');
					}
				}
			}

		});		
		
		return false;
	});
	
	
	jQuery('form[name="resetpassform"]').submit(function(event){
		event.preventDefault();
		var formObj = $(this);
		
		//remove all error and validation fields
		$(this).find('.error').each(function(event){
			$(this).remove();
		});
		
		
		var pass1 = $(this).find('input.pass1').val();
		if('' == pass1){
			$(this).find('input.pass1').parent('p').append('<div class="error">'+lsx_login_params.empty_password+'</div>');
			return false;
		}	
		
		var pass2 = $(this).find('input.pass2').val();
		if('' == pass2){
			$(this).find('input.pass2').parent('p').append('<div class="error">'+lsx_login_params.empty_password+'</div>');
			return false;
		}
		
		if(pass1 !== pass2 ){
			$(this).find('input.pass2').parent('p').append('<div class="error">'+lsx_login_params.no_match+'</div>');
			return false;
		}
		
		var key = $(this).find('input[name="rp_key"]').val();
		var login = $(this).find('input[name="user_login"]').val();		
		
	    var params = {
				action: 		'lsx_reset_confirmed',
				pass1:		 	pass1,
				pass2:		 	pass2,
				key:		 	key,
				login:		 	login
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
					$('.lostpasswordform.loading').removeClass('loading');
					
					if(result.success == '2'){
						formObj.append('<p class="error">'+result.message+'</p>');
					}else if(result.success == '1'){
						formObj.find('p').each(function(event){
							$(this).remove();
						}); 
						formObj.append('<p class="success">'+result.message+'</p>');
					}
				}
			}

		});	
		
	});
	
	$('input.user_login , input.user_pass, input.pass1, input.pass2').click(function(event){
		$(this).parent('p').find('.error').remove();
	});
	
});