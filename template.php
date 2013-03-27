<?php

/**
 * Print the title for an IRDX item.
 * 
 * @action irdx_the_title
 *
 * @param string $irdx_code The IRDX code of the item required
 * @return string The title of the IRDX item
 * @author Simon Wheatley
 **/
function irdx_the_title( $irdx_code ) {
	echo get_irdx_the_title( $irdx_code );
}
add_action( 'irdx_the_title', 'irdx_the_title' );

/**
 * Return the title for an IRDX item.
 *
 * @param string $irdx_code The IRDX code of the item required
 * @return void (echoes)
 * @author Simon Wheatley
 **/
function get_irdx_the_title( $irdx_code ) {
	global $irdx_embed;
	if ( ! $irdx_item = $irdx_embed->get_irdx( $irdx_code ) )
		return '';
	if ( is_wp_error( $irdx_item ) )
		return '';
	return $irdx_item->get_title();
}

/**
 * Print the IMG element for the thumbnail for an IRDX item.
 * 
 * @action irdx_the_title
 *
 * @param string $irdx_code The IRDX code of the item required
 * @params array $args An array with optional 'alt' and 'class' indexes to be used for those attributes of the IMG element
 * @return string An IMG element
 * @author Simon Wheatley
 **/
function irdx_the_thumbnail( $irdx_code,  $args ) {
	echo get_irdx_the_thumbnail( $irdx_code, $args );
}
add_action( 'irdx_the_thumbnail', 'irdx_the_thumbnail', 10, 2 );

/**
 * Return the IMG element for the thumbnail for an IRDX item.
 *
 * @param string $irdx_code The IRDX code of the item required
 * @return void (echoes)
 * @author Simon Wheatley
 **/
function get_irdx_the_thumbnail( $irdx_code, $args ) {
	global $irdx_embed;
	if ( ! $irdx_item = $irdx_embed->get_irdx( $irdx_code ) )
		return '';
	if ( is_wp_error( $irdx_item ) ) 
		return '';
	return $irdx_item->get_thumbnail( $args );
}

/**
 * Print the description for an IRDX item.
 * 
 * @action irdx_the_description
 *
 * @param string $irdx_code The IRDX code of the item required
 * @return string The description of the IRDX item
 * @author Simon Wheatley
 **/
function irdx_the_description( $irdx_code ) {
	echo get_irdx_the_description( $irdx_code );
}
add_action( 'irdx_the_description', 'irdx_the_description' );

/**
 * Return the description for an IRDX item.
 *
 * @param string $irdx_code The IRDX code of the item required
 * @return void (echoes)
 * @author Simon Wheatley
 **/
function get_irdx_the_description( $irdx_code ) {
	global $irdx_embed;
	if ( ! $irdx_item = $irdx_embed->get_irdx( $irdx_code ) )
		return '';
	if ( is_wp_error( $irdx_item ) )
		return '';
	return $irdx_item->get_description();
}
