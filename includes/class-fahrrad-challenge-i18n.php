<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/patrickvogel
 * @since      1.0.0
 *
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/includes
 * @author     Patrick Vogel <fahrrad-challenge@patrickvogel.de>
 */
class Fahrrad_Challenge_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fahrrad-challenge',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
