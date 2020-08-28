<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/patrickvogel
 * @since             1.0.0
 * @package           Fahrrad_Challenge
 *
 * @wordpress-plugin
 * Plugin Name:       Fahrrad-Challenge
 * Plugin URI:        https://github.com/patrickvogel/fahrrad-challenge
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Patrick Vogel
 * Author URI:        https://github.com/patrickvogel
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fahrrad-challenge
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FAHRRAD_CHALLENGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fahrrad-challenge-activator.php
 */
function activate_fahrrad_challenge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fahrrad-challenge-activator.php';
	Fahrrad_Challenge_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fahrrad-challenge-deactivator.php
 */
function deactivate_fahrrad_challenge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fahrrad-challenge-deactivator.php';
	Fahrrad_Challenge_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fahrrad_challenge' );
register_deactivation_hook( __FILE__, 'deactivate_fahrrad_challenge' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fahrrad-challenge.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fahrrad_challenge() {

	$plugin = new Fahrrad_Challenge();
	$plugin->run();

}
run_fahrrad_challenge();
