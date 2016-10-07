<?php
/**
 * @package WordPress
 * @subpackage ThemeWP
 *
 **/
#################################################################################################### */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! defined('ADDONS_INIT') ) {

	require_once('ACFWP.php');
	require_once('ACF_Theme_Options_WP.php');

	require_once('Post_Type_Steps_WP.php');
	$Post_Type_Steps_WP = new Post_Type_Steps_WP();
	$Post_Type_Steps_WP->init();

	define( 'ADDONS_INIT', true );

} // end if ( ! defined('ADDONS_INIT') )
