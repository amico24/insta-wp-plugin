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

class Multi_Insta_Feeds_Admin
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('init', array($this, 'init_admin_classes'));
		add_action('admin_menu', array($this, 'addPluginAdminMenu'));
		add_shortcode('feed_display', array($this, 'feed_display'));
		add_action('update_option_mif_access_token', array($this, 'update_ig_id'), 10, 3);
	}
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/multi-insta-feeds-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/multi-insta-feeds-admin.js');

	}

	/**
	 * initializes helper classes
	 * @return void
	 */
	public function init_admin_classes()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin\class-multi-insta-feeds-admin-errors.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin\class-multi-insta-feeds-admin-graph-accounts.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin\class-multi-insta-feeds-admin-graph-api.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin\class-multi-insta-feeds-admin-graph-groups.php';
	}

	/**
	 * Second version of shortcode for post display, uses graph api to display business accounts
	 * @param mixed $atts
	 * @param mixed $content
	 * @return string
	 */
	public function feed_display($atts, $content = ""){
		//this is so messy it might be good to move this to its own file
		//problem for future me
		$a = shortcode_atts(
			array(
				'type' => 'accounts',
				'group' => 'ALL', //ALL is default if no group is specified
				'length' => '5' 
			),
			$atts
		);
		$graph_groups = new Multi_Insta_Feeds_Graph_Groups;
		$accounts = new Multi_Insta_Feeds_Graph_Accounts;
		$post_list = array();

		if($a['group']=='ALL'){
			$accounts_to_display = $accounts -> get_accounts();
		} elseif(!isset($a['group']) || $a['group'] >= $graph_groups->get_total_groups() || $a['group'] < 0){
			$accounts_to_display = null;
		} else {
			$accounts_to_display = $graph_groups->get_user_list($a['group']);
		}

		//check if group exists
		if ($accounts_to_display == null) {
			return '
			<p>Group not found.</p>
			';
		} else {
			if ($a['type'] == 'posts') {
				//put 5 most recent posts on every account on $post_list
				foreach ($accounts_to_display as $account) {
					array_push($post_list, $accounts->get_media_list($account, $a['length']));
				}
				//flatten array
				$post_list = array_merge(...$post_list);

				//sort posts by date (copy/pasted lmao idk how this works)
				$date_compare = function ($a, $b) {
					$t1 = strtotime($a['timestamp']);
					$t2 = strtotime($b['timestamp']);
					return $t2 - $t1;
				};
				usort($post_list, $date_compare);


				//get 5 most recent posts
				$post_list = array_slice($post_list, 0, $a['length']);

				//build html for posts
				$html_snippet = "";
				
				foreach (array_keys($post_list) as $post_index) {

					if ($post_list[$post_index]['media_type'] == 'VIDEO') {
						$media_snippet = '
						<video height="200" width="200" muted autoplay loop controls>
							<source src="'.$post_list[$post_index]['media_url'].'" type="video/mp4">
							Video not found.
					  	</video> 
						';
					} elseif($post_list[$post_index]['media_type'] == 'IMAGE' || $post_list[$post_index]['media_type'] == 'CAROUSEL_ALBUM') {
						$media_snippet = '
						<img src="' . $post_list[$post_index]['media_url'] . '" height="200" width="200">
						';
					}
					$html_snippet .= '
						<div>
							<p>' . $post_list[$post_index]['username'] . '</p>
							'.$media_snippet.'
						</div>
					';
				}

				return '
				<div style="display: flex; gap: 5px;">
					' . $html_snippet . '
				</div>
				';
			} elseif ($a['type'] == 'accounts') {
				//build html for account
				$html_snippet = "";

				foreach ($accounts_to_display as $user) {
					$bio = $accounts->get_account($user)['biography'] ?? ''; //in case of no bio
					$html_snippet .= '
					<a href="https://www.instagram.com/' . $user . '" target="_blank" style="text-decoration: none; color: initial;" >
						<div style="text-align: center; width:150px; border: 1px solid #000000;" >
							<img src = "' . $accounts->get_account($user)['profile_picture_url'] . '" width = "150">
							<p style = "font-size: large;">' . $accounts->get_account($user)['name'] . '</p>
							<p style = "font-size: medium; color: rgb(87, 87, 87);">' . $accounts->get_account($user)['username'] . '</p>
							<p style = "font-size: small;">' . $bio . '</p>
						</div>
					</a>
					
					';
				}
				return '
				<div style="display: flex; gap: 5px; align-items:flex-start;">
					' . $html_snippet . '
				</div>
				';
			} else {
				return 'Feed type not found.';
			}
		}
	}

	/**
	 * Grabs Admins Instagram User ID whenever access token is updated
	 * @param mixed $old_value
	 * @param mixed $value
	 * @param mixed $option
	 * @return void
	 */
	public function update_ig_id($old_value, $value, $option)
	{
		$graph_api = new Multi_Insta_Feeds_Graph_API;
		$graph_api->retrieve_ig_id();
	}


	//since im not using the settings api here (mostly bc i dont understand how it works) I just deleted most of the stuff here and only kept the minimum needed to display everything

	public function addPluginAdminMenu()
	{
		//adds the option for the settings to the sidebar
		add_menu_page($this->plugin_name, 'Multi Insta Feed', 'administrator', $this->plugin_name, array($this, 'displayPluginAdminDashboard'), 'dashicons-camera', 26);

		//adds a page to display when you click the sidebar button
		add_submenu_page($this->plugin_name, 'Multi Insta Feed Settings', 'Settings', 'administrator', $this->plugin_name . '-settings', array($this, 'displayPluginAdminSettings'));
	}

	public function displayPluginAdminDashboard()
	{
		//connects the display file for the dashboard and displays the html there
		require_once 'partials/' . $this->plugin_name . '-admin-display.php';
	}

	public function displayPluginAdminSettings()
	{
		//connects to the display file for settings page
		require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
	}
}