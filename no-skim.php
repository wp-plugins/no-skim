<?php
/*
Plugin Name: No Skim
Plugin URI: https://wordpress.org/plugins/no-skim/
Description: Suppress Skimlinks
Version: 1.1.1
Author: David Artiss
Author URI: http://www.artiss.co.uk
Text Domain: no-skim
Domain Path: /languages
*/

/**
* Plugin initialisation
*
* Loads the plugin's translated strings
*
* @since	1.1
*/

function noskim_plugin_init() {

	$language_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages/';

	load_plugin_textdomain( 'no-skim', false, $language_dir );

}

add_action( 'init', 'noskim_plugin_init' );

/**
* Add meta to plugin details
*
* Add options to plugin meta line
*
* @since	1.0
*
* @param	string  $links	Current links
* @param	string  $file	File in use
* @return   	string		Links, now with settings added
*/

function noskim_set_plugin_meta( $links, $file ) {

	if ( strpos( $file, 'no-skim.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/no-skim">' . __( 'Support', 'noskim' ) . '</a>' ) );
		$links = array_merge( $links, array( '<a href="http://www.artiss.co.uk/donate">' . __( 'Donate', 'noskim' ) . '</a>' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'noskim_set_plugin_meta', 10, 2 );

/**
* Add a custom meta box
*
* Action to define a new meta box for post and page editor
*
* @since	1.1
*/

function noskim_add_custom_box() {

	$box_title = __( 'Noskim', 'noskim' );

	add_meta_box( 'noskim_metaid', __( $box_title ), 'noskim_custom_box', 'post' );

	add_meta_box( 'noskim_metaid', __( $box_title ), 'noskim_custom_box', 'page' );

}

add_action( 'admin_init', 'noskim_add_custom_box', 1 );

/**
* Display custom meta box
*
* Display the custom meta box in the editor
*
* @since	1.1
*
* @param	$post	   string	Post details
*/

function noskim_custom_box( $post ) {

	// Use nonce for verification

	wp_nonce_field( plugin_basename( __FILE__ ), 'noskim_noncename' );

	// Now request the information

	echo '<label for="noskim">' . __( 'Suppress Skimlinks?', 'noskim' ) . '&nbsp;</label> ';
	echo '<input type="checkbox" id="noskim" name="noskim" value="Yes"';
	if ( strtolower( get_post_meta( $post->ID, 'noskim', true ) ) == 'yes' ) { echo ' checked="checked"'; }
	echo ' />';
}

/**
* Save meta data
*
* Save the data entered into the meta box
*
* @since	1.1
*
* @param	$post_id   string	Post ID
*/

function noskim_save_postdata( $post_id ) {

	// If this is an auto save routine and the form has not been submitted then
	// don't do anything

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }

	// Verify this came from the correct meta box and with proper authorization

	if ( isset( $_POST[ 'noskim_noncename' ] ) ) {
		if ( !wp_verify_nonce( $_POST[ 'noskim_noncename' ], plugin_basename( __FILE__ ) ) ) { return; }
	}

	// Check permissions

	if ( isset( $_POST[ 'post_hide' ] ) ) {
		if ( $_POST[ 'post_type' ] == 'page' ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) { return; }
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) ) { return; }
		}
	}

	// Save the data

	if ( isset( $_POST[ 'noskim' ] ) ) {
		$data = $_POST[ 'noskim' ];
	} else {
		$data = '';
	}
	update_post_meta( $post_id, 'noskim', $data );

}

add_action( 'save_post', 'noskim_save_postdata' );

/**
* Noskim shortcode
*
* Shortcode function to suppress Skimlink output
*
* @since	1.0
*
* @param	string	$paras		Shortcode parameters (ignored)
* @param	string	$content	Content to be suppressed
* @return	string				Output code
*/

function noskim_shortcode( $paras = '', $content = '' ) {

	return generate_noskim_code( do_shortcode( $content ) );

}

add_shortcode( 'noskim', 'noskim_shortcode' );

/**
* Add noskim to the post/page content
*
* A filter to add noskim to the page, if the meta box has been ticked
*
* @since	1.1
*/

function add_noskim_to_content( $content ) {

	global $post;

	if ( strtolower( get_post_meta( $post->ID, 'noskim', true ) ) == 'yes' ) {

		return generate_noskim_code( $content );

	} else {

		return $content;
	}

}

add_filter( 'the_content', 'add_noskim_to_content' );

/**
* Generate the 'no skim' code
*
* Quick function to add shortcode suppression to some output
*
* @since	1.1
*
* @param	string	$text		Text to add Skimlink suppress to
* @return	string				Output code
*/

function generate_noskim_code( $text ) {

	return '<div class="noskim">'. $text . '</div>';

}
?>