<?php

/**
 * Plugin Name: Tiledesk Chat
 * Plugin URI: https://www.tiledesk.com/
 * Description: Tiledesk Live Chat - Add live chat with integrated support bot in your pages with just a few clicks!
 * Version: 1.0.0
 * Author: Tiledesk
 * Author URI: http://www.frontiere21.it/
 * License: GPL2
 */
define('TILEDESKCHAT_VERSION', '1.0.0');

class TiledeskLiveChat
{
    const SCRIPT_URL = '//code.tiledesk.co/';
    const API_URL = 'https://api-v2.tiledesk.co';
    const CHAT_URL = 'https://support.tiledesk.com/dashboard/#/project/';
    const PUBLIC_KEY_OPTION = 'tiledesk-one-public-key';
    const PRIVATE_KEY_OPTION = 'tiledesk-one-private-key';
    const ASYNC_LOAD_OPTION = 'tiledesk-async-load';
    const CLEAR_ACCOUNT_DATA_ACTION = 'tiledesk-chat-reset';
    const TILEDESK_PLUGIN_NAME = 'tiledesk-live-chat';
    const TOGGLE_ASYNC_ACTION = 'tiledesk-chat-toggle-async';

    public function __construct()
    {
        if (!empty($_GET['tiledesk_chat_version'])) {
            echo TILEDESKCHAT_VERSION;
            exit;
        }

        /* Before add link to menu - check is user trying to unninstal */
        if (is_admin() && current_user_can('activate_plugins') && !empty($_GET['tiledesk_one_clear_cache'])) {
            delete_option(TiledeskLiveChat::PUBLIC_KEY_OPTION);
            delete_option(TiledeskLiveChat::PRIVATE_KEY_OPTION);
        }

       

        if (get_option(TiledeskLiveChat::PUBLIC_KEY_OPTION)) {
            add_action('admin_footer', array($this, 'adminJS'));
        }
        
        if (!is_admin()) {
                add_action('wp_footer', array($this, 'enqueueScriptsAsync'), PHP_INT_MAX);
        } else if (current_user_can('activate_plugins')) {
            add_action('admin_menu', array($this, 'addAdminMenuLink'));
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
            add_action('wp_ajax_get_project_keys', array($this, 'ajaxGetProjectKeys'));
            add_action('admin_post_' . TiledeskLiveChat::CLEAR_ACCOUNT_DATA_ACTION . '', array($this, 'uninstall'));
            add_action('admin_post_' . TiledeskLiveChat::TOGGLE_ASYNC_ACTION . '', array($this, 'toggleAsync'));
            add_filter('plugin_action_links', array($this, 'pluginActionLinks'), 10, 2);
        }
    }

    public static function activate()
    {
        update_option(TiledeskLiveChat::ASYNC_LOAD_OPTION, true);
    }

    public static function getRedirectUrl($projectid)
    {
    
            return TiledeskLiveChat::CHAT_URL . $projectid . '/home';
    }

    public function pluginActionLinks($links, $file)
    {
        
        if (strpos($file, basename(__FILE__)) !== false) {
            if (get_option(TiledeskLiveChat::PUBLIC_KEY_OPTION)) {
                $links[] = '<a href="' . admin_url('admin-post.php') . '?action=' . TiledeskLiveChat::CLEAR_ACCOUNT_DATA_ACTION . '">' . esc_html__('Clear Account Data',
                        TiledeskLiveChat::TILEDESK_PLUGIN_NAME) . '</a>';
                if (get_option(TiledeskLiveChat::ASYNC_LOAD_OPTION)) {
                    $toggleAsyncLabel = '✓';
                    $onclickPart = 'onclick="return confirm(\'Disabling asynchronous loading of the chat widget may affect the page loading time of your website. Are you sure you want to disable the asynchronous loading?\');"';
                } else {
                    $toggleAsyncLabel = '✘';
                    $onclickPart = '';
                }
                $links[] = '<a href="' . admin_url('admin-post.php') . '?action=' . TiledeskLiveChat::TOGGLE_ASYNC_ACTION . '" ' . $onclickPart . '>' . esc_html__($toggleAsyncLabel . ' Asynchronous loading',
                        TiledeskLiveChat::TILEDESK_PLUGIN_NAME) . '</a>';
            }
        }
        return $links;
    }

