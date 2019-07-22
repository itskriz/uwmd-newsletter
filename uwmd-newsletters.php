<?php

/**
 * Plugin Name: UWMD Monthly Newsletters
 * Plugin URI: https://github.com/itskriz/uwmd-newsletters
 * Description: A plugin that adds a monthly newsletter archive to the website
 * Version: 1.0
 * Author: Kris Williams / Roar Media
 * Author URI: http://https://github.com/itskriz/
 */

// Register Custom Post Type
function register_uwmd_newsletter() {

	$labels = array(
		'name'                  => _x( 'Newsletter', 'Post Type General Name', 'uwmd' ),
		'singular_name'         => _x( 'Newsletter', 'Post Type Singular Name', 'uwmd' ),
		'menu_name'             => __( 'Monthly Newsletters', 'uwmd' ),
		'name_admin_bar'        => __( 'Monthly Newsletters', 'uwmd' ),
		'archives'              => __( 'Newsletter Archives', 'uwmd' ),
		'attributes'            => __( 'Newsletter Attributes', 'uwmd' ),
		'parent_item_colon'     => __( 'Parent Newsletter:', 'uwmd' ),
		'all_items'             => __( 'All Newsletters', 'uwmd' ),
		'add_new_item'          => __( 'Add New Newsletter', 'uwmd' ),
		'add_new'               => __( 'Add New', 'uwmd' ),
		'new_item'              => __( 'New Newsletter', 'uwmd' ),
		'edit_item'             => __( 'Edit Newsletter', 'uwmd' ),
		'update_item'           => __( 'Update Newsletter', 'uwmd' ),
		'view_item'             => __( 'View Newsletter', 'uwmd' ),
		'view_items'            => __( 'View Newsletter', 'uwmd' ),
		'search_items'          => __( 'Search Newsletter', 'uwmd' ),
		'not_found'             => __( 'Not found', 'uwmd' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'uwmd' ),
		'featured_image'        => __( 'Featured Image', 'uwmd' ),
		'set_featured_image'    => __( 'Set featured image', 'uwmd' ),
		'remove_featured_image' => __( 'Remove featured image', 'uwmd' ),
		'use_featured_image'    => __( 'Use as featured image', 'uwmd' ),
		'insert_into_item'      => __( 'Insert into newsletter', 'uwmd' ),
		'uploaded_to_this_item' => __( 'Uploaded to this newsletter', 'uwmd' ),
		'items_list'            => __( 'Newsletters list', 'uwmd' ),
		'items_list_navigation' => __( 'Newsletters list navigation', 'uwmd' ),
		'filter_items_list'     => __( 'Filter newsletters list', 'uwmd' ),
	);
	$rewrite = array(
		'slug'                  => 'monthly-newsletter',
		'with_front'            => false,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Newsletter', 'uwmd' ),
		'description'           => __( 'A post type to contain newsletter content', 'uwmd' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 10,
		'menu_icon'             => 'dashicons-email-alt',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'page',
	);
	register_post_type( 'uwmd-newsletter', $args );

}
add_action( 'init', 'register_uwmd_newsletter', 0 );

// Register Metaboxes
function uwmd_newsletter_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function uwmd_newsletter_add_meta_box() {
	add_meta_box(
		'uwmd_newsletter-uwmd-newsletter',
		__( 'UWMD Newsletter', 'uwmd_newsletter' ),
		'uwmd_newsletter_html',
		'uwmd-newsletter',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'uwmd_newsletter_add_meta_box' );

function uwmd_newsletter_html( $post) {
	wp_nonce_field( '_uwmd_newsletter_nonce', 'uwmd_newsletter_nonce' ); ?>

	<p>
		<label for="uwmd_newsletter_email_html"><?php _e( 'Email HTML', 'uwmd_newsletter' ); ?></label><br>
		<textarea name="uwmd_newsletter_email_html" id="uwmd_newsletter_email_html" rows="40" width="100%" style="width: 100%;"><?php echo uwmd_newsletter_get_meta( 'uwmd_newsletter_email_html' ); ?></textarea>
	
	</p><?php
}

function uwmd_newsletter_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['uwmd_newsletter_nonce'] ) || ! wp_verify_nonce( $_POST['uwmd_newsletter_nonce'], '_uwmd_newsletter_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['uwmd_newsletter_email_html'] ) )
		update_post_meta( $post_id, 'uwmd_newsletter_email_html', wp_kses_post( $_POST['uwmd_newsletter_email_html'] ) );
}
add_action( 'save_post', 'uwmd_newsletter_save' );

/*
	Usage: uwmd_newsletter_get_meta( 'uwmd_newsletter_email_html' )
*/

// Set Template
function uwmd_newsletter_single_template($single) {
	global $post;
	if ('uwmd-newsletter' == $post->post_type) {
		if (file_exists( plugin_dir_path(__FILE__) . '/includes/single-uwmd_newsletter.php' )) {
			return plugin_dir_path(__FILE__) . '/includes/single-uwmd_newsletter.php';
		}
	}
	return $single;
}
add_filter('single_template', 'uwmd_newsletter_single_template');

// Monthly Newsletter Template
function uwmd_add_monthly_template( $page_templates ) {
	$page_templates['template-uwmd_monthly-newsletter.php'] = __('Monthly Newsletter');
	return $page_templates;
}
add_filter( 'theme_page_templates', 'uwmd_add_monthly_template', 10, 4 );

function uwmd_load_monthly_template( $template ) {
	if (get_page_template_slug() == 'template-uwmd_monthly-newsletter.php' ) {
		if (file_exists( plugin_dir_path(__FILE__) . '/includes/template-uwmd_monthly-newsletter.php' )) {
			$template = plugin_dir_path(__FILE__) . '/includes/template-uwmd_monthly-newsletter.php';
		}
	}
	return $template;
}
add_filter( 'template_include', 'uwmd_load_monthly_template' );

// Deregister Scripts and CSS
function uwmd_remove_default_scripts() {
	if (is_singular('uwmd-newsletter') || is_page_template('template-uwmd_monthly-newsletter.php')) {
		global $wp_styles;
		foreach ($wp_styles->registered as $handle => $data) {
			wp_dequeue_style($handle);
			wp_deregister_style($handle);
		}
		global $wp_scripts;
		foreach ($wp_scripts->registered as $handle => $data) {
			wp_dequeue_style($handle);
			wp_deregister_style($handle);
		}
	}
}
add_action('wp_enqueue_scripts', 'uwmd_remove_default_scripts', 999);

function uwmd_newsletters($numberposts = -1, $order = 'DESC', $orderby = 'date') {
	return get_posts(array(
		'post_type'		=> 'uwmd-newsletter',
		'numberposts' => $numberposts,
		'order'				=> $order,
		'orderby'			=> $orderby
	));
}

function uwmd_newsletters_shortcode($atts) {
	$a = shortcode_atts(array(
		'numberposts'	=> -1,
		'order'				=> 'DESC',
		'orderby'			=> 'data'
	), $atts);
	$get_newsletters = uwmd_newsletters($a['numberposts'], $a['order'], $a['orderby']);
	$newsletters = array();
	foreach ($get_newsletters as $item) {
		$newsletter = sprintf(
			'<li class="uwmd-newsletters__list-item"><h3 class="uwmd-newsletters__title"><a class="uwmd-newsletters__link" href="%2$s" target="_blank" rel="noopener noreferrer">%1$s (%3$s)</a></h3></li>',
			$item->post_title,
			get_the_permalink($item->ID),
			date('F Y', strtotime($item->post_date))
		);
		array_push($newsletters, $newsletter);
	}
	$newsletters_output = implode('', $newsletters);
	return sprintf(
		'<div class="uwmd-newsletters"><ul class="uwmd-newsletters_list">%1$s</ul></div>',
		$newsletters_output
	);
}
add_shortcode('uwmd-newsletters', 'uwmd_newsletters_shortcode');

?>