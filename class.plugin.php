<?php

/**
 * A simple base plugin class by John Blackbourn. Very much still under development.
 **/

class IRDX_Embed_Plugin {

	/**
	 * Class constructor
	 *
	 * @author John Blackbourn
	 **/
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Returns the URL for for a file/dir within this plugin.
	 *
	 * @param $path string The path within this plugin, e.g. '/js/clever-fx.js'
	 * @return string URL
	 * @author John Blackbourn
	 **/
	protected function plugin_url( $file = '' ) {
		return $this->plugin( 'url', $file );
	}

	/**
	 * Returns the filesystem path for a file/dir within this plugin.
	 *
	 * @param $path string The path within this plugin, e.g. '/js/clever-fx.js'
	 * @return string Filesystem path
	 * @author John Blackbourn
	 **/
	protected function plugin_path( $file = '' ) {
		return $this->plugin( 'path', $file );
	}

	/**
	 * Returns a version number for the given plugin file.
	 *
	 * @param $path string The path within this plugin, e.g. '/js/clever-fx.js'
	 * @return string Version
	 * @author John Blackbourn
	 **/
	protected function plugin_ver( $file ) {
		return filemtime( $this->plugin_path( $file ) );
	}

	/**
	 * Returns the current plugin's basename, eg. 'my_plugin/my_plugin.php'.
	 *
	 * @return string Basename
	 * @author John Blackbourn
	 **/
	protected function plugin_base() {
		return $this->plugin( 'base' );
	}

	/**
	 * Populates and returns the current plugin info.
	 *
	 * @author John Blackbourn
	 **/
	protected function plugin( $item, $file = '' ) {
		if ( !isset( $this->plugin ) ) {
			$this->plugin = array(
				'url'  => plugin_dir_url( $this->file ),
				'path' => plugin_dir_path( $this->file ),
				'base' => plugin_basename( $this->file )
			);
		}
		return $this->plugin[$item] . ltrim( $file, '/' );
	}

	/**
	 * Returns the name of the nonce field for a given post and meta key
	 *
	 * @param int $post_id The post ID
	 * @param string $meta_key The meta field key
	 * @return string The name to use in the nonce for the given post and meta key
	 * @author John Blackbourn
	 **/
	function meta_handler_nonce_name( $post_id, $meta_key ) {
		$meta_key = sanitize_title( $meta_key );
		return "handle_meta_{$post_id}_{$meta_key}_nonce";
	}

	/**
	 * Returns the complete nonce field for a given post and meta key
	 *
	 * @param int $post_id The post ID
	 * @param string $meta_key The meta field key
	 * @return string The complete nonce field for the given post and meta field
	 * @author John Blackbourn
	 **/
	function meta_handler_nonce_field( $post_id, $meta_key ) {
		$name  = $this->meta_handler_nonce_name( $post_id, $meta_key );
		$value = $this->meta_handler_nonce_value( $post_id, $meta_key );
		return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
	}

	/**
	 * Returns the nonce for a given post and meta key
	 *
	 * @param int $post_id The post ID
	 * @param string $meta_key The meta field key
	 * @return string The value to use in the nonce for the given post and meta key
	 * @author John Blackbourn
	 **/
	function meta_handler_nonce_value( $post_id, $meta_key ) {
		return wp_create_nonce( $this->meta_handler_nonce_name( $post_id, $meta_key ) );
	}

	/**
	 * Verifies a nonce in the current request for a given post and meta key
	 *
	 * @param int $post_id The post ID
	 * @param string $meta_key The meta field key
	 * @return bool Whether the corresponding nonce found in $_REQUEST is present and valid or not
	 **/
	function verify_meta_handler_nonce( $post_id, $meta_key ) {
		$name = $this->meta_handler_nonce_name( $post_id, $meta_key );
		if ( isset( $_REQUEST[$name] ) )
			return wp_verify_nonce( $_REQUEST[$name], $name );
		return false;
	}
	
	/**
	 * Hooks the WP admin_notices action to render any notices
	 * that have been set with the set_admin_notice method.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function admin_notices() {
		$user_id = get_current_user_id();
		if ( ! $errors = get_user_meta( $user_id, 'ird_admin_errors', true ) )
			$errors =  array();
		if ( ! $notices = get_user_meta( $user_id, 'ird_admin_notices', true ) )
			$notices = array();

		if ( $errors )
			foreach ( $errors as $error )
				$this->render_admin_error( $error );

		if ( $notices )
			foreach ( $notices as $notice )
				$this->render_admin_notice( $notice );

		delete_user_meta( $user_id, 'ird_admin_errors' );
		delete_user_meta( $user_id, 'ird_admin_notices' );
	}
	
	/**
	 * Echoes some HTML for an admin notice.
	 *
	 * @param string $notice The notice 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function render_admin_notice( $notice ) {
		echo "<div class='updated'><p>$notice</p></div>";
	}
	
	/**
	 * Echoes some HTML for an admin error.
	 *
	 * @param string $error The error 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function render_admin_error( $error ) {
		echo "<div class='error'><p>$error</p></div>";
	}
	
	/**
	 * Sets a string as an admin notice.
	 *
	 * @param string $msg A *localised* admin notice message 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function set_admin_notice( $msg ) {
		$user_id = get_current_user_id();
		if ( ! $notices = get_user_meta( $user_id, 'ird_admin_notices', true ) )
			$notices = array();
		$notices[] = $msg;
		update_user_meta( $user_id, 'ird_admin_notices', $notices );
	}
	
	/**
	 * Sets a string as an admin error.
	 *
	 * @param string $msg A *localised* admin error message 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function set_admin_error( $msg ) {
		$user_id = get_current_user_id();
		if ( ! $errors = get_user_meta( $user_id, 'ird_admin_errors', true ) )
			$errors =  array();
		// @TODO: Set hash of message as index, to prevent dupes
		$errors[] = $msg;
		update_user_meta( $user_id, 'ird_admin_errors', $errors );
	}

	/**
	 * A version of _n() which accepts already-localised strings as parameters
	 *
	 * @param string $singular The text that will be used if $number is 1
	 * @param string $plural The text that will be used if $number is not 1
	 * @param int $number The number to compare against to use either $singular or $plural
	 * @return string Either $singular or $plural text
	 * @author John Blackbourn
	 */
	public function n( $singular, $plural, $number ) {
		return ( 1 == $number ) ? $singular : $plural;
	}

}

defined( 'ABSPATH' ) or die();

?>