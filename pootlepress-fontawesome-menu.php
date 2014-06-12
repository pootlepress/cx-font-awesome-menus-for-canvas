<?php
/*
Plugin Name: Canvas Extension - FontAwesome Menu
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to customize menu with FontAwesome icons.
Version: 1.1.0
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

$GLOBALS['pootlepress_fontawesome_menu'] = new Pootlepress_FontAwesome_Menu( __FILE__ );
$GLOBALS['pootlepress_fontawesome_menu']->version = '1.1.0';

?>
