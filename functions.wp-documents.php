<?php
/**
 * Documents Procedural API
 */

/**
 * Initialize $wp_documents if it has not been set.
 *
 * @global WP_Documents $wp_documents
 *
 * @return WP_Documents WP_Documents instance.
 */
function wp_documents() {
	global $wp_documents;
	if ( ! ( $wp_documents instanceof WP_Documents ) ) {
		$wp_documents = new WP_Documents();
	}
	return $wp_documents;
}

/**
 * Display documents that are in the $handles queue.
 *
 * Passing an empty array to $handles prints the queue,
 * passing an array with one string prints that document,
 * and passing an array of strings prints those documents.
 *
 * @global WP_Documents $wp_documents The WP_Documents object for printing documents.
 *
 * @param string|bool|array $handles Documents to be printed. Default 'false'.
 * @return array On success, a processed array of WP_Dependencies items; otherwise, an empty array.
 */
function wp_print_documents( $handles = false ) {
	if ( '' === $handles ) { // for wp_head
		$handles = false;
	}
	/**
	 * Fires before documents in the $handles queue are printed.
	 */
	if ( ! $handles ) {
		do_action( 'wp_print_documents' );
	}

	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	global $wp_documents;
	if ( ! ( $wp_documents instanceof WP_Documents ) ) {
		if ( ! $handles ) {
			return array(); // No need to instantiate if nothing is there.
		}
	}

	return wp_documents()->do_items( $handles );
}

/**
 * Register a HTML document.
 *
 * @see WP_Dependencies::add()
 *
 * @param string      $handle Name of the document.
 * @param string|bool $src    Path to the document from the WordPress root directory. Example: '/my.html'.
 * @param array       $deps   An array of registered document handles this document depends on. Default empty array.
 * @param string|bool $ver    String specifying the document version number. Used to ensure that the correct version
 *                            is sent to the client regardless of caching. Default 'false'. Accepts 'false', 'null', or 'string'.
 * @return bool Whether the document has been registered. True on success, false on failure.
 */
function wp_register_document( $handle, $src, $deps = array(), $ver = false ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	return wp_documents()->add( $handle, $src, $deps, $ver );
}

/**
 * Remove a registered document.
 *
 * @see WP_Dependencies::remove()
 *
 * @param string $handle Name of the document to be removed.
 */
function wp_deregister_document( $handle ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	wp_documents()->remove( $handle );
}

/**
 * Enqueue a HTML document.
 *
 * Registers the document if source provided (does NOT overwrite) and enqueues.
 *
 * @see WP_Dependencies::add(), WP_Dependencies::enqueue()
 *
 * @param string      $handle Name of the document.
 * @param string|bool $src    Path to the document from the WordPress root directory. Example: '/my.html'.
 * @param array       $deps   An array of registered document handles this document depends on. Default empty array.
 * @param string|bool $ver    String specifying the document version number, if it has one. This parameter is used
 *                            to ensure that the correct version is sent to the client regardless of caching, and so
 *                            should be included if a version number is available and makes sense for the document.
 */
function wp_enqueue_document( $handle, $src = false, $deps = array(), $ver = false ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	$wp_documents = wp_documents();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_documents->add( $_handle[0], $src, $deps, $ver );
	}
	$wp_documents->enqueue( $handle );
}

/**
 * Remove a previously enqueued HTML document.
 *
 * @see WP_Dependencies::dequeue()
 *
 * @param string $handle Name of the document to be removed.
 */
function wp_dequeue_document( $handle ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	wp_documents()->dequeue( $handle );
}

/**
 * Check whether a HTML document has been added to the queue.
 *
 * @param string $handle Name of the document.
 * @param string $list   Optional. Status of the document to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether document is queued.
 */
function wp_document_is( $handle, $list = 'enqueued' ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	return (bool) wp_documents()->query( $handle, $list );
}

/**
 * Add metadata to a HTML document.
 *
 * Works only if the document has already been added.
 *
 * Possible values for $key and $value:
 * 'conditional' string      Comments for IE 6, lte IE 7 etc.
 * 'rtl'         bool|string To declare an RTL document.
 * 'suffix'      string      Optional suffix, used in combination with RTL.
 * 'alt'         bool        For rel="alternate stylesheet".
 * 'title'       string      For preferred/alternate stylesheets.
 *
 * @see WP_Dependency::add_data()
 *
 * @param string $handle Name of the document.
 * @param string $key    Name of data point for which we're storing a value.
 *                       Accepts 'conditional', 'rtl' and 'suffix', 'alt' and 'title'.
 * @param mixed  $value  String containing the HTML data to be added.
 * @return bool True on success, false on failure.
 */
function wp_document_add_data( $handle, $key, $value ) {
	return wp_documents()->add_data( $handle, $key, $value );
}
