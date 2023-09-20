<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sample.com
 * @since             1.0.0
 * @package           Multi_Insta_Feeds
 *
 * @wordpress-plugin
 * Plugin Name:       Multi Insta Feeds
 * Plugin URI:        https://sample.com
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Cyrus Kael Abiera
 * Author URI:        https://sample.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       multi-insta-feeds
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
define( 'MULTI_INSTA_FEEDS_VERSION', '2.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-multi-insta-feeds-activator.php
 */
function activate_multi_insta_feeds() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-multi-insta-feeds-activator.php';
	Multi_Insta_Feeds_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-multi-insta-feeds-deactivator.php
 */
function deactivate_multi_insta_feeds() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-multi-insta-feeds-deactivator.php';
	Multi_Insta_Feeds_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_multi_insta_feeds' );
register_deactivation_hook( __FILE__, 'deactivate_multi_insta_feeds' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-multi-insta-feeds.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_multi_insta_feeds() {

	$plugin = new Multi_Insta_Feeds();
	$plugin->run();

}
run_multi_insta_feeds();
