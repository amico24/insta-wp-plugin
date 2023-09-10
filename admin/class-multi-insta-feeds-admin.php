<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Multi_Insta_Feeds
 * @subpackage Multi_Insta_Feeds/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Multi_Insta_Feeds
 * @subpackage Multi_Insta_Feeds/admin
 * @author     Cyrus Kael Abiera <cyrus.abiera@activ8bn.com>
 */
namespace MIF\Admin;
class Multi_Insta_Feeds_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('init', array($this,'init_admin_classes'));
		add_filter('query_vars', array($this, 'themeslug_query_vars' ));
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ));
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));
		add_action('admin_init', array($this, 'get_auth_code'));
		add_action( 'admin_notices', array($this, 'admin_warning'));
		add_shortcode( 'feed_display', array( $this, 'feed_display' ));

	}
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Multi_Insta_Feeds_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Multi_Insta_Feeds_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/multi-insta-feeds-admin.css', array(), $this->version, 'all' );

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
		 * defined in Multi_Insta_Feeds_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Multi_Insta_Feeds_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/multi-insta-feeds-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function init_admin_classes(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin\class-multi-insta-feeds-admin-accounts.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin\class-multi-insta-feeds-admin-api-connect.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin\multi-insta-feeds-admin-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin\class-multi-insta-feeds-admin-groups.php';
	}
 
	public function feed_display($atts, $content = ""){
		$a = shortcode_atts( array(
			'group' => ''					
			), $atts );
		$api = new Multi_Insta_Feeds_API_Connect;
		$groups = new Multi_Insta_Feeds_Groups;
		$post_list = array();


		//check if group exists
		if(!isset($a['group']) || $a['group'] >= $groups -> get_total_groups() || $a['group'] < 0){
			return '
			<p>Group not found.</p>
			';
		} else {
			//list of user ids in the group
			$user_list = $groups -> get_user_list($a['group'], 'user_id');

			//put 5 most recent posts on every account on $post_list
			foreach($user_list as $user){
				$recent_posts = array_slice($api -> get_media_list($user), 0, 5);
				array_push($post_list, $recent_posts);
			}

			//flatten the array to make it 1 dimensional
			$post_list = array_merge(...$post_list);

			//sort posts by date (copy/pasted lmao idk how this works)
			$date_compare = function ($a, $b){
				$t1 = strtotime($a['timestamp']);
				$t2 = strtotime($b['timestamp']);
				return $t2 - $t1;
			};
			usort($post_list, $date_compare);

			//get 5 most recent posts out of sorted array
			$post_list = array_slice($post_list, 0, 5);

			//build html for posts (used foreach loop in case of < 5 posts)
			$html_snippet = "";
			foreach(array_keys($post_list) as $post_index) {
				$html_snippet .= '
					<div>
						<p>'.$post_list[$post_index]['username'].'</p>
						<img src="'.$post_list[$post_index]['media_url'].'" height="200" width="200">
					</div>
				';
			}

			return '
			<div style="display: flex; gap: 5px;">
				'.$html_snippet.'
			</div>
			';
		}

	}

	//adds 'code' to the list of valid url parameters
	public function themeslug_query_vars( $qvars ) {
		$qvars[] = 'code';
		return $qvars;
	}

	//this is supposed to be for error checking but its kinda bad 
	function admin_warning($messages) {

		$error = get_option('mif_error', null);
		if(!empty($error)){
			$error_string = implode(' | ',$error['error']);
		
			$notice = <<<EOT
			<div class="notice notice-error">
				<p>Error: $error_string</p>
			</div>
			EOT;
			echo $notice;
		}
	}

	//calls instagram api if code string query is found
	public function get_auth_code(){
		if(isset($_GET['code'])){
			$api = new Multi_Insta_Feeds_API_Connect;
			$api -> retrieve_access_token();
		}
	}

	//imma be real past this point i have no clue what this does i copy pasted it from a tutorial (https://blog.wplauncher.com/create-wordpress-plugin-settings-page/)
	//i just know it makes a plugin dashboard and settings menu for me

	public function addPluginAdminMenu() {
		//adds the option for the settings to the sidebar
		add_menu_page(  $this->plugin_name, 'Multi Insta Feed', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-camera', 26 );
		
		//adds a page to display when you click the sidebar button
		add_submenu_page( $this->plugin_name, 'Multi Insta Feed Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
	}

	public function displayPluginAdminDashboard() {
		//connects the display file for the dashboard and displays the html there
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
  	}

	public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		//connects to the display file for settings page
		//also displays error message if there is
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'pluginNameSettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
	}

	public function pluginNameSettingsMessages($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 
				$err_code = esc_attr( 'plugin_name_example_setting' );                 
				$setting_field = 'plugin_name_example_setting';                 
				break;
		}
		$type = 'error';
		add_settings_error(
			   $setting_field,
			   $err_code,
			   $message,
			   $type
		   );
	}

	public function registerAndBuildFields() {
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */     
		add_settings_section(
			// ID used to identify this section and with which to register options
			'plugin_name_general_section', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
			array( $this, 'plugin_name_display_general_account' ),    
			// Page on which to add this section of options
			'plugin_name_general_settings'                   
		);
		unset($args);
		$args = array (
					'type'      => 'input',
					'subtype'   => 'text',
					'id'    => 'plugin_name_example_setting',
					'name'      => 'plugin_name_example_setting',
					'required' => 'true',
					'get_options_list' => '',
					'value_type'=>'normal',
					'wp_data' => 'option'
				);
		add_settings_field(
			'plugin_name_example_setting',
			'Example Setting',
			array( $this, 'plugin_name_render_settings_field' ),
			'plugin_name_general_settings',
			'plugin_name_general_section',
			$args
		);


		register_setting(
			'plugin_name_general_settings',
			'plugin_name_example_setting'
		);

	}

	public function plugin_name_display_general_account() {
		echo '<p>These settings apply to all Plugin Name functionality.</p>';
	} 

	public function plugin_name_render_settings_field($args) {
		/* EXAMPLE INPUT
				  'type'      => 'input',
				  'subtype'   => '',
				  'id'    => $this->plugin_name.'_example_setting',
				  'name'      => $this->plugin_name.'_example_setting',
				  'required' => 'required="required"',
				  'get_option_list' => "",
					'value_type' = serialized OR normal,
		'wp_data'=>(option or post_meta),
		'post_id' =>
		*/     
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox'){
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
				}
				break;
			default:
				# code...
				break;
		}
	}

	

}