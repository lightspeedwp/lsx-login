# LSX Login

The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.
 
## Changelog

### 1.2 - 
* 

### 1.0.1 - 08/12/16
* Fix - Reduced the access to server (check API key status) using transients
* Fix - Made the API URLs dev/live dynamic using a prefix "dev-" in the API KEY

### 1.0.0 - 30/11/16
* First Version

## Filters

Add aditional links to the login widget, the links are sent via an indexed array $links;
```apply_filters('lsx_logged_in_links',$links);```

Disable the frontend login template, if you just want to use the login widget.
```add_filter('lsx_login_disable_template', function( $bool ) { return true; });```

Or disble the tempalte for specific pages,  below is an example you would use to disable the tempalte on the home page.
```
function disable_homepage_login_template($disable=false){
	if(is_home() || is_front_page()){
		$disable = true;
	}
	return $disable;
}
add_filter('lsx_login_disable_template','disable_homepage_login_template');
```
