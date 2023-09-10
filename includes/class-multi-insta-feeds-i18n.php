<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Multi_Insta_Feeds
 * @subpackage Multi_Insta_Feeds/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Multi_Insta_Feeds
 * @subpackage Multi_Insta_Feeds/includes
 * @author     Cyrus Kael Abiera <cyrus.abiera@activ8bn.com>
 */
class Multi_Insta_Feeds_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'multi-insta-feeds',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
