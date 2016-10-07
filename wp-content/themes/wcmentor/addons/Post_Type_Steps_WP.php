<?php
/**
 * @package WordPress
 * @subpackage ParentTheme_VC
 * S@FEIsOrnh)J6p!ZRNCe3^ID
 **/
####################################################################################################


/**
	 * Post_Type_Steps_WP
 **/
class Post_Type_Steps_WP {

	/**
	 * post_type
	 *
	 * @access public
	 * @var string
	 **/
	var $post_type = 'steps';

	/**
	 * slug
	 *
	 * @access public
	 * @var string
	 **/
	var $slug = 'steps';

	/**
	 * name
	 *
	 * @access public
	 * @var string
	 **/
	var $name = 'Steps';

	/**
	 * __construct
	 **/
	function __construct() {
	}


	/**
	 * init
	 **/
	function init() {

		// hook method init
		add_action( 'init', [ $this, 'action__init' ] );

		// hook method admin_init
		add_action( 'admin_init', [ $this, 'action__admin_init' ] );

	} // end function init


	/**
	 * init
	 **/
	function action__init() {

		if ( function_exists('get_field') ) {

			// only if steps is active in the installation
			if ( get_field('add_the_steps_post-type','option') ) {

				$this->register_post_type();
				apply_filters( 'comment_notification_recipients', [ $this, 'filter__comment_notification_recipients' ], 10, 2 );

				// add_action( 'the_post', [ $this, 'the_post' ] );
				add_filter( 'pre_get_posts', [ $this, 'pre_get_posts' ] );

				// turn off notifications for this post type
				add_filter( 'notify_moderator', [ $this, 'return_false_on_steps_post_type' ], 10, 2 );
				add_filter( 'notify_post_author', [ $this, 'return_false_on_steps_post_type' ], 10, 2 );

				// $comment_ID
				add_action( 'comment_post', [ $this, 'new_comment_notify' ], 10, 3 );

				if ( function_exists('scporder_uninstall') ) {
					add_filter( 'the_title', [ $this, 'prepend_index_count'], 10, 2 );
				}

				add_filter( 'the_content', [ $this, 'append_content'] );
				add_filter( 'post_class', [ $this, 'post_class'] );

			}

		}

	} // end function init


	/**
	 * admin_init
	 **/
	function action__admin_init() {

		add_filter( "manage_edit-{$this->post_type}_columns", [ $this, 'edit_columns' ] );
		add_action( "manage_pages_custom_column", [ $this, 'custom_columns' ] );
		// add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', [ $this, 'column_register_sortable' ] );
		// add_filter( 'request', [ $this, 'column_orderby' ] );

	} // end function admin_init


	/**
	 * set
	 **/
	function set( $key, $val = false ) {

		if ( isset( $key ) AND ! empty( $key ) ) {
			$this->$key = $val;
		}

	} // end function set


	####################################################################################################
	/**
	 * Register
	 **/
	####################################################################################################


	/**
	 * register_post_type
	 **/
	function register_post_type() {

		register_post_type( $this->post_type, apply_filters( "register_post_type-$this->post_type", [
			'labels' => [
				'name' => __( $this->name, 'childtheme' ),
				'singular_name' => __( $this->name, 'childtheme' ),
				'add_new' => __( 'Add New', 'childtheme' ),
				'add_new_item' => __( 'Add New', 'childtheme' ),
				'edit_item' => __( "Edit $this->name", 'childtheme' ),
				'new_item' => __( "New $this->name", 'childtheme' ),
				'view_item' => __( "View $this->name", 'childtheme' ),
				'search_items' => __( "Search $this->name", 'childtheme' ),
				'not_found' => __( "No $this->name found", 'childtheme' ),
				'not_found_in_trash' => __( "No $this->name found in Trash", 'childtheme' ),
				'parent_item_colon' => '',
				'menu_name' => __( $this->name, 'childtheme' )
			],

			// 'description' => '',
			'public' => true,
			// 'publicly_queryable'	=> true,
			// 'exclude_from_search'	=> false,
			'show_ui' => true,
			'show_in_menu' => true, // edit.php?post_type=page
			// 'menu_position' => null,
			// 'menu_icon' => get_stylesheet_directory_uri() . "/addons/PostTypes/images/" . $this->post_type . "-16x16.png", // is set in class construct
			'capability_type' => 'post', // requires 'page' to call in post_parent
			// 'capabilities' => array(), --> See codex for detailed description
			// 'map_meta_cap' => false,
			'hierarchical' => true, // requires manage_pages_custom_column for custom_columns add_action // requires 'true' to call in post_parent

			'supports' => [
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'trackbacks',
				'custom-fields',
				'comments',
				'revisions',
				'page-attributes', //  (menu order, hierarchical must be true to show Parent option)
				'post-formats',
			],

			// 'register_meta_box_cb' => '', --> managed via class method add_meta_boxes()
			// 'taxonomies' => array('post_tag', $this->post_type . '-tax-hierarchal'), // array of registered taxonomies
			// 'permalink_epmask' => 'EP_PERMALINK',
			'has_archive' => true, // Enables post type archives. Will use string as archive slug.

			'rewrite' => [ // Permalinks
				'slug' => $this->slug,
				// 'with_front' => '', // set this to false to over-write a wp-admin-permalink structure
				// 'feeds' => '', // default to has_archive value
				// 'pages' => true,
			],

			'query_var' => $this->post_type, // This goes to the WP_Query schema
			'can_export' => true,
			// 'show_in_nav_menus' => '', // value of public argument
			'_builtin' => false,
			'_edit_link' => 'post.php?post=%d',

		] )  );

	} // end function register_post_type


