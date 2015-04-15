jQuery(document).ready( function($) {
	
	// login with facebook
	$( document ).on( 'click', 'a.edd-slg-social-login-facebook', function(){
		
		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.fberror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.fberrormsg );
			return false;
		} else {
			
			FB.login(function(response) {
				//alert(response.status);
			  if (response.status === 'connected') {
			  	//creat user to site
			  	edd_slg_social_connect( 'facebook', object );
			  }
			}, {scope:'publish_stream,email'});	
		}
	});
	
	// login with google+
	$( document ).on( 'click', 'a.edd-slg-social-login-googleplus', function(){
		
		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.gperror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.gperrormsg );
			return false;
		} else {
			
			var googleurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-gp-redirect-url').val();
			
			if(googleurl == '') {
				alert( EDDSlg.urlerror );
				return false;
			}
				
			var googleLogin = window.open(googleurl, "google_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var gTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (googleLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(gTimer);
						googleLogin.close();
						edd_slg_social_connect( 'googleplus', object );
					}
				} catch (e) {}
			}, 500);
		}
	});
	
	// login with linkedin
	$( document ).on( 'click', 'a.edd-slg-social-login-linkedin', function(){
	
		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.lierror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.lierrormsg );
			return false;
		} else {
		
			var linkedinurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-li-redirect-url').val();
			
			if(linkedinurl == '') {
				alert( EDDSlg.urlerror );
				return false;
			}
			var linkedinLogin = window.open(linkedinurl, "linkedin", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var lTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (linkedinLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(lTimer);
						linkedinLogin.close();
						edd_slg_social_connect( 'linkedin', object );
					}
				} catch (e) {}
			}, 300);
		}
		
	});
	
	// login with twitter
	$( document ).on( 'click', 'a.edd-slg-social-login-twitter', function(){

		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		//var redirect_url = $(this).parents('.edd-slg-social-container').find('.edd-slg-redirect-url').val();
		var parents = $(this).parents( 'div.edd-slg-social-container' );
		var appendurl = '';
		
		//check button is clicked form widget
		if( parents.hasClass('edd-slg-widget-content') ) {
			appendurl = '&container=widget';
		} 
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.twerror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.twerrormsg );
			return false;
		} else {
		
			var twitterurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-tw-redirect-url').val();
			
			if( twitterurl == '' ) {
				alert( EDDSlg.urlerror );
				return false;
			}
				
			var twLogin = window.open(twitterurl, "twitter_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var tTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if ( twLogin.location.hostname == window.location.hostname ) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(tTimer);
						twLogin.close();
						window.parent.location = EDDSlg.socialloginredirect+appendurl;
					}
				} catch (e) {}
			}, 300);
		}
		
	});
	
	// login with yahoo
	$( document ).on( 'click', 'a.edd-slg-social-login-yahoo', function(){

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.yherror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.yherrormsg );
			return false;
		} else {
		
			var yahoourl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-yh-redirect-url').val();
			
			if(yahoourl == '') {
				alert( EDDSlg.urlerror );
				return false;
			}
			var yhLogin = window.open(yahoourl, "yahoo_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var yTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (yhLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(yTimer);
						yhLogin.close();
						edd_slg_social_connect( 'yahoo', object );
					}
				} catch (e) {}
			}, 300);
		}
	});
	
	// login with foursquare
	$( document ).on( 'click', 'a.edd-slg-social-login-foursquare', function(){
	
		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.fserror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.fserrormsg );
			return false;
		} else {
		
			var foursquareurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-fs-redirect-url').val();
			
			if(foursquareurl == '') {
				alert( EDDSlg.urlerror );
				return false;
			}
			var fsLogin = window.open(foursquareurl, "foursquare_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var fsTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (fsLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(fsTimer);
						fsLogin.close();
						edd_slg_social_connect( 'foursquare', object );
					}
				} catch (e) {}
			}, 300);
		}
	});
	
	// login with windows live
	$( document ).on( 'click', 'a.edd-slg-social-login-windowslive', function(){
	
		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');
		
		if( EDDSlg.wlerror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.wlerrormsg );
			return false;
		} else {
		
			var windowsliveurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-wl-redirect-url').val();
			
			if(windowsliveurl == '') {
				alert( EDDSlg.urlerror );
				return false;
			}
			var wlLogin = window.open(windowsliveurl, "windowslive_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var wlTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (wlLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(wlTimer);
						wlLogin.close();
						edd_slg_social_connect( 'windowslive', object );
					}
				} catch (e) {}
			}, 300);
		}
	});
	
	// login with VK.com
	$( document ).on( 'click', 'a.edd-slg-social-login-vk', function(){	
				
		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		
		errorel.hide();
		errorel.html('');		
		
		if( EDDSlg.vkerror == '1' ) {
			errorel.show();
			errorel.html( EDDSlg.vkerrormsg );
			return false;
		} else {
		
			var vkurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-vk-redirect-url').val();			
					
			if(vkurl == '') {
				alert( EDDSlg.urlerror );
				return false;
			}
								
			var vkLogin = window.open(vkurl, "vk_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");			
			var vkTimer = setInterval(function () { //set interval for executing the code to popup
				try {					
					if (vkLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(vkTimer);
						vkLogin.close();						  
						edd_slg_social_connect( 'vk', object ); 
					}
				} catch (e) {}
			}, 300);
		}
	});
	
});

// Social Connect Process
function edd_slg_social_connect( type, object ) {
	
	var data = { 
					action	:	'edd_slg_social_login',
					type	:	type
				};
			
	//show loader
	jQuery('.edd-slg-login-loader').show();
	jQuery('.edd-slg-social-wrap').hide();
	
	jQuery.post( EDDSlg.ajaxurl,data,function(response){
		
		// hide loader
		jQuery('.edd-slg-login-loader').hide();
		jQuery('.edd-slg-social-wrap').show();
		
		var redirect_url = object.parents('.edd-slg-social-container').find('.edd-slg-redirect-url').val();		
		
		if( response != '' ) {
			
			var result = jQuery.parseJSON( response );
						
			//alert( redirect_url );  
			
			if( redirect_url != '' ) {
				
				window.location = redirect_url;
				
			} else {
				
				//if user created successfully then reload the page
				window.location.reload();
			}
		}
	});
}