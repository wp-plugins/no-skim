<?php
/*
Plugin Name: No Skim
Description: Suppress Skimlinks
Version: 1.0
Author: David Artiss
Author URI: http://www.artiss.co.uk
*/

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
		$links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/no-skim">' . __( 'Support' ) . '</a>' ) );
		$links = array_merge( $links, array( '<a href="http://www.artiss.co.uk/donate">' . __( 'Donate' ) . '</a>' ) );
	}

	return $links;
}
add_filter( 'plugin_row_meta', 'noskim_set_plugin_meta', 10, 2 );

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
    
	return '<div class="noskim">'. do_shortcode( $content )  . '</div>';
	
}

add_shortcode( 'noskim', 'noskim_shortcode' );
?>