	/**
	 * Mail Content Type
	 **/
	function mail_content_type() {

		return "text/html";

	} // end function mail_content_type


	####################################################################################################
	/**
	 * Functionality
	 **/
	####################################################################################################


	/**
	 * get_notification_recipient_emails
	 **/
	function get_notification_recipient_emails( $post_id ) {
		// $log = new Monolog_WP();

		if ( ! get_field('emails_to_include_in_notifications','option') ) {
			// $log->logger->addInfo( '! emails_to_include_in_notifications' );
			return [];
		}

		$additional_emails = [];
		if ( get_field( 'additional_emails_to_send_to', $post_id ) ) {
			$additional_emails = get_field( 'additional_emails_to_send_to', $post_id );
			$additional_emails = explode( ',', $additional_emails );
			foreach ( $additional_emails as $k => $email ) {
				$additional_emails[$k] = trim($email);
			}
			// $log->logger->addInfo( 'additional_emails_to_send_to', [$additional_emails] );
		}

		$emails = [];
		if ( get_field( 'activate_group_notifications', $post_id ) ) {
			$emails = get_field( 'emails_to_include_in_notifications', 'option' );
			$emails = explode( ',', $emails );
			$emails = array_filter( $emails );
			foreach ( $emails as $k => $email ) {
				$emails[$k] = trim($email);
			}
		}
		// $log->logger->addInfo( 'emails_to_include_in_notifications', [$emails] );

		$emails = array_merge( $emails, $additional_emails );

		// $log->logger->addInfo( '$emails', [$emails] );

		return $emails;

	} // end function get_notification_recipient_emails


	/**
	 * return_false_on_steps_post_type
	 **/
	function return_false_on_steps_post_type( $maybe_notify, $comment_id ) {

		$comment = get_comment( $comment_id );
		$post = get_post( $comment->comment_post_ID );

		if ( $post->post_type == $this->post_type ) {
			return $maybe_notify;
		}

		return $maybe_notify;

	} // end function return_false_on_steps_post_type


	/**
	 * new_comment_notify
	 **/
	function new_comment_notify( $comment_ID, $comment_approved, $commentdata ) {

		if ( $comment_approved ) {
			$comment = get_comment( $comment_ID );
			$post = get_post( $comment->comment_post_ID );
			if ( $post->post_type == $this->post_type ) {

				if ( ! get_field( 'activate_notifications', $post->ID ) ) {
					return;
				}

				add_filter( 'wp_mail_content_type', [ $this, 'mail_content_type' ] );

				// $log = new Monolog_WP();

				$subject = 'Comment: ' . $post->post_title;
				$to_emails = $this->get_notification_recipient_emails( $post->ID );
				$message = $this->get_html_message( $this->get_message_text( $commentdata, $post ), $subject );
				$from_email = get_option('admin_email');
				$headers = "From: " . stripslashes( $from_email ) . " <$from_email>\r\n ";

				// $log->logger->addInfo( $subject, [$to_emails, $message, $headers]);

				// send a individual emails
				foreach ( $to_emails as $to_email ) {
					wp_mail( $to_email, $subject, $message, $headers );
				}

			}
		}

	} // end function new_comment_notify


	/**
	 * get_message_text
	 **/
	function get_message_text( $commentdata, $post ) {

		$output = get_field('notification_email_template','option');
		$output = str_replace( '[comment_author]', $commentdata['comment_author'], $output );
		$output = str_replace( '[comment_author_email]', $commentdata['comment_author_email'], $output );
		$output = str_replace( '[comment_content]', $commentdata['comment_content'], $output );

		$output = str_replace( '[comment_url]', home_url() . "/wp-admin/post.php?post={$post->ID}&action=edit", $output );

		return $output;

	} // end function get_message_text


