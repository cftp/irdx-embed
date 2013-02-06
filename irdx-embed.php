<?php
/*
Plugin Name: IRDX Embed (Internet Retailing Directory)
Plugin URI:  https://github.com/cftp/irdx-embed
Description: Embed IRDX links into your WordPress site
Version:     1.0
Author:      Code for the People
Author URI:  http://codeforthepeople.com/

Copyright © 2013 Code for the People Ltd

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

defined( 'ABSPATH' ) or die();

if ( !defined( 'IRDX_URL' ) )
	define( 'IRDX_URL', 'http://internetretailing.net/irdx-json/%s/' );

require_once dirname( __FILE__ ) . '/class.plugin.php';

class IRDX_Embed extends IRDX_Embed_Plugin {

	/**
	 * The IRDX boxes to render at the 
	 * end of the content.
	 * 
	 * @var array
	 **/
	protected $boxes = array();

	/**
	 * The IRDX object cache.
	 * 
	 * @var array
	 **/
	protected $irdxs = array();

	/**
	 * Class constructor
	 *
	 * @return null
	 */
	function __construct() {

		# Actions:
		add_action( 'init',        array( $this, 'init' ) );
		add_action( 'save_post',   array( $this, 'save_post' ), 10, 2 );

		# Filters:
		add_filter( 'the_content', array( $this, 'the_content' ), 200 );

		# Set up the plugin from the parent class:
		parent::__construct( __FILE__ );

	}

	/**
	 * Register our shortcodes.
	 *
	 * @return void
	 * @author John Blackbourn
	 **/
	function init() {

		add_shortcode( 'irdx', array( $this, 'shortcode' ) );
		add_shortcode( 'IRDX', array( $this, 'shortcode' ) );

	}

	/**
	 * Insert IRDX mentions into the post content
	 *
	 * @param  string $the_content The HTML content for a post
	 * @return string The HTML content for a post
	 * @author Simon Wheatley / John Blackbourn
	 **/
	function the_content( $the_content ) {

		global $post;

		if ( isset( $this->boxes[$post->ID] ) and is_singular() ) {
			$the_content .= sprintf( '<div class="irdx-boxes"><h2 class="irdx-boxes-title">%s</h2>', __( 'Mentioned in this piece&hellip;', 'irdx-embed' ) );
			foreach ( $this->boxes[$post->ID] as $count => $irdx )
				$the_content .= $this->irdx_box( $irdx, $count, get_post_type() );
			// Reset the boxes
			unset( $this->boxes[$post->ID] );
			$the_content .= "</div>";
		}
		return $the_content;
	}

	/**
	 * [irdx] shortcode handler
	 *
	 * @param array $atts Shortcode attributes
	 * @return string The shortcode markup
	 * @author John Blackbourn / Simon Wheatley
	 */
	function shortcode( $atts ) {

		global $post;
		
		if ( !isset( $atts[0] ) )
			return '';

		if ( ! is_array( $this->boxes ) )
			$this->boxes = array();
		if ( ! isset( $this->boxes[$post->ID] ) )
			$this->boxes[$post->ID] = array();

		$code = strtoupper( trim( $atts[0] ) );
		$irdx = $this->get_irdx( $code );
		
		if ( is_wp_error( $irdx ) ) {
			if ( current_user_can( 'edit_post', get_the_ID() ) )
				return '<span><strong style="color:#c00">' . $irdx->get_error_message() . '</strong></span>';
			else
				return '??';
		}
		
		$this->boxes[$post->ID][] = $irdx;

		return sprintf( '<span class="irdx-code irdx-%s">[<a href="%s">IRDX %s</a>]</span>',
			esc_attr( strtolower( $irdx->get_code() ) ),
			$irdx->get_permalink(),
			esc_html( strtoupper( $code ) )
		);

	}

	/**
	 * Get an IRDX_Item object by its IRDX code
	 *
	 * @param  string $code An IRDX code
	 * @return IRDX_Item|WP_Error An IRDX_Item object, or a WP_Error object on failure
	 * @author John Blackbourn
	 */
	function get_irdx( $code ) {

		$code = strtolower( $code );

		if ( isset( $this->irdxs[$code] ) )
			return $this->irdxs[$code];

		$cache_key = sprintf( 'irdx-%s', $code );
		$irdx      = get_site_transient( $cache_key ) ;

		if ( $irdx ) {
			if ( $irdx->valid )
				return new IRDX_Item( $irdx );
			else
				return new WP_Error( 'invalid_code', __( 'Invalid IRDX code', 'irdx_embed' ) );
		}

		$response = $this->fetch_irdx( $code );

		if ( is_wp_error( $response ) )
			return $response;

		if ( $response->valid ) {

			# Cache valid responses for 24 hours
			set_site_transient( $cache_key, $response, 60*60*24 );
			$irdx = new IRDX_item( $response );

		} else {

			# Cache invalid responses for 10 mins
			set_site_transient( $cache_key, $response, 60*10 );
			$irdx = new WP_Error( 'invalid_code', __( 'Invalid IRDX code', 'irdx_embed' ) );

		}

		$this->irdxs[$code] = $irdx;

		return $irdx;

	}

	function fetch_irdx( $code ) {

		$url = esc_url_raw( sprintf( IRDX_URL, strtolower( $code ) ) );

		$request = wp_remote_get( $url, array(

		) );

		if ( is_wp_error( $request ) )
			return $request;

		if ( 200 != wp_remote_retrieve_response_code( $request ) )
			return new WP_Error( 'http_error', __( 'Unable to connect to IRDX server', 'irdx_embed' ) );

		$json = trim( wp_remote_retrieve_body( $request ) );

		if ( empty( $json ) or !is_object( $response = json_decode( $json ) ) )
			return new WP_Error( 'invalid_response', __( 'Invalid response from IRDX server', 'irdx_embed' ) );

		return $response;

	}

	/**
	 * Return the markup for a given IRDX box
	 *
	 * @param IRDX_Item  $irdx The IRDX_Item object
	 * @param int $count The number of the box, i.e. the third box for this post 
	 * @return string The box markup
	 * @author Simon Wheatley
	 */
	function irdx_box( IRDX_Item $irdx, $count, $post_type ) {

		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

		$template_names = array(
			sprintf( 'mentioned-%s.php', $post_type ),
			'mentioned.php'
		);

		ob_start();

		if ( $template = locate_template( $template_names ) )
			include( $template );
		else
			include( $this->plugin_path( 'mentioned.php' ) );

		return ob_get_clean();

	}

	/**
	 * Formats text using the same formatting used for post content. Handy for use within the 'post_content' filter where using
	 * functions such as get_the_content() or get_the_excerpt() will cause stack overflows or infinite recursion.
	 *
	 * @see get_the_content()
	 * @param string $text The text to format
	 * @return string A nicely formatted piece of text
	 * @author John Blackbourn
	 **/
	function format_content( $text ) {

		$text = wptexturize( $text );
		$text = convert_chars( $text );
		$text = wpautop( $text );
		return $text;

	}

}

class IRDX_Item {

	function __construct( $data ) {
		foreach ( $data as $k => $v )
			$this->$k = $v;
	}

	function get_permalink() {
		return esc_url_raw( $this->permalink );
	}

	function get_title() {
		return $this->title;
	}

	function get_thumbnail() {
		if ( isset( $this->thumbnail ) )
			return sprintf( '<img src="%s" alt="" />', esc_url_raw( $this->thumbnail ) );
		else
			return false;
	}

	function get_description() {
		return $this->description;
	}

	function get_code() {
		return $this->code;
	}

}

global $irdx_embed;

$irdx_embed = new IRDX_Embed;
