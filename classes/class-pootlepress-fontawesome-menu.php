<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Pootlepress_FontAwesome_Menu Class
 *
 * Base class for the Pootlepress FontAwesome Menu.
 *
 * @package WordPress
 * @subpackage Pootlepress_FontAwesome_Menu
 * @category Core
 * @author Pootlepress
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $version
 * 
 * - __construct()
 * - add_theme_options()
 * - get_menu_styles()
 * - load_stylesheet()
 * - load_script()
 * - load_localisation()
 * - check_plugin()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 * - get_header()
 * - woo_nav_custom()
 */
class Pootlepress_FontAwesome_Menu {
	public $token = 'pootlepress-fontawesome-menu';
	public $version;
	private $file;

    public $mobileMenuIconPos;
    public $mobileMenuIconColor;
    public $mobileMenuIconSize;
    public $mobileMenuLabelRemove;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->load_plugin_textdomain();
		add_action( 'init', 'check_main_heading', 0 );
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );

		// Add the custom theme options.
		add_filter( 'option_woo_template', array( &$this, 'add_theme_options' ) );

		// Load for a method/function for the selected style and load it.
		add_action( 'get_header', array( &$this, 'get_header' ) , 1000);

		// Load for a stylesheet for the selected style and load it.
		//add_action( 'wp_enqueue_scripts', array( &$this, 'load_stylesheet' ) );

        add_action('wp_head', array(&$this, 'option_css'), 100); // hook it behind mobile mnu manager hook, so can overwrite its style

        add_filter( 'wp_nav_menu_args', array(&$this, 'filter_widget_menu_args') );

        $this->mobileMenuIconPos = get_option('pootlepress-fa-menu-mobile-menu-icon-pos', 'Left of nav label');
        $this->mobileMenuIconColor = get_option('pootlepress-fa-menu-mobile-menu-icon-color', '#000000');
        $this->mobileMenuIconSize = get_option('pootlepress-fa-menu-mobile-menu-icon-size', 'Large');
        $this->mobileMenuLabelRemove = get_option('pootlepress-fa-menu-mobile-menu-label-remove', 'false');

	} // End __construct()

	/**
	 * Add theme options to the WooFramework.
	 * @access public
	 * @since  1.0.0
	 * @param array $o The array of options, as stored in the database.
	 */
	public function add_theme_options ( $o ) {
		
		$o[] = array(
				'name' => __( 'FontAwesome Menu', 'pootlepress-fontawesome-menu' ),
				'type' => 'subheading'
				);
//        $o[] = array(
//            'id' => 'pootlepress-fa-menu-enable',
//            'name' => __( 'Use Papple Menu', 'pootlepress-fontawesome-menu' ),
//            'desc' => __( 'Enable Papple Menu', 'pootlepress-fontawesome-menu' ),
//            'std' => 'true',
//            'type' => 'checkbox'
//        );
        //
        // Primary Nav
        //
        $o[] = array(
            "id" => "pootlepress-fa-menu-primary-nav-icon-pos",
            "name" => __( 'Primary Navigation Icon Position', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Primary Navigation Icon Position.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Left of nav label",
                "Right of nav label",
                "Above nav label(centered)",
                "Below nav label(centered)")
        );
        $o[] = array(
            "id" => "pootlepress-fa-menu-primary-nav-icon-size",
            "name" => __( 'Size of Primary Navigation Icons', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Size of Primary Navigation Icons.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Large",
                "2x",
                "3x",
                "4x"
                )
        );
        $o[] = array(
            'id' => 'pootlepress-fa-menu-primary-nav-label-remove',
            'name' => __( 'Remove navigation label on Primary Nav?', 'pootlepress-fontawesome-menu' ),
            'desc' => __( 'Remove navigation label on Primary Nav?', 'pootlepress-fontawesome-menu' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] =	array(
            'id' => 'pootlepress-fa-menu-primary-nav-icon-color',
            'name' => 'Choose color of primary navigation icons',
            'desc' => 'Choose color of primary navigation icons',
            'std' => '#000000',
            'type' => 'color'
        );

        //
        // Primary Nav Submenu
        //
        $o[] = array(
            "id" => "pootlepress-fa-menu-primary-nav-sub-menu-icon-pos",
            "name" => __( 'Primary Nav Sub menu Icon Position', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Primary Nav Sub menu Icon Position.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Left of nav label",
                "Right of nav label")
        );
        $o[] = array(
            "id" => "pootlepress-fa-menu-primary-nav-sub-menu-icon-size",
            "name" => __( 'Size of Primary Nav Sub Menu Icons', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Size of Primary Nav Sub Menu Icons.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Large",
                "2x",
                "3x",
                "4x"
            )
        );
        $o[] =	array(
            'id' => 'pootlepress-fa-menu-primary-nav-sub-menu-icon-color',
            'name' => 'Choose color of primary nav sub menu icons',
            'desc' => 'Choose color of primary nav sub menu icons',
            'std' => '#000000',
            'type' => 'color'
        );
        //
        // Top Nav
        //
        $o[] = array(
            "id" => "pootlepress-fa-menu-top-nav-icon-pos",
            "name" => __( 'Top Navigation Icon Position', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Top Navigation Icon Position.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Left of nav label",
                "Right of nav label")
        );
        $o[] = array(
            "id" => "pootlepress-fa-menu-top-nav-icon-size",
            "name" => __( 'Size of Top Navigation Icons', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Size of Top Navigation Icons.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Large",
                "2x",
                "3x",
                "4x"
            )
        );
        $o[] = array(
            'id' => 'pootlepress-fa-menu-top-nav-label-remove',
            'name' => __( 'Remove navigation label on Top Nav?', 'pootlepress-fontawesome-menu' ),
            'desc' => __( 'Remove navigation label on Top Nav?', 'pootlepress-fontawesome-menu' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] =	array(
            'id' => 'pootlepress-fa-menu-top-nav-icon-color',
            'name' => 'Choose color of top navigation icons',
            'desc' => 'Choose color of top navigation icons',
            'std' => '#000000',
            'type' => 'color'
        );

        //
        // Top Nav Submenu
        //
        $o[] = array(
            "id" => "pootlepress-fa-menu-top-nav-sub-menu-icon-pos",
            "name" => __( 'Top Nav Sub menu Icon Position', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Top Nav Sub menu Icon Position.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Left of nav label",
                "Right of nav label")
        );
        $o[] = array(
            "id" => "pootlepress-fa-menu-top-nav-sub-menu-icon-size",
            "name" => __( 'Size of Top Nav Sub Menu Icons', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Size of Top Nav Sub Menu Icons.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Large",
                "2x",
                "3x",
                "4x"
            )
        );
        $o[] =	array(
            'id' => 'pootlepress-fa-menu-top-nav-sub-menu-icon-color',
            'name' => 'Choose color of top nav sub menu icons',
            'desc' => 'Choose color of top nav sub menu icons',
            'std' => '#000000',
            'type' => 'color'
        );

        //
        // Custom Widget Menu
        //
        $o[] = array(
            "id" => "pootlepress-fa-menu-widget-nav-icon-pos",
            "name" => __( 'Custom Menu Widget Icon Position', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Custom Menu Widget Icon Position.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Left of nav label",
                "Right of nav label")
        );
        $o[] = array(
            "id" => "pootlepress-fa-menu-widget-nav-icon-size",
            "name" => __( 'Size of Custom Menu Widget Icons', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Size of Custom Menu Widget Icons.', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Large",
                "2x",
                "3x",
                "4x"
            )
        );
        $o[] = array(
            'id' => 'pootlepress-fa-menu-widget-nav-label-remove',
            'name' => __( 'Remove navigation label on Custom Menu Widgets?', 'pootlepress-fontawesome-menu' ),
            'desc' => __( 'Remove navigation label on Custom Menu Widgets?', 'pootlepress-fontawesome-menu' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] =	array(
            'id' => 'pootlepress-fa-menu-widget-nav-icon-color',
            'name' => 'Choose color of Custom Menu icons',
            'desc' => 'Choose color of Custom Menu icons',
            'std' => '#000000',
            'type' => 'color'
        );

        //
        // mobile menu
        //
        $o[] = array(
            "id" => "pootlepress-fa-menu-mobile-menu-icon-pos",
            "name" => __( 'Mobile Menu Icon Position', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Mobile Menu Icon Position', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Left of nav label",
                "Right of nav label")
        );
        $o[] = array(
            "id" => "pootlepress-fa-menu-mobile-menu-icon-size",
            "name" => __( 'Size of Mobile Menu Icons', 'pootlepress-fontawesome-menu' ),
            "desc" => __( 'Size of Mobile Menu Icons', 'pootlepress-fontawesome-menu' ),
            "type" => "select",
            "options" => array(
                "Large",
                "2x",
                "3x",
                "4x"
            )
        );
        $o[] = array(
            'id' => 'pootlepress-fa-menu-mobile-menu-label-remove',
            'name' => __( 'Remove navigation labels on Mobile Menu?', 'pootlepress-fontawesome-menu' ),
            'desc' => __( 'Remove navigation labels on Mobile Menu?', 'pootlepress-fontawesome-menu' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] =	array(
            'id' => 'pootlepress-fa-menu-mobile-menu-icon-color',
            'name' => 'Choose color of Mobile Menu icons',
            'desc' => 'Choose color of Mobile Menu icons',
            'std' => '#ffffff',
            'type' => 'color'
        );
        return $o;
	} // End add_theme_options()



    public function option_css() {

        $primaryNavIconPos = get_option('pootlepress-fa-menu-primary-nav-icon-pos', 'Left of nav label');
        $primaryNavIconColor = get_option('pootlepress-fa-menu-primary-nav-icon-color', '#000000');
        $primaryNavIconSize = get_option('pootlepress-fa-menu-primary-nav-icon-size', 'Large');

        $primaryNavSubMenuIconPos = get_option('pootlepress-fa-menu-primary-nav-sub-menu-icon-pos', 'Left of nav label');
        $primaryNavSubMenuIconColor = get_option('pootlepress-fa-menu-primary-nav-sub-menu-icon-color', '#000000');
        $primaryNavSubMenuIconSize = get_option('pootlepress-fa-menu-primary-nav-sub-menu-icon-size', 'Large');

        $topNavIconPos = get_option('pootlepress-fa-menu-top-nav-icon-pos', 'Left of nav label');
        $topNavIconColor = get_option('pootlepress-fa-menu-top-nav-icon-color', '#000000');
        $topNavIconSize = get_option('pootlepress-fa-menu-top-nav-icon-size', 'Large');
        $topNavLabelRemove = get_option('pootlepress-fa-menu-top-nav-label-remove', 'false');

        $topNavSubMenuIconPos = get_option('pootlepress-fa-menu-top-nav-sub-menu-icon-pos', 'Left of nav label');
        $topNavSubMenuIconColor = get_option('pootlepress-fa-menu-top-nav-sub-menu-icon-color', '#000000');
        $topNavSubMenuIconSize = get_option('pootlepress-fa-menu-top-nav-sub-menu-icon-size', 'Large');

        $widgetNavIconPos = get_option('pootlepress-fa-menu-widget-nav-icon-pos', 'Left of nav label');
        $widgetNavIconColor = get_option('pootlepress-fa-menu-widget-nav-icon-color', '#000000');
        $widgetNavLabelRemove = get_option('pootlepress-fa-menu-widget-nav-label-remove', 'false');

        $primaryNavIconCss = '';
        $primaryNavLinkCss = '';
        switch ($primaryNavIconPos) {
            case 'Left of nav label':
                $primaryNavIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
            case 'Right of nav label':
                $primaryNavIconCss .= "float: right; margin-left: 5px; line-height: inherit;";
                break;
            case 'Above nav label(centered)':
                $primaryNavIconCss .= "display: block; text-align: center;";
                $primaryNavLinkCss .= "text-align: center;";
                break;
            case 'Below nav label(centered)':
                $primaryNavIconCss .= "display: block; text-align: center;";
                $primaryNavLinkCss .= "text-align: center;";
                break;
        }

        if ($primaryNavIconPos == 'Left of nav label' ||
            $primaryNavIconPos == 'Right of nav label') {
            $lineHeight = '';
            switch ($primaryNavIconSize) {
                case 'Large':
                    $lineHeight = '1.3333333333333333em';
                    break;
                case '2x':
                    $lineHeight = '2em';
                    break;
                case '3x':
                    $lineHeight = '3em';
                    break;
                case '4x':
                    $lineHeight = '4em';
                    break;
                default:
                    $lineHeight = '1.3333333333333333em';
                    break;
            }

            $primaryNavLinkCss .= " line-height: " . $lineHeight . ";";
        }

        $primaryNavIconCss .= " color: " . $primaryNavIconColor . ";";

        //
        // Primary Nav Sub Menu
        //
        $primaryNavSubMenuIconCss = '';
        switch ($primaryNavSubMenuIconPos) {
            case 'Left of nav label':
                $primaryNavSubMenuIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
            case 'Right of nav label':
                $primaryNavSubMenuIconCss .= "float: right; margin-left: 5px; line-height: inherit;";
                break;
            default:
                $primaryNavSubMenuIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
        }

        $primaryNavSubMenuLinkLineHeight = '';
        switch ($primaryNavSubMenuIconSize) {
            case 'Large':
                $primaryNavSubMenuLinkLineHeight = '1.3333333333333333em';
                break;
            case '2x':
                $primaryNavSubMenuLinkLineHeight = '2em';
                break;
            case '3x':
                $primaryNavSubMenuLinkLineHeight = '3em';
                break;
            case '4x':
                $primaryNavSubMenuLinkLineHeight = '4em';
                break;
            default:
                $primaryNavSubMenuLinkLineHeight = '1.3333333333333333em';
                break;
        }

        $primaryNavSubMenuLinkCss = '';
        $primaryNavSubMenuLinkCss .= " line-height: " . $primaryNavSubMenuLinkLineHeight . ";";

        $primaryNavSubMenuIconCss .= " color: " . $primaryNavSubMenuIconColor . ";";
        $primaryNavSubMenuIconCss .= " text-decoration: initial;";

        //
        // Top Nav
        //
        $topNavIconCss = '';
        if ($topNavLabelRemove === 'false') {
            switch ($topNavIconPos) {
                case 'Left of nav label':
                    $topNavIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                    break;
                case 'Right of nav label':
                    $topNavIconCss .= "float: right; margin-left: 5px; line-height: inherit;";
                    break;
                default:
                    $topNavIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                    break;
            }
        }

        $topNavIconCss .= " color: " . $topNavIconColor . ";";

        $topNavLinkLineHeight = '';
        switch ($topNavIconSize) {
            case 'Large':
                $topNavLinkLineHeight = '1.3333333333333333em';
                break;
            case '2x':
                $topNavLinkLineHeight = '2em';
                break;
            case '3x':
                $topNavLinkLineHeight = '3em';
                break;
            case '4x':
                $topNavLinkLineHeight = '4em';
                break;
            default:
                $topNavLinkLineHeight = '1.3333333333333333em';
                break;
        }

        $topNavLinkCss = '';
        if (class_exists('Pootlepress_Top_Nav_Manager') && $topNavIconSize == 'Large' ) {
        } else {
            $topNavLinkCss .= " line-height: " . $topNavLinkLineHeight . " !important;";
        }

        //
        // Top Nav Sub Menu
        //
        $topNavSubMenuIconCss = '';
        switch ($topNavSubMenuIconPos) {
            case 'Left of nav label':
                $topNavSubMenuIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
            case 'Right of nav label':
                $topNavSubMenuIconCss .= "float: right; margin-left: 5px; line-height: inherit;";
                break;
            default:
                $topNavSubMenuIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
        }

        $topNavSubMenuIconCss .= " color: " . $topNavSubMenuIconColor . ";";
        $topNavSubMenuIconCss .= " text-decoration: initial;";

        $topNavSubMenuLinkLineHeight = '';
        switch ($topNavSubMenuIconSize) {
            case 'Large':
                $topNavSubMenuLinkLineHeight = '1.3333333333333333em';
                break;
            case '2x':
                $topNavSubMenuLinkLineHeight = '2em';
                break;
            case '3x':
                $topNavSubMenuLinkLineHeight = '3em';
                break;
            case '4x':
                $topNavSubMenuLinkLineHeight = '4em';
                break;
            default:
                $topNavSubMenuLinkLineHeight = '1.3333333333333333em';
                break;
        }

        $topNavSubMenuLinkCss = '';
        $topNavSubMenuLinkCss .= " line-height: " . $topNavSubMenuLinkLineHeight . " !important;";

        //
        // Widget Nav
        //
        $widgetNavIconCss = '';
        if ($widgetNavLabelRemove === 'false') {
            switch ($widgetNavIconPos) {
                case 'Left of nav label':
                    $widgetNavIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                    break;
                case 'Right of nav label':
                    $widgetNavIconCss .= "float: right; margin-left: 5px; line-height: inherit;";
                    break;
                default:
                    $widgetNavIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                    break;
            }
        }

        $widgetNavIconCss .= " color: " . $widgetNavIconColor . ";";
        $widgetNavIconCss .= " text-decoration: initial;";

        //
        // Mobile Menu
        //
        $mobileMenuIconCss = 'text-decoration: none; ';
        switch ($this->mobileMenuIconPos) {
            case 'Left of nav label':
                $mobileMenuIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
            case 'Right of nav label':
                $mobileMenuIconCss .= "float: right; margin-left: 5px; line-height: inherit;";
                break;
            default:
                $mobileMenuIconCss .= "float: left; margin-right: 5px; line-height: inherit;";
                break;
        }

        $mobileMenuLinkLineHeight = '';
        switch ($this->mobileMenuIconSize) {
            case 'Large':
                $mobileMenuLinkLineHeight = '1.3333333333333333em';
                break;
            case '2x':
                $mobileMenuLinkLineHeight = '2em';
                break;
            case '3x':
                $mobileMenuLinkLineHeight = '3em';
                break;
            case '4x':
                $mobileMenuLinkLineHeight = '4em';
                break;
            default:
                $mobileMenuLinkLineHeight = '1.3333333333333333em';
                break;
        }

        $mobileMenuLinkCss = '';
        $mobileMenuLinkCss .= " line-height: " . $mobileMenuLinkLineHeight . " !important;";
        $mobileMenuLinkCss .= " height: " . $mobileMenuLinkLineHeight . " !important;";

        $mobileMenuIconFontSize = $mobileMenuLinkLineHeight;
        $mobileMenuIconCss .= " font-size: " . $mobileMenuIconFontSize . " !important;";
        $mobileMenuIconCss .= " color: " . $this->mobileMenuIconColor . " !important;";

        $mobileMenuLabelCss = '';
        if ($this->mobileMenuLabelRemove == 'true') {
            $mobileMenuLabelCss .= "display: none;";
        }

        $css = '';

        $css .= "@media only screen and (min-width: 768px) {\n";

        $css .= "#navigation ul.nav > li > a {\n";
        $css .= "\t" . $primaryNavLinkCss . "\n";
        $css .= "}\n";

        $css .= "#navigation ul.nav > li > a > i {\n";
        $css .= "\t" . $primaryNavIconCss . "\n";
        $css .= "}\n";

        $css .= "#navigation ul.nav > li ul li > a {\n";
        $css .= "\t" . $primaryNavSubMenuLinkCss . "\n";
        $css .= "}\n";

        $css .= "#navigation ul.nav > li ul li > a > i {\n";
        $css .= "\t" . $primaryNavSubMenuIconCss . "\n";
        $css .= "}\n";

        $css .= "}\n";

        $css .= "#top-nav > li > a {\n";
        $css .= "\t" . $topNavLinkCss . "\n";
        $css .= "}\n";

        $css .= "#top-nav > li > a > i {\n";
        $css .= "\t" . $topNavIconCss . "\n";
        $css .= "}\n";

        $css .= "#top-nav > li ul li > a {\n";
        $css .= "\t" . $topNavSubMenuLinkCss . "\n";
        $css .= "}\n";

        $css .= "#top-nav > li ul li > a > i {\n";
        $css .= "\t" . $topNavSubMenuIconCss . "\n";
        $css .= "}\n";

        $css .= ".widget_nav_menu ul {\n";
        $css .= "\t" . "list-style-type: none;\n";
        $css .= "}\n";

        $css .= ".widget_nav_menu .menu li > a > i {\n";
        $css .= "\t" . $widgetNavIconCss . "\n";
        $css .= "}\n";

        //
        // Begin Mobile Menu
        //
        $css .= "@media only screen and (max-width: 767px) {\n";

        $css .= "#navigation ul.nav li a, \n" .
         "#navigation ul.nav li.current-menu-item a, \n" .
         "#navigation ul.nav li.current-menu-ancestor a, \n" .
         "#navigation ul li.menu-item:not(.current-menu-item) a {\n";
        $css .= "\t" . $mobileMenuLinkCss . ";\n";
        $css .= "}\n";

        $css .= "#navigation ul.nav li > a > i, #navigation ul.nav li > a > i:before {\n";
        $css .= "\t" . $mobileMenuIconCss . ";\n";
        $css .= "}\n";

        $css .= "#navigation ul.nav li > a > span {\n";
        $css .= "\t" . $mobileMenuLabelCss . ";\n";
        $css .= "}\n";

        $css .= "}\n"; // End Mobile Menu

        echo "<style>".$css."</style>";
    }

	/**
	 * Load stylesheet required for the style, if has any.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_stylesheet () {
	    wp_enqueue_style('pootlepress-fontawesome-menu', esc_url( plugins_url( 'styles/fontawesome-menu.css', $this->file ) ) );
	} // End load_stylesheet()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->token, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = $this->token;
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->token . '-version', $this->version );
		}
	} // End register_plugin_version()

    /**
     * Hook for get_header
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    public function get_header () {
        remove_action('woo_nav_inside', 'woo_nav_primary', 10); // this is added by canvas
        //remove_action( 'woo_header_after','woo_nav_primary', 10 );
        remove_action('woo_top', 'woo_top_navigation', 10);

        add_action( 'woo_nav_inside', array(&$this, 'woo_nav_custom'), 10 );
        add_action('woo_top', array($this, 'woo_top_navigation_custom'), 10);
    }


    public function woo_nav_custom() {

        //$enabled = get_option('pootlepress-apple-menu-enable', 'true');
        //if ($enabled == '') {
        //    $enabled = 'true';
        //}

        //if ($enabled == 'true') {

            ?>

        <a href="<?php echo home_url(); ?>" class="nav-home"><i class="fa fa-home"></i><span><?php _e( 'Home', 'woothemes' ); ?></span></a>

	<?php
	if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
        echo '<h3>' . woo_get_menu_name( 'primary-menu' ) . '</h3>';
        wp_nav_menu( array( 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl',
            'theme_location' => 'primary-menu', 'link_before' => '<span>', 'link_after' => '</span>',
            'walker' => new Pootlepress_FA_Main_Nav_Walker() ) );
    } else {
        ?>
        <ul id="main-nav" class="nav fl">
            <?php
            if ( get_option( 'woo_custom_nav_menu' ) == 'true' ) {
                if ( function_exists( 'woo_custom_navigation_output' ) ) { woo_custom_navigation_output( 'name=Woo Menu 1' ); }
            } else { ?>

                <?php if ( is_page() ) { $highlight = 'page_item'; } else { $highlight = 'page_item current_page_item'; } ?>
                <li class="<?php echo esc_attr( $highlight ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
                <?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
            <?php } ?>
        </ul><!-- /#nav -->
    <?php }

        //woo_nav_after();
    } // End woo_nav_custom()

    function woo_top_navigation_custom() {
        if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) {
            ?>
            <div id="top">
                <div class="col-full">
                    <?php
                    echo '<h3 class="top-menu">' . woo_get_menu_name( 'top-menu' ) . '</h3>';
                    wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav',
                        'menu_class' => 'nav top-navigation fl', 'theme_location' => 'top-menu',
                        'link_before' => '<span>', 'link_after' => '</span>',
                        'walker' => new Pootlepress_FA_Top_Nav_Walker()
                    ) );
                    ?>
                </div>
            </div><!-- /#top -->
        <?php
        }
    } // End woo_top_navigation()

    public function filter_widget_menu_args($args) {
        if ($args['fallback_cb'] === '') {
            $args['walker'] = new Pootlepress_FA_Widget_Nav_Walker();
            return $args;
        } else {
            return $args;
        }
    }
} // End Class


