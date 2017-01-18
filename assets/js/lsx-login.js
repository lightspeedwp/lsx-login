
jQuery(document).ready(function($) {

	var isMobile = false; //initiate as false
	// device detection
	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

	if(false == isMobile){
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
	}else{
		jQuery('.loginform').attr('action','/wp-login.php');
	}

	if (('undefined' === typeof jQuery().dropdown)) {
		jQuery.ajax({
			url: lsx_login_params.theme_url + 'assets/js/bootstrap.dropdown.js',
			dataType: "script",
			cache: true
		});
	}

	if (('undefined' === typeof jQuery().modal)) {
		jQuery.ajax({
			url: lsx_login_params.theme_url + 'assets/js/bootstrap.modal.js',
			dataType: "script",
			cache: true
		});
	}

	if (('undefined' === typeof jQuery().tab)) {
		jQuery.ajax({
			url: lsx_login_params.theme_url + 'assets/js/bootstrap.tab.js',
			dataType: "script",
			cache: true
		});
	}

});