	/**
	 * the_post
	 **/
	function the_post( $post ) {

		if ( $post->post_type == $this->post_type ) {
			$post = self::thePost($post);
		}

		return $post;

	} // end function the_post


	/**
	 * thePost
	 **/
	static function thePost( $post ) {

		$post->permalink = get_permalink( $post->ID );

		return $post;

	} // end function thePost


	/**
	 * pre_get_posts
	 **/
	function pre_get_posts( $wp_query ) {

		if (
			(
				$wp_query->is_main_query()
				AND $wp_query->get('post_type') == $this->post_type
			) OR (
				$wp_query->get('post_type') == $this->post_type
			)
		) {
			$wp_query->set( 'orderby', 'menu_order' );
			$wp_query->set( 'order', 'ASC' );
			$wp_query->set( 'posts_per_page', -1 );
		}

	} // end function pre_get_posts


	/**
	 * HTML Frame
	 *
	 * @version 1.0
	 * @updated 02.09.13
	 *
	 * Note: html was pulled directly from mailchimp template as
	 * a secure starting point for an html frame.
	 **/
	function get_html_message( $message, $subject ) {

		$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$html .= "<html>";
			$html .= "<head>";
				$html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
				$html .= "<title>$subject</title>";

			$html .= "</head>";
			$html .= "<body leftmargin=\"0\" marginwidth=\"0\" topmargin=\"0\" marginheight=\"0\" offset=\"0\" style=\"font-family:Arial; font-size:12px; line-height:18px; color:#333; -webkit-text-size-adjust: none;margin: 0;padding: 0;display:block;width: auto !important; $this->html_body_css_style\">";

				$html .= $message;

			$html .= "</body>";
		$html .= "</html>";

		return $html;

	} // end function set_html_message


	/**
	 * post_class
	 **/
	function post_class( $classes ) {
		global $post;
		if ( $post->post_type == $this->post_type ) {
			$classes[] = 'step-status-' . get_field( 'status', $post->ID );
		}
		return $classes;
	}


	/**
	 * append_content
	 **/
	function append_content( $content ) {
		global $post;

		if ( $post->post_type == $this->post_type ) {
			$content .= '<div class="item-status">' . get_field( 'status', $post->ID ) . '</div>';
			$content .= '<p><strong>Status:</strong> ' . get_field( 'status', $post->ID ) . '</p>';
			$content .= '<strong>Status notes:</strong>';
			if ( get_field( 'status_note', $post->ID ) ) {
				$content .= get_field( 'status_note', $post->ID );
			} else {
				$content .= wpautop('No Notes');
			}
			$content .= '<div class="items-to-discuss"><strong>Items to discus:</strong>' . get_field( 'general_items_to_discuss', $post->ID ) . '</div>';
		}

		return $content;

	} // end function append_content


	/**
	 * prepend_index_count
	 **/
	function prepend_index_count( $title, $id ) {
		global $wp_query, $post;

		if (
			$post->ID == $id
			AND $post->post_type == $this->post_type
		) {
			$index = $wp_query->current_post + 1;
			$title = 'Step ' . $index . ': ' . $title;
		}

		return $title;

	} // end function prepend_index_count


	####################################################################################################
	/**
	 * Admin Management
	 **/
	####################################################################################################


	/**
	 * column_register_sortable
	 **/
	function column_register_sortable( $columns ) {

		// $columns['featured'] = 'featured';

		return $columns;

	} // end function column_register_sortable


	/**
	 * column_orderby
	 **/
	function column_orderby( $vars ) {

		// Sorting by post_meta numeric values
		if ( isset( $vars['orderby'] ) AND $vars['post_type'] == $this->post_type ) {

			$vars['meta_compare'] = '>';
			$vars['meta_value'] = 0;

			switch ( $vars['orderby'] ) {
				case "featured" :
					$vars['meta_key'] = '_books__featured_order';
					break;
			}

			$vars['orderby'] = 'meta_value_num';

		} // end if ( isset( $vars['orderby'] ) )

		return $vars;

	} // end function column_orderby


	/**
	 * edit_columns
	 **/
	function edit_columns( $columns ) {

		$columns['status'] = 'Status';
		$columns['status_note'] = 'Status Note';

		unset( $columns['author'] );
		unset( $columns['date'] );

		return $columns;

	} // end edit_columns


	/**
	 * custom_columns
	 **/
	function custom_columns( $column ) {
		global $post;

		if ( $post->post_type == $this->post_type ) {

			switch ( $column ) {

				case "status_note" :
					the_field( 'status_note', $post->ID );
					break;
				case "status":
					the_field( 'status', $post->ID );
					break;

			} // end switch

		} // end if

	} // end custom_columns


} // end class Post_Type_Steps_WP
