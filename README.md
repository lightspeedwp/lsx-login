# LSX Login

The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.
 
## Changelog

### 1.0.3
* Dev - Moved main PHP class to a separate file
* Dev - Renamed 'inc' folder to 'includes'
* Dev - Added GulpJS to generate CSS and JS files
* Dev - Removed global variable $lst_login (started to use get_instance method instead)
* Dev - Allow email address login
* Dev - Allow user choose which pages are blocked or not
* Dev - Displayed a list of post types to "restrict"
* Dev - Added in a checkbox that enables you to "lock off" the entire site
* Dev - Added a custom field so user can decide to "restrict" a page or not
* Dev - Added no-index tag for logged in pages
* Dev - Made the primary menu display different automatically for logged users
* Dev - Shortcode to display useful links as dropdown menu related with the logged status

### 1.0.2
* Added in a My Account template with filters to extend.
* Added in a "Theme Options" page under the "Appearance" menu.
* Fix - Fixed all prefixes replaces (to_ > lsx_to_, TO_ > LSX_TO_)

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

Allow Plugins to add their own widgets to the dashboard.
```do_action('lsx_my_account_dashboard_widgets');```

Allow plugins to add in their own tabs
```
$tabs = array(
    'dashboard' => array(
        'label' => esc_html__('Dashboard','lsx-login'),
        'callback' => 'lsx_my_account_dashboard_tab',
    )
);
add_filter('lsx_my_account_tabs', $tabs);