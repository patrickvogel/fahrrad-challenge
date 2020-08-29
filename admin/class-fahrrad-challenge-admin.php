<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/patrickvogel
 * @since      1.0.0
 *
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/admin
 * @author     Patrick Vogel <fahrrad-challenge@patrickvogel.de>
 */
class Fahrrad_Challenge_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $fahrrad_challenge_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_menu', array( $this, 'fahrrad_challenge_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'fahrrad_challenge_page_init' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fahrrad_Challenge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fahrrad_Challenge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fahrrad-challenge-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fahrrad_Challenge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fahrrad_Challenge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fahrrad-challenge-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function fahrrad_challenge_add_plugin_page() {
		add_plugins_page(
			__( 'Bike-Challenge', 'fahrrad-challenge' ), // page_title
			__( 'Bike-Challenge', 'fahrrad-challenge' ), // menu_title
			'manage_options', // capability
			'fahrrad-challenge', // menu_slug
			array( $this, 'fahrrad_challenge_create_admin_page' ) // function
		);
	}

	public function fahrrad_challenge_create_admin_page() {
		$this->fahrrad_challenge_options = get_option( 'fahrrad_challenge_option_name' ); ?>

		<div class="wrap">
			<h2><?= __( 'Bike-Challenge', 'fahrrad-challenge' ) ?></h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'fahrrad_challenge_option_group' );
					do_settings_sections( 'fahrrad-challenge-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function fahrrad_challenge_page_init() {
		register_setting(
			'fahrrad_challenge_option_group', // option_group
			'fahrrad_challenge_option_name', // option_name
			array( $this, 'fahrrad_challenge_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'fahrrad_challenge_setting_section', // id
			__( 'Configuration', 'fahrrad-challenge' ), // title
			array( $this, 'fahrrad_challenge_section_info' ), // callback
			'fahrrad-challenge-admin' // page
		);

		add_settings_field(
			'begin_date', // id
			__( 'Begin date of campaign (YYYY-MM-DD)', 'fahrrad-challenge' ), // title
			array( $this, 'begin_date_callback' ), // callback
			'fahrrad-challenge-admin', // page
			'fahrrad_challenge_setting_section' // section
		);

		add_settings_field(
			'end_date', // id
			__( 'End date of campaign (YYYY-MM-DD)', 'fahrrad-challenge' ), // title
			array( $this, 'end_date_callback' ), // callback
			'fahrrad-challenge-admin', // page
			'fahrrad_challenge_setting_section' // section
		);

		add_settings_field(
			'co2_factor', // id
			__( 'CO2 factor (CO2 factor * distance = CO2 savings; e.g. 0.1875)', 'fahrrad-challenge' ), // title
			array( $this, 'co2_factor_callback' ), // callback
			'fahrrad-challenge-admin', // page
			'fahrrad_challenge_setting_section' // section
		);

		add_settings_field(
			'future_entries', // id
			__( 'Future entries', 'fahrrad-challenge' ), // title
			array( $this, 'future_entries_callback' ), // callback
			'fahrrad-challenge-admin', // page
			'fahrrad_challenge_setting_section' // section
		);
	}

	public function fahrrad_challenge_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['begin_date'] ) ) {
			$sanitary_values['begin_date'] = sanitize_text_field( $input['begin_date'] );
		}

		if ( isset( $input['end_date'] ) ) {
			$sanitary_values['end_date'] = sanitize_text_field( $input['end_date'] );
		}

		if ( isset( $input['co2_factor'] ) ) {
			$sanitary_values['co2_factor'] = sanitize_text_field( $input['co2_factor'] );
		}

		if ( isset( $input['future_entries'] ) ) {
			$sanitary_values['future_entries'] = $input['future_entries'];
		}

		return $sanitary_values;
	}

	public function fahrrad_challenge_section_info() {
		
	}

	public function begin_date_callback() {
		printf(
			'<input class="regular-text" type="text" name="fahrrad_challenge_option_name[begin_date]" id="begin_date" value="%s">',
			isset( $this->fahrrad_challenge_options['begin_date'] ) ? esc_attr( $this->fahrrad_challenge_options['begin_date']) : ''
		);
	}

	public function end_date_callback() {
		printf(
			'<input class="regular-text" type="text" name="fahrrad_challenge_option_name[end_date]" id="end_date" value="%s">',
			isset( $this->fahrrad_challenge_options['end_date'] ) ? esc_attr( $this->fahrrad_challenge_options['end_date']) : ''
		);
	}

	public function co2_factor_callback() {
		printf(
			'<input class="regular-text" type="text" name="fahrrad_challenge_option_name[co2_factor]" id="co2_factor" value="%s">',
			isset( $this->fahrrad_challenge_options['co2_factor'] ) ? esc_attr( $this->fahrrad_challenge_options['co2_factor']) : ''
		);
	}

	public function future_entries_callback() {
		printf(
			'<input type="checkbox" name="fahrrad_challenge_option_name[future_entries]" id="future_entries" value="future_entries" %s> <label for="future_entries">'.__( 'Allowed', 'fahrrad-challenge' ).'</label>',
			( isset( $this->fahrrad_challenge_options['future_entries'] ) && $this->fahrrad_challenge_options['future_entries'] === 'future_entries' ) ? 'checked' : ''
		);
	}

}

/* 
 * Retrieve this value with:
 * $fahrrad_challenge_options = get_option( 'fahrrad_challenge_option_name' ); // Array of All Options
 * $begin_date = $fahrrad_challenge_options['begin_date']; // Aktionszeitraum Beginn (YYYY-MM-DD)
 * $end_date = $fahrrad_challenge_options['end_date']; // Aktionszeitraum Ende (YYYY-MM-DD)
 * $co2_factor = $fahrrad_challenge_options['co2_factor']; // CO2-Faktor (Strecke * Faktor = CO2-Einsparung)
 * $future_entries = $fahrrad_challenge_options['future_entries']; // Einträge in der Zukunft erlaubt
 */