    public function toggleAsync()
    {
        update_option(TiledeskLiveChat::ASYNC_LOAD_OPTION, !get_option(TiledeskLiveChat::ASYNC_LOAD_OPTION));
        wp_redirect(admin_url('plugins.php'));
        die();
    }


    // important
    public function ajaxGetProjectKeys()
    {
        update_option(TiledeskLiveChat::PUBLIC_KEY_OPTION, $_POST['project_id']);
        echo TiledeskLiveChat::getRedirectUrl($_POST['project_id']);
        exit();
    }

    public function ajaxTiledeskChatSaveKeys()
    {
        if (!is_admin()) {
            exit;
        }

// rivedere
        if (empty($_POST['public_key'])) {
            exit;
        }
        update_option(TiledeskLiveChat::PUBLIC_KEY_OPTION, $_POST['public_key']);
        echo '1';
        exit;
    }


    public function enqueueScriptsAsync()
    {
        $publicKey = TiledeskLiveChat::getPublicKey();
        $asyncScript = <<<SRC
    
    <script type="application/javascript">
        window.tiledeskSettings = 
          {
            projectid: "$publicKey",
          };
          (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id; //js.async=!0;
            js.src = "https://widget.tiledesk.com/v2/tiledesk.js";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'tiledesk-jssdk'));
      </script>

SRC;
        echo $asyncScript;
    }

    public static function getPublicKey()
    {
        $publicKey = get_option(TiledeskLiveChat::PUBLIC_KEY_OPTION);

        if ($publicKey) {
            return $publicKey;
        }
    }


    public function enqueueAdminScripts()
    {
        wp_enqueue_script('tiledesk-chat-admin', plugins_url('media/js/options.js', __FILE__), array(), TILEDESKCHAT_VERSION,
            true);
        wp_enqueue_style('tiledesk-chat-admin-style', plugins_url('media/css/options.css', __FILE__), array(),
            TILEDESKCHAT_VERSION);
    }

    public function adminJS()
    {
        $publicKey = TiledeskLiveChat::getPublicKey();        
        $redirectUrl = '';

        if ($publicKey && $publicKey != 'false') {
            $redirectUrl = TiledeskLiveChat::getRedirectUrl($publicKey);
        } else {
            $redirectUrl = admin_url('admin-ajax.php?action=tiledesk_chat_redirect');
        }

        echo "<script>jQuery('a[href=\"admin.php?page=tiledesk-chat\"]').attr('href', '" . $redirectUrl . "').attr('target', '_blank') </script>";
    }

    public function addAdminMenuLink()
    {
        add_menu_page(
            'Tiledesk Chat', 'Tiledesk Chat', 'manage_options', 'tiledesk-chat', array($this, 'addAdminPage'),
            content_url() . '/plugins/' . TiledeskLiveChat::TILEDESK_PLUGIN_NAME . '/media/img/icon.png'
        );
    }

    public function addAdminPage()
    {
        // Set class property
        $dir = plugin_dir_path(__FILE__);
        include $dir . 'options.php';
    }

    public function uninstall()
    {
        delete_option(TiledeskLiveChat::PUBLIC_KEY_OPTION);
        delete_option(TiledeskLiveChat::PRIVATE_KEY_OPTION);
        delete_option(TiledeskLiveChat::ASYNC_LOAD_OPTION);
        wp_redirect(admin_url('plugins.php'));
        die();
    }
}

add_action('init', 'initialize_tiledesk');

function initialize_tiledesk()
{
    $tiledeskLiveChat = new TiledeskLiveChat();
}

register_activation_hook(__FILE__, array('TiledeskLiveChat', 'activate'));