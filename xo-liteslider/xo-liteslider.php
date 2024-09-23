<?php
/**
 * XO Slider plugin for WordPress.
 *
 * @package xo-slider
 * @author  ishitaka
 * @license GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       XO Slider
 * Plugin URI:        https://xakuro.com/wordpress/
 * Description:       XO Slider is a plugin that allows you to easily create sliders.
 * Author:            Xakuro
 * Author URI:        https://xakuro.com/
 * License:           GPL v2 or later
 * Requires at least: 4.9
 * Requires PHP:      7.0
 * Version:           3.8.6
 * Text Domain:       xo-liteslider
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'XO_SLIDER_VERSION', '3.8.6' );
define( 'XO_SLIDER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

require_once __DIR__ . '/inc/class-xo-slider.php';

$xo_slider = new XO_Slider();

/**
 * Display slider.
 *
 * @since 2.0.0
 *
 * @param int  $slider_id       Optional, default is latest slider. Slider ID.
 * @param bool $deprecated_echo Optional, default is true. Set to false for return.
 * @return void|string Void if `$echo` argument is true, slider HTML if `$echo` is false.
 */
function xo_slider( $slider_id = 0, $deprecated_echo = true ) {
	global $xo_slider;

	if ( true !== $deprecated_echo ) {
		_deprecated_argument( __FUNCTION__, '3.8.0', 'Use <code>$xo_slider->get_slider()</code> instead if you do not want the value echoed.' );
	}

	$slider = $xo_slider->get_slider( $slider_id );
	if ( $deprecated_echo ) {
		echo $slider; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	return $slider;
}

/**
 * Display slider.
 *
 * @since 1.0.0
 * @deprecated 2.0.0 Use xo_slider()
 * @see xo_slider()
 *
 * @param int $slider_id Optional, default is latest slider. Slider ID.
 */
function xo_liteslider( $slider_id = 0 ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'xo_slider' );

	xo_slider( $slider_id, true );
}
