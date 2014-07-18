<?php
/*
Plugin Name: Canvas Extension - FontAwesome Menu
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to customize menu with FontAwesome icons.
Version: 1.2
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-fontawesome-menu-functions.php' );
require_once( 'classes/class-pootlepress-fontawesome-menu.php' );
require_once('classes/class-main-nav-walker.php');
require_once('classes/class-top-nav-walker.php');
require_once('classes/class-widget-nav-walker.php');
require_once( 'classes/class-pootlepress-updater.php');

$GLOBALS['pootlepress_fontawesome_menu'] = new Pootlepress_FontAwesome_Menu( __FILE__ );
$GLOBALS['pootlepress_fontawesome_menu']->version = '1.2';

add_action('init', 'pp_fam_updater');
function pp_fam_updater()
{
    if (!function_exists('get_plugin_data')) {
        include(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $data = get_plugin_data(__FILE__);
    $wptuts_plugin_current_version = $data['Version'];
    $wptuts_plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
    $wptuts_plugin_slug = plugin_basename(__FILE__);
    new Pootlepress_Updater ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}
?>
