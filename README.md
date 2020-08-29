# Fahrrad-Challenge

## License

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description

This plugin offers a "bike challenge" to all users of a WP instance: 

* Users can enter the kilometers they have ridden by bike
* The distances are added up and the CO2 savings are calculated

## Installation

1. Zip the folder and name it `fahrrad-challenge.zip`
2. Upload `fahrrad-challenge.zip` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin through the 'Plugins'->'Bike-Challenge' menu in WordPress

## Usage

The following shortcodes are currently available:

* Shortcodes that can be used on any page:

  * [fahrrad_challenge_total] - Total distance
  * [fahrrad_challenge_co2] - Total CO2 savings
  * [fahrrad_challenge_top5_distance] - Top 5 users (distance)
  * [fahrrad_challenge_top5_co2] - Top 5 users (CO2 savings)

* Shortcodes that should be used on pages that are only accessible for users logged in:
  
  * [fahrrad_challenge_user_total] - Total distance of the user logged in
  * [fahrrad_challenge_user_co2] - Total CO2 savings of the user logged in
  * [fahrrad_challenge_user_input] - Input form to enter ridden kilometers
  * [fahrrad_challenge_user_entries] - List of all entered datasets of the user logged in


