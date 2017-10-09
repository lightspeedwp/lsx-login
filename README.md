# LSX Login

The LSX Login extension allows users to log into a dashboard and then see configurable content based on which users can access which content.

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