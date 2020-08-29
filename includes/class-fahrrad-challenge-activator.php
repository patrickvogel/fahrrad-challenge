<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/patrickvogel
 * @since      1.0.0
 *
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/includes
 * @author     Patrick Vogel <fahrrad-challenge@patrickvogel.de>
 */
class Fahrrad_Challenge_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		
		$sql = "CREATE TABLE $table_name (
				`user_id` bigint(20) UNSIGNED NOT NULL,
				`date` date NOT NULL,
				`distance` float NOT NULL,
				`co2` BOOLEAN NOT NULL,
    			PRIMARY KEY(`user_id`, `date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
		dbDelta($sql);
	}

}
