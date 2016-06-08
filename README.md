# LSX Login
* Contributors: <a href="https://github.com/krugazul">Warwick</a>
* Author: LightSpeed
* Author URI: https://www.lsdev.biz/
* Plugin Name: LSX
* Plugin URI: https://github.com/lightspeeddevelopment/lsx-login/
* Requires at least: 4.1
* Tested up to: 4.3
* Stable tag: 4.3
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description

Activate the plugin to display a login form and reser your password form, in a 2 column display. Users will need to log in to view your site. The forms are based directly on the WordPress forms.
 
* Ajax form submission
* Login Widget with logged in and out view.

## Installation

* Upload the plugin to the /wp-content/plugins/  folder.
* Activate via the WP Dashboard

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

## Changlog

### 1.0
* First Version