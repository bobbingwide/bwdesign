<?php 
/**
Plugin Name: bwdesign
Depends: oik base plugin, oik fields, oik themes, oik-shortcodes
Plugin URI: https://www.bobbingwide.com/blog/oik_plugins/bwdesign
Description: Letter taxonomies for bobbingwidewebdesign.com	- pseudo grandchild theme
Version: 0.0.2
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Text Domain: bwdesign
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2017, 2018 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/


/**
 * Register the additional taxonomies for bwdesign
 *
 * - Depends on oik-a2z for the "letters" taxonomy. 
 * - oik-a2z automatically registers the filter that will set the taxonomy term from the title or content. 
 *
 */ 
function bwdesign_loaded() {
  add_action( 'oik_fields_loaded', 'bwdesign_oik_fields_loaded', 11 );
	//add_filter( "query_post_type_letter_taxonomy_filters", "oik_shortcode_a2z_query_post_type_letter_taxonomy_filters", 11 );
	add_action( "wp_enqueue_scripts", "bwdesign_enqueue_scripts", 12 );
	add_filter( 'genesis_footer_creds_text', "bwdesign_genesis_footer_creds_text", 11 );
	add_action( 'genesis_entry_footer', 'bwdesign_genesis_entry_footer', 11 );
	add_filter( "register_post_type_args", "bwdesign_register_post_type_args", 10, 2 );
}

/**
 * Implements 'oik_fields_loaded' for bobbinwidewebdesign.com
 *
 * * Registers the letters taxonomy for oik plugins, themes and shortcodes 
 * * Registers the _plugin_ref for posts and pages
 *
 * Associates it to the object types as required.
 * Note: This association is used to automatically set the 
 * filter hooks which automatically set the taxonomy terms for a post
 * from the title and/or content. 
 * 
 * We don't use the oik_letters taxonomy in bwdesign.
 * 
 */ 
function bwdesign_oik_fields_loaded() {
	register_taxonomy_for_object_type( "letters", "page" ); 
	bw_register_field_for_object_type( "letters", "page" );
	
	bw_register_custom_tags( "letters", "oik-plugins", "Letters" );
	bw_register_field_for_object_type( "letters", "oik-plugins" );
	
	register_taxonomy_for_object_type( "letters", "oik-themes" );
	bw_register_field_for_object_type( "letters", "oik-themes" );
	
	register_taxonomy_for_object_type( "letters", "oik_shortcodes" );
	bw_register_field_for_object_type( "letters", "oik_shortcodes" );
	
	bw_register_field( "_plugin_ref", "noderef", "Component", array( "#type" => array( "oik-plugins", "oik-themes" ), "#multiple" => 5, "#optional" => true ) );
	
	bw_register_field_for_object_type( "_plugin_ref", "post" );
	bw_register_field_for_object_type( "_plugin_ref", "page" );
}
	
function bwdesign_register_post_type_args( $args, $post_type ) {
	
	$post_types = array( "post", "page", "oik-plugins", "attachment", "oik-themes" );
	bw_trace2( $post_types, "post_types", false );
	$add_clone = in_array( $post_type, $post_types );
	if ( $add_clone ) {
		$args['supports'][] = 'clone';
	}
	bw_trace2( $add_clone, "add_clone", true );
	return( $args );

}

/**
 * Enqueues bwlink.css for styling of bobbing wide
 * 
 * Note: This is enqueued after oik-custom.css ( priority 12 )
 */
function bwdesign_enqueue_scripts() {
	wp_enqueue_style( 'bwlink-css', WP_PLUGIN_URL . '/oik-bob-bing-wide/bwlink.css', array() );
	$timestamp = null;
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$timestamp = filemtime( __DIR__ . '/css/bwdesign.css' );
	}
	wp_enqueue_style( 'bwdesign-css', oik_url( "css/bwdesign.css", "bwdesign" ), array(), $timestamp );
}

/**
 * Appends even more stuff to the footer credits.
 * 
 * @param string $text - the footer credits so far
 * @return string a few additions to brighten the day
 */
function bwdesign_genesis_footer_creds_text( $text ) { 
	$text .= "[div more][wp v p m][ediv]"; 
	$text .= "[div class=bwlogo][bw cp=h][ediv]";
	return( $text );
}

/**
 * Adds [bw_fields] for single posts only
 *
 * @TODO This could fail if oik is not loaded. 
 */
function bwdesign_genesis_entry_footer() {
	$post = get_post();
	if ( $post->post_type == "post" && is_single() ) {
		echo bw_do_shortcode( "[bw_fields]" );
	}
}

bwdesign_loaded();
