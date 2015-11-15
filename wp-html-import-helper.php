<?php
/**
 * Plugin Name: WP HTML Imports Helper
 * Plugin URI: https://github.com/shield-9/wp-html-imports-helper
 * Description: Add support for HTML Imports enqueue
 * Author: Daisuke Takahashi (Extend Wings)
 * Version: 0.1
 * Author URI: https://www.extendwings.com
 * Text Domain: wp-html-imports-helper
 * Domain Path: /languages
 */

require_once( plugin_dir_path( __FILE__ ) . 'class.wp-documents.php' );
require_once( plugin_dir_path( __FILE__ ) . 'functions.wp-documents.php' );

add_action( 'wp_head', 'wp_print_documents', 8 );

