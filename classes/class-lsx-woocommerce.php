<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LSX_Login_WooCommerce {

	protected static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		//WooCommerce

		add_action( 'init', array( $this, 'woocommerce_add_rewrite_rule' ) );
		add_action( 'init', array( $this, 'add_rewrite_rule' ) );
		add_filter( 'query_vars', array( $this, 'woocommerce_add_query_vars' ), 0 );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'woocommerce_account_menu_items' ) );
		add_filter( 'woocommerce_get_myaccount_page_permalink', array( $this, 'woocommerce_get_myaccount_page_permalink' ) );
		add_action( 'woocommerce_login_form_end', array( $this, 'social_login_buttons' ) );

		//Redirect to My Account page after login
		add_action( 'lsx_login_redirect', array( $this, 'login_redirect' ) );

		if ( class_exists( 'Tour_Operator' ) ) {
			$this->options = get_option( '_lsx-to_settings', false );
		} else {
			$this->options = get_option( '_lsx_settings', false );

			if ( false === $this->options ) {
				$this->options = get_option( '_lsx_lsx-settings', false );
			}
		}

	}

	/**
	 * Main Instance.
	 *
	 * @return LSX_Login_WooCommerce Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add endpoints for my-account pages.
	 */
	public function add_rewrite_rule() {
		if ( class_exists( 'woocommerce' ) ) {
			return;
		}

		$account_slug = $this->get_my_account_page_slug();
		$endpoints = false;
		$endpoints = apply_filters('lsx_my_account_endpoints',$endpoints);
		add_rewrite_tag('%tab%', '([^&]+)');
		if(false !== $endpoints) {
			foreach ($endpoints as $endpoint) {
				add_rewrite_rule( $account_slug.'/'.$endpoint.'/?$', 'index.php?pagename='.$account_slug.'&tab='.$endpoint, 'top' );
			}
		}
	}

	/**
	 * Add endpoints for my-account pages.
	 */
	public function woocommerce_add_rewrite_rule() {
		if ( class_exists( 'woocommerce' ) ) {
			$endpoints = apply_filters( 'lsx_my_account_endpoints', array() );

			foreach ( $endpoints as $endpoint ) {
				add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
			}
		}
	}

	/**
	 * Add endpoints for my-account pages.
	 */
	public function woocommerce_add_query_vars( $vars ) {
		if ( class_exists( 'woocommerce' ) ) {
			$endpoints = apply_filters( 'lsx_my_account_endpoints', array() );

			foreach ( $endpoints as $endpoint ) {
				$vars[] = $endpoint;
			}
		}

		return $vars;
	}

	/**
	 * Insert the new endpoint into the my-account menu.
	 */
	public function woocommerce_account_menu_items( $items ) {
		$tabs_from_lsx = apply_filters( 'lsx_my_account_tabs', array() );

		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );

		foreach ( $tabs_from_lsx as $key => $value ) {
			$items[$key] = $value['label'];
		}

		$items['customer-logout'] = $logout;

		foreach ( $tabs_from_lsx as $key => $value ) {
			if ( has_filter( 'woocommerce_account_' . $key . '_endpoint' ) ) {
				continue;
			}
			add_filter( 'woocommerce_account_' . $key . '_endpoint', array( $this, 'woocommerce_account_x_endpoint' ) );
		}

		return $items;
	}

	/**
	 * Dashboard permalink.
	 * Without this function, WC can't find the correct Dashboard permalink.
	 */
	public function woocommerce_get_myaccount_page_permalink( $permalink ) {
		$account_slug = $this->get_my_account_page_slug();
		$permalink = site_url( '/' . $account_slug . '/' );
		return $permalink;
	}

	/**
	 * Return My Account page slug
	 */
	public function get_my_account_page_slug() {
		$my_account_id = false;

		if ( isset( $this->options['login']['my_account_id'] ) ) {
			$my_account_id = $this->options['login']['my_account_id'];
		}

		if ( false !== $my_account_id ) {
			$my_account_page = get_post( $my_account_id );
			$account_slug = $my_account_page->post_name;
		} else {
			$account_slug = 'my-account';
		}

		return $account_slug;
	}

	/**
	 * Redirect to My Account page after login
	 */
	public function login_redirect( $url ) {
		$account_slug = $this->get_my_account_page_slug();
		$url = site_url( '/' . $account_slug . '/' );
		return $url;
	}

	/**
	 * Redirect to My Account page after login
	 */
	public function social_login_buttons( ) {
		if ( function_exists( 'woocommerce_social_login_buttons') && ! is_user_logged_in() && ! is_checkout() && ! is_cart() && ! is_account_page() ) {
			woocommerce_social_login_buttons( get_permalink() );
		}
	}
}
