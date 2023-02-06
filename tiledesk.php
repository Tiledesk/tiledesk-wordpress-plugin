<?php

/**
 * Plugin Name: Tiledesk Live Chat for WordPress
 * Plugin URI: https://www.tiledesk.com/
 * Description: Add Tiledesk Live Chat with integrated support bot in your pages with just a few clicks.
 * Version: 1.0.2
 * Author: tiledesk
 * Author URI: https://www.tiledesk.com/
 * License: GPL2
 */

define( 'TILEDESKCHAT_VERSION', '1.0.2' );

class TiledeskLiveChat {
	const CONSOLE_URL = 'https://console.tiledesk.com/v2/dashboard/#/project/';
	const PUBLIC_KEY_OPTION = 'tiledesk-one-public-key';
	const PRIVATE_KEY_OPTION = 'tiledesk-one-private-key';
	const ASYNC_LOAD_OPTION = 'tiledesk-async-load';
	const CLEAR_ACCOUNT_DATA_ACTION = 'tiledesk-chat-reset';
	const TILEDESK_PLUGIN_NAME = 'tiledesk-live-chat';
	const JS_SDK = 'https://widget.tiledesk.com/v4/launch.js';

	public function __construct() {
		if ( ! empty( $_GET['tiledesk_chat_version'] ) ) {
			echo esc_attr( TILEDESKCHAT_VERSION );
			exit;
		}

		/* Before add link to menu - check is user trying to uninstall */
		if ( is_admin() && current_user_can( 'activate_plugins' ) && ! empty( $_GET['tiledesk_one_clear_cache'] ) ) {
			delete_option( TiledeskLiveChat::PUBLIC_KEY_OPTION );
			delete_option( TiledeskLiveChat::PRIVATE_KEY_OPTION );
		}

		if ( get_option( TiledeskLiveChat::PUBLIC_KEY_OPTION ) ) {
			add_action( 'admin_footer', array( $this, 'scripts_in_admin_footer' ) );
		}

		if ( ! is_admin() ) {
			add_action( 'wp_footer', array( $this, 'scripts_in_footer' ) );
		} else if ( current_user_can( 'activate_plugins' ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu_link' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'wp_ajax_get_project_keys', array( $this, 'ajax_get_project_keys' ) );
			add_action( 'admin_post_' . TiledeskLiveChat::CLEAR_ACCOUNT_DATA_ACTION . '', array( $this, 'uninstall' ) );
			add_filter( 'plugin_action_links', array( $this, 'pluginActionLinks' ), 10, 2 );
		}
	}

	public static function activate() {
		update_option( TiledeskLiveChat::ASYNC_LOAD_OPTION, true );
	}

	public function pluginActionLinks( $links, $file ) {
		if ( strpos( $file, basename( __FILE__ ) ) !== false ) {
			if ( get_option( TiledeskLiveChat::PUBLIC_KEY_OPTION ) ) {
				$links[] = sprintf( '<a href="%s?action=%s">%s</a>', admin_url( 'admin-post.php' ), TiledeskLiveChat::CLEAR_ACCOUNT_DATA_ACTION, esc_html__(
					'Clear Account Data',
					TiledeskLiveChat::TILEDESK_PLUGIN_NAME
				) );
			}
		}

		return $links;
	}

	public function ajax_get_project_keys() {
		$project_id = sanitize_text_field( $_POST['project_id'] );
		update_option( TiledeskLiveChat::PUBLIC_KEY_OPTION, $project_id );
		echo esc_url( TiledeskLiveChat::get_redirect_url( $project_id ) );
		exit();
	}

	public static function get_redirect_url( $project_id ) {
		return TiledeskLiveChat::CONSOLE_URL . $project_id . '/home';
	}

	public function ajaxTiledeskChatSaveKeys() {
		if ( ! is_admin() ) {
			exit;
		}

		// Cleanup key field
		$public_key = sanitize_text_field( $_POST['public_key'] );

		if ( empty( $public_key ) ) {
			exit;
		}

		// Update key option
		update_option( TiledeskLiveChat::PUBLIC_KEY_OPTION, $public_key );
		echo '1';
		exit;
	}

	/**
	 * Outputs script tags right before closing </body> tag
	 *
	 * We want it in the footer to avoid having to hook on document.onload -- all the DOM we need is
	 * already loaded by now.
	 *
	 * While WordPress has a system to load JavaScript assets, unfortunately it still doesn't support
	 * the ES6 `type=module` convention, so we chose to print the script tags manually in the footer.
	 */
	public function scripts_in_footer() {
		$projectid = TiledeskLiveChat::getPublicKey();

		// Create basic config for widget
		$widget_config = [
			'projectid' => esc_attr( $projectid ),
		];

		// Add enrichment for logged-in users
		if ( is_user_logged_in() ) {
			$current_user                  = wp_get_current_user();
			$widget_config['userEmail']    = esc_attr( $current_user->user_email );
			$widget_config['userFullname'] = esc_attr( $current_user->display_name );
		}

		require_once __DIR__ . '/templates/footer-embed.php';
	}

	public static function getPublicKey() {
		$publicKey = get_option( TiledeskLiveChat::PUBLIC_KEY_OPTION );

		if ( $publicKey ) {
			return $publicKey;
		}
	}

	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'tiledesk-chat-admin', plugins_url( 'media/js/options.js', __FILE__ ), [], TILEDESKCHAT_VERSION, true );
		wp_enqueue_style( 'tiledesk-chat-admin-style', plugins_url( 'media/css/options.css', __FILE__ ), [], TILEDESKCHAT_VERSION );
	}

	public function scripts_in_admin_footer() {
		$publicKey   = TiledeskLiveChat::getPublicKey();
		$redirectUrl = '';

		if ( $publicKey && $publicKey != 'false' ) {
			$redirectUrl = TiledeskLiveChat::get_redirect_url( $publicKey );
		} else {
			$redirectUrl = admin_url( 'admin-ajax.php?action=tiledesk_chat_redirect' );
		}

		require_once __DIR__ . '/templates/footer-admin.php';
	}

	public function add_admin_menu_link() {
		add_menu_page(
			'Tiledesk Chat',
			'Tiledesk Chat',
			'manage_options',
			'tiledesk-chat',
			array( $this, 'add_admin_page' ),
			plugins_url( 'media/img/icon.png', __FILE__ )
		);
	}

	public function add_admin_page() {
		require_once __DIR__ . '/templates/options.php';
	}

	public function uninstall() {
		delete_option( TiledeskLiveChat::PUBLIC_KEY_OPTION );
		delete_option( TiledeskLiveChat::PRIVATE_KEY_OPTION );
		wp_redirect( admin_url( 'plugins.php' ) );
		die();
	}
}

add_action( 'init', 'initialize_tiledesk' );

function initialize_tiledesk() {
	$tiledeskLiveChat = new TiledeskLiveChat();
}

register_activation_hook( __FILE__, array( 'TiledeskLiveChat', 'activate' ) );