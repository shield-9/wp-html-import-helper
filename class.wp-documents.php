<?php
/**
 * Documents enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 */

/**
 * Documents enqueue class.
 *
 * @uses WP_Dependencies
 */
class WP_Documents extends WP_Dependencies {
	public $base_url;
	public $content_url;
	public $default_version;
	public $text_direction = 'ltr';
//	public $concat = '';
//	public $concat_version = '';
//	public $do_concat = false;
	public $print_html = '';
	public $print_code = '';
	public $default_dirs;

	public function __construct() {
		/**
		 * Fires when the WP_Documents instance is initialized.
		 *
		 * @param WP_Documents &$this WP_Documents instance, passed by reference.
		 */
		do_action_ref_array( 'wp_default_documents', array( &$this ) );
	}

	/**
	 * @param string $handle
	 * @return bool
	 */
	public function do_item( $handle ) {
		if ( !parent::do_item( $handle ) ) {
			return false;
		}

		$obj = $this->registered[ $handle ];
		if ( null === $obj->ver ) {
			$ver = '';
		} else {
			$ver = $obj->ver ? $obj->ver : $this->default_version;
		}

		if ( isset( $this->args[ $handle ] ) ) {
			$ver = $ver ? $ver . '&amp;' . $this->args[ $handle ] : $this->args[ $handle ];
		}

/*
		if ( $this->do_concat ) {
			if ( $this->in_default_dir( $obj->src ) && !isset( $obj->extra['conditional'] ) && !isset( $obj->extra['alt'] ) ) {
				$this->concat .= "$handle,";
				$this->concat_version .= "$handle$ver";

				$this->print_code .= $this->print_inline_document( $handle, false );

				return true;
			}
		}
*/

		$href = $this->_html_href( $obj->src, $ver, $handle );
		if ( empty( $href ) ) {
			// Turns out there is nothing to print.
			return true;
		}
		$rel = 'import';
		$title = isset( $obj->extra['title'] ) ? "title='" . esc_attr( $obj->extra['title'] ) . "'" : '';

		/**
		 * Filter the HTML link tag of an enqueued document.
		 *
		 * @param string $html   The link tag for the enqueued document.
		 * @param string $handle The document's registered handle.
		 * @param string $href   The document's source URL.
		 */
		$tag = apply_filters( 'document_loader_tag', "<link rel='$rel' id='$handle-document' $title href='$href' />\n", $handle, $href );

		$conditional_pre = $conditional_post = '';
		if ( isset( $obj->extra['conditional'] ) && $obj->extra['conditional'] ) {
			$conditional_pre  = "<!--[if {$obj->extra['conditional']}]>\n";
			$conditional_post = "<![endif]-->\n";
		}
/*
		if ( $this->do_concat ) {
			$this->print_html .= $conditional_pre;
			$this->print_html .= $tag;
			if ( $inline_style = $this->print_inline_style( $handle, false ) ) {
				$this->print_html .= sprintf( "<style id='%s-inline-css' type='text/css'>\n%s\n</style>\n", esc_attr( $handle ), $inline_style );
			}
			$this->print_html .= $conditional_post;
		} else {
*/
			echo $conditional_pre;
			echo $tag;
			echo $conditional_post;
//		}

		return true;
	}

	/**
	 * @param mixed $handles
	 * @param bool $recursion
	 * @param mixed $group
	 * @return bool
	 */
	public function all_deps( $handles, $recursion = false, $group = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion ) {
			/**
			 * Filter the array of enqueued documents before processing for output.
			 *
			 * @param array $to_do The list of enqueued documents about to be processed.
			 */
			$this->to_do = apply_filters( 'print_documents_array', $this->to_do );
		}
		return $r;
	}

	/**
	 * @param string $src
	 * @param string $ver
	 * @param string $handle
	 * @return string
	 */
	public function _html_href( $src, $ver, $handle ) {
		if ( !is_bool($src) && !preg_match('|^(https?:)?//|', $src) && ! ( $this->content_url && 0 === strpos($src, $this->content_url) ) ) {
			$src = $this->base_url . $src;
		}

		if ( !empty($ver) )
			$src = add_query_arg('ver', $ver, $src);

		/**
		 * Filter an enqueued document's fully-qualified URL.
		 *
		 * @param string $src    The source URL of the enqueued document.
		 * @param string $handle The document's registered handle.
		 */
		$src = apply_filters( 'document_loader_src', $src, $handle );
		return esc_url( $src );
	}

	/**
	 * @param string $src
	 * @return bool
	 */
	public function in_default_dir($src) {
		if ( ! $this->default_dirs )
			return true;

		foreach ( (array) $this->default_dirs as $test ) {
			if ( 0 === strpos($src, $test) )
				return true;
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function do_footer_items() { // HTML 5 allows styles in the body, grab late enqueued items and output them in the footer.
		$this->do_items(false, 1);
		return $this->done;
	}

	/**
	 * @access public
	 */
	public function reset() {
//		$this->do_concat = false;
//		$this->concat = '';
//		$this->concat_version = '';
		$this->print_html = '';
	}
}