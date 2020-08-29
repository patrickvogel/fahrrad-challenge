<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/patrickvogel
 * @since      1.0.0
 *
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Fahrrad_Challenge
 * @subpackage Fahrrad_Challenge/public
 * @author     Patrick Vogel <fahrrad-challenge@patrickvogel.de>
 */
class Fahrrad_Challenge_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode( 'fahrrad_challenge_total', array( $this, 'shortcutTotal' ));
		add_shortcode( 'fahrrad_challenge_co2', array( $this, 'shortcutCO2' ));
		add_shortcode( 'fahrrad_challenge_user_total', array( $this, 'shortcutUserTotal' ));
		add_shortcode( 'fahrrad_challenge_user_co2', array( $this, 'shortcutUserCO2' ));
		add_shortcode( 'fahrrad_challenge_user_input', array( $this, 'shortcutUserInput' ));
		add_shortcode( 'fahrrad_challenge_user_entries', array( $this, 'shortcutUserEntries' ));
		add_shortcode( 'fahrrad_challenge_top5_distance', array( $this, 'shortcutTop5Distance' ));
		add_shortcode( 'fahrrad_challenge_top5_co2', array( $this, 'shortcutTop5CO2' ));
		add_action( 'init', array($this, 'actionProcessInput') );
		add_action( 'init', array($this, 'actionDeleteEntry') );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fahrrad-challenge-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fahrrad-challenge-public.js', array( 'jquery' ), $this->version, false );

	}

	public function shortcutTotal( $atts, $content = "" ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT COALESCE(SUM(distance), 0) AS distance FROM $table_name";
		$result = $wpdb->get_results($sql) or die(mysql_error());		
		return $this->formatNumber($result[0]->distance, 1, ' km');
	}

	public function shortcutUserTotal( $atts, $content = "" ) {
		$user = wp_get_current_user();
		if($user->ID===0) return; // Only for users

		global $wpdb;
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT COALESCE(SUM(distance), 0) AS distance FROM $table_name WHERE user_id=$user->ID";
		$result = $wpdb->get_results($sql) or die(mysql_error());		
		return $this->formatNumber($result[0]->distance, 1, ' km');
	}

	public function shortcutCO2( $atts, $content = "" ) {
		global $wpdb;
		$options = $this->getOptions();
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT COALESCE(SUM(distance), 0)*%f AS co2 FROM $table_name WHERE co2=1";
		$result = $wpdb->get_results($wpdb->prepare($sql,$options['co2_factor'])) or die(mysql_error());		
		return $this->formatNumber($result[0]->co2, 1, ' kg');
	}

	public function shortcutUserCO2( $atts, $content = "" ) {
		$user = wp_get_current_user();
		if($user->ID===0) return; // Only for users

		$options = $this->getOptions();

		global $wpdb;
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT COALESCE(SUM(distance), 0)*%f AS co2 FROM $table_name WHERE co2=1 AND user_id=$user->ID";
		$result = $wpdb->get_results($wpdb->prepare($sql,$options['co2_factor'])) or die(mysql_error());		
		return $this->formatNumber($result[0]->co2, 1, ' kg');
	}

	public function shortcutTop5Distance( $atts, $content = "" ) {
		global $wpdb;
		$user_table_name = $wpdb->prefix . 'users';
		$entries_table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT u.display_name, e.distance FROM (SELECT user_id, COALESCE(SUM(distance), 0) AS distance FROM $entries_table_name GROUP BY user_id ORDER BY 2 DESC LIMIT 0,5) e INNER JOIN $user_table_name u ON e.user_id = u.ID ORDER BY 2 DESC";
		$result = $wpdb->get_results($sql) or die(mysql_error());		
		$return = "<ol>";
		foreach($result as $user){
			$return .= "<li>$user->display_name: ".$this->formatNumber($user->distance, 1, ' km')."</li>";
		}
		$return .= "</ol>";
		return $return;
	}

	public function shortcutTop5CO2( $atts, $content = "" ) {
		global $wpdb;
		$options = $this->getOptions();
		$user_table_name = $wpdb->prefix . 'users';
		$entries_table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT u.display_name, e.co2 FROM (SELECT user_id, COALESCE(SUM(distance)*%f, 0) AS co2 FROM $entries_table_name WHERE co2=1 GROUP BY user_id ORDER BY 2 DESC LIMIT 0,5) e INNER JOIN $user_table_name u ON e.user_id = u.ID ORDER BY 2 DESC";
		$result = $wpdb->get_results($wpdb->prepare($sql,$options['co2_factor'])) or die(mysql_error());		
		$return = "<ol>";
		foreach($result as $user){
			$return .= "<li>$user->display_name: ".$this->formatNumber($user->co2, 1, ' kg')."</li>";
		}
		$return .= "</ol>";
		return $return;
	}

	public function shortcutUserInput( $atts, $content = "" ) {
		$user = wp_get_current_user();
		if($user->ID===0) return; // Only for users

		$options = $this->getOptions();

		$from = new Datetime($options['begin_date']);
		$fromStr = $from->format('Y-m-d');
		$to = new Datetime($options['end_date']); 
		$now = new Datetime('now'); 	
		if(!$options['future_entries'] && $now < $to) {
			$to = $now;
		}

		$toStr = $to->format('Y-m-d');

		global $wpdb;
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT `date` FROM $table_name WHERE user_id = $user->ID";
		$result = $wpdb->get_results($sql);	
		
		$return = '<form action="" method="post">
					<input type="hidden" name="fahrrad_challenge_input" value="true">
					<div class="wp-block-columns">
						<div class="wp-block-column" style="min-width:310px;flex-basis:33.33%">
							<p>
								<label for="fahrrad_challenge_date">'.__( 'Date', 'fahrrad-challenge' ).'</label>
								<input type="text" name="fahrrad_challenge_date" value="" id="fahrrad_challenge_datepicker" required>
							</p>
						</div>
						<div class="wp-block-column" style="flex-basis:66.67%">
							<p>
								<label for="fahrrad_challenge_distance">'.__( 'Distance (km)', 'fahrrad-challenge' ).'</label>
								<br><input type="text" name="fahrrad_challenge_distance" value="'.$_POST['fahrrad_challenge_distance'].'" size="6" required pattern="[0-9]+([,\.][0-9]+)?">
							</p>
							<p>
								<label for="fahrrad_challenge_distance">'.__( 'Saved CO<sub>2</sub>?', 'fahrrad-challenge' ).'</label>
								<br>
								<input type="radio" name="fahrrad_challenge_co2" id="fahrrad_challenge_co2_1" value="1" checked>
								<label for="fahrrad_challenge_co2_1"><small> '.__( 'Bike instead of car', 'fahrrad-challenge' ).'</small></label>
								<br>
								<input type="radio" name="fahrrad_challenge_co2" id="fahrrad_challenge_co2_0" value="0">
								<label for="fahrrad_challenge_co2_0"><small> '.__( 'No CO<sub>2</sub> savings', 'fahrrad-challenge' ).'</small></label>
							</p>
							<p>
								<input type="submit" class="fahrrad-challenge-btn-save" value="'.__( 'Save', 'fahrrad-challenge' ).'">
							</p>
						</div>
					</div>
				</form>
				<style>';
		foreach($result as $entry){
			$return .= 'span[aria-label="'.$entry->date.'"] {
							color: #fff !important;
							background-color: #0070AD !important;
				  		}';
		}	
		$return .= '</style>
				<script>
					flatpickr("#fahrrad_challenge_datepicker",
							  { 
								locale: "'.__( 'en', 'fahrrad-challenge' ).'",
								inline: true,
								allowInput: true,
								mode: "multiple",
								dateFormat: "Y-m-d",
								ariaDateFormat: "Y-m-d",
								minDate: "'.$fromStr.'",
								maxDate: "'.$toStr.'",
								disable: [';

		for($i = 0; $i < count($result); $i++) {
			if($i>0) $return .= ',';
			$return .= '"'.$result[$i]->date.'"';			
		}						
								
		$return .= '  					  ]
							  })
				</script>';

		return $return;
	}

	public function shortcutUserEntries( $atts, $content = "" ) {
		$user = wp_get_current_user();
		if($user->ID===0) return; // Only for users

		global $wpdb;
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "SELECT `date`, COALESCE(distance,0) AS distance, CASE WHEN co2 = 1 THEN '✓' ELSE '' END, `date` AS dummy FROM $table_name WHERE user_id = $user->ID ORDER BY 1 DESC";
		$result = $wpdb->get_results($sql);		
		foreach($result as $entry){
			$entry->date = $this->formatDate($entry->date);
			$entry->distance = $this->formatNumber($entry->distance, 1, '');
		}
		$return .= '<table id="fahrrad_challenge_entries"></table>
				   <script>
						var data = '.json_encode($result).';
						var obj = {
							// Quickly get the headings
							headings: ["'.__( 'Date', 'fahrrad-challenge' ).'","'.__( 'Distance', 'fahrrad-challenge' ).'","'.__( 'CO<sub>2</sub> savings', 'fahrrad-challenge' ).'",""],
						
							// data array
							data: []
						};
						
						// Loop over the objects to get the values
						for ( var i = 0; i < data.length; i++ ) {
						
							obj.data[i] = [];
						
							for (var p in data[i]) {
								if( data[i].hasOwnProperty(p) ) {
									obj.data[i].push(data[i][p]);
								}
							}
						}
					   	var dataTable = new DataTable("#fahrrad_challenge_entries", {
							data: obj,
							searchable: false,
							labels: {
								perPage: "'.__( '{select} entries per page', 'fahrrad-challenge' ).'",
								noRows: "'.__( 'No entries to found', 'fahrrad-challenge' ).'",
								info: "'.__( 'Showing {start} to {end} of {rows} entries', 'fahrrad-challenge' ).'",
							},
							columns: [
								{ 
									select: 0, 
									type: "date", 
									format: "'.get_option('date_format').'",
									sort: "asc"
								},
								{ 
									select: 1, 
									type: "number",
									render: function(data, cell, row) {
										return data + " km";
									} 
								},						
								{
									select: 3,
									sortable: false,
									render: function(data, cell, row) {
										return \'<form action="" method="post">\'
											  +\'	<input type="hidden" name="fahrrad_challenge_delete" value="true">\'
											  +\'	<input type="hidden" name="fahrrad_challenge_date" value="\'+data+\'">\'
											  +\'	<input type="submit" class="fahrrad-challenge-btn-delete" value="'.__( 'Delete', 'fahrrad-challenge' ).'">\'
											  +\'</form>\';
									}
								}
							]   
						});
				   </script>';

		return $return;
	}

	public function actionProcessInput() {
		$user = wp_get_current_user();
		if($user->ID===0) return; // Only for users
		
		if( ! isset( $_POST['fahrrad_challenge_input'] ) ) return; // Only if form was submitted

		global $wpdb;

		$user = wp_get_current_user();
		$dateStr = sanitize_text_field($_POST['fahrrad_challenge_date']);
		$distance = floatval(str_replace(",",".",sanitize_text_field($_POST['fahrrad_challenge_distance'])));
		$co2 = intval(sanitize_text_field($_POST['fahrrad_challenge_co2']));

		$dates = explode(", ", $dateStr);
		
		foreach($dates as $date){
			if($this->validateDate($date) && $distance >=0){

				$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
				$data = array('user_id' => $user->ID, 'date' => $date, 'distance' => $distance, 'co2' => $co2);
				$format = array('%d','%s', '%f', '%d');
				$wpdb->insert($table_name,$data,$format);		
			}
		}
		
		return;
		
	}

	public function actionDeleteEntry() {
		$user = wp_get_current_user();
		if($user->ID===0) return; // Only for users
		
		if( ! isset( $_POST['fahrrad_challenge_delete'] ) ) return; // Only if form was submitted

		global $wpdb;

		$user = wp_get_current_user();
		$date = sanitize_text_field($_POST['fahrrad_challenge_date']);
		
		$table_name = $wpdb->prefix . 'fahrrad_challenge_entries';
		$sql = "DELETE 
		          FROM $table_name
				 WHERE `user_id` = %s
				   AND `date` = %s;";
		$wpdb->query($wpdb->prepare($sql,$user->ID, $date));		
		
		return;
		
	}

	private function validateDate($date, $format = 'Y-m-d')
	{
		$options = $this->getOptions();
		/* $begin_date = $fahrrad_challenge_options['begin_date']; // Aktionszeitraum Beginn (YYYY-MM-DD)
		* $end_date = $fahrrad_challenge_options['end_date']; // Aktionszeitraum Ende (YYYY-MM-DD)
		* $co2_factor = $fahrrad_challenge_options['co2_factor']; // CO2-Faktor (Strecke * Faktor = CO2-Einsparung)
		* $future_entries = $fahrrad_challenge_options['future_entries']; // Einträge in der Zukunft erlaubt 
		*/

		$d = DateTime::createFromFormat($format, $date);
		$from = new Datetime($options['begin_date']); // Nicht vor Stichtag (TODO: via Konfig)
		$to = new Datetime($options['end_date']);
		$now = new Datetime('now'); 	
		if(!$options['future_entries'] && $now < $to) {
			$to = $now;
		}
		return $d && $d->format($format) === $date && $d >= $from && $d <= $to;
	}

	private function getOptions(){
		return get_option( 'fahrrad_challenge_option_name' );
	}

	private function formatNumber($number, $decimals, $metric){
		$dec_point = __( '.', 'fahrrad-challenge' );
		$thousands_sep = __( ',', 'fahrrad-challenge' );
		return number_format ($number, $decimals, $dec_point, $thousands_sep).$metric;
	}

	private function formatDate($date){
		$d = new DateTime($date);
		$f = get_option('date_format');
		return $d->format($f);
	}

}
