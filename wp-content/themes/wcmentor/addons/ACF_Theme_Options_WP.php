<?php
/**
 * @package WordPress
 * @subpackage ProjectName
 *
 **/
####################################################################################################

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * ACF_Theme_Options_WP
 **/
$ACF_Theme_Options_WP = new ACF_Theme_Options_WP();
$ACF_Theme_Options_WP->init();
class ACF_Theme_Options_WP {


	/**
	 * __construct
	 **/
	function __construct() {

	} // end function __construct


	/**
	 * init
	 **/
	function init() {

		add_action( 'init', [ $this, 'action__init' ], 11 );
		if ( is_admin() ) {
			// add_action( 'admin_menu', [ $this, 'remove_mene_page' ], 99 );
			add_filter( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
		}

	} // end function init


	/**
	 * action__init
	 **/
	function action__init() {

		$this->add_options_pages();

	} // end function action__init


	/**
	 * add_options_pages
	 **/
	function add_options_pages() {

		if ( function_exists('acf_add_options_sub_page') ) {

			acf_add_options_sub_page( array(
				'title' => 'Theme Options',
				'menu' => 'Theme Options',
				'slug' => 'theme-options',
				'parent' => 'themes.php',
				'capability' => 'manage_options'
			) );
			acf_add_options_sub_page( array(
				'title' => 'Options',
				'menu' => 'Options',
				'slug' => 'steps-options',
				'parent' => 'edit.php?post_type=steps',
				'capability' => 'manage_options'
			) );

		}

	} // end function add_options_pages


	/**
	 * remove_mene_page
	 **/
	function remove_mene_page() {

		if (
			! is__user('randy')
		) {
			remove_menu_page( 'edit.php?post_type=acf' );
			remove_menu_page( 'edit.php?post_type=acf-field-group' );
		}

	} // end function remove_mene_page


	/**
	 * pre_get_posts
	 **/
	function pre_get_posts( $query ) {

		if ( 'acf-field-group' == $_GET['post_type'] ) {
			$query->set( 'orderby', 'title' );
		}

		return $query;

	} // end function pre_get_posts


} // end class ACF_Theme_Options_WP
