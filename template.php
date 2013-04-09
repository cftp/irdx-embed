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
 * @param array $args An array with optional 'alt' and 'class' indexes to be used for those attributes of the IMG element
 * @return string An IMG element
 * @author Simon Wheatley
 **/
function irdx_the_thumbnail( $irdx_code, $args = array() ) {
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
function get_irdx_the_thumbnail( $irdx_code, $args = array() ) {
	global $irdx_embed;
	if ( ! $irdx_item = $irdx_embed->get_irdx( $irdx_code ) )
		return '';
	if ( is_wp_error( $irdx_item ) ) 
		return '';
	return $irdx_item->get_thumbnail( $args );
}

/**
 * Print the IMG element for a specified image size for an IRDX item.
 * 
 * @action irdx_the_title
 *
 * @param string $irdx_code The IRDX code of the item required
 * @param string $size The name of the image size required
 * @param array $args An array with optional 'alt' and 'class' indexes to be used for those attributes of the IMG element
 * @return string An IMG element
 * @author John Blackbourn
 **/
function irdx_the_image( $irdx_code, $size, $args = array() ) {
	echo get_irdx_the_image( $irdx_code, $size, $args );
}
add_action( 'irdx_the_image', 'irdx_the_image', 10, 3 );

/**
 * Return the IMG element for a specified image size for an IRDX item.
 *
 * @param string $irdx_code The IRDX code of the item required
 * @param string $size The name of the image size required
 * @return void (echoes)
 * @author John Blackbourn
 **/
function get_irdx_the_image( $irdx_code, $size, $args = array() ) {
	global $irdx_embed;
	if ( ! $irdx_item = $irdx_embed->get_irdx( $irdx_code ) )
		return '';
	if ( is_wp_error( $irdx_item ) ) 
		return '';
	return $irdx_item->get_image( $size, $args );
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
