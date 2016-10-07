<?php
/**
 * @package WordPress
 * @subpackage WCMentor
 *
 **/
#################################################################################################### */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Initiate Addons
 **/
require_once( 'addons/initiate-addons.php' );


/**
 * WCMentorFunctions Class
 **/
$WCMentorFunctions = new WCMentorFunctions();
$WCMentorFunctions->init();
class WCMentorFunctions {

	/**
	 * file_path
	 *
	 * @access public
	 * @var string
	 * @since 1.0
	 **/
	public $file_path = '';


	/**
 	 * __construct
	 **/
	function __construct() {

	} // end function __construct


	/**
	* initWCMentorFunctions
	**/
	function init() {

		add_action( 'init', [ $this, 'action__init' ] );

	} // end function initWCMentorFunctions


	/**
	 * set
	 **/
	 function set( $key, $val = false ) {

		 if ( isset( $key ) AND ! empty( $key ) ) {
			 $this->$key = $val;
		 }

	 } // end function set


	/**
	 * init
	 **/
	function action__init() {

		add_action( 'wp_enqueue_scripts', [ $this, 'register_style_and_scripts' ], 9 );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );

	} // end function init


	/**
	 * register_style_and_scripts
	 **/
	function register_style_and_scripts() {

		wp_register_style( 'wcmentor-style-css', get_stylesheet_directory_uri() . "/css/style.css", [], null );
		wp_register_script( 'wcmentor-scripts-js', get_stylesheet_directory_uri() . "/js/siteScripts.js", [ 'jquery' ], null );

	} // end function register_style_and_scripts


	/**
	 * wp_enqueue_scripts
	 **/
	function wp_enqueue_scripts() {

		wp_enqueue_style( 'wcmentor-style-css' );
		wp_enqueue_script( 'wcmentor-scripts-js' );

	} // end function wp_enqueue_scripts


} // end class WCMentorFunctions
