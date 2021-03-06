<?php
/**
 * Plugin Name:     Easy Digital Downloads - Hide Button Prices
 * Plugin URI:      http://wordpress.org/plugins/easy-digital-downloads-hide-button-prices/
 * Description:     Removes prices from purchase buttons on Easy Digital Downloads
 * Version:         1.0.4
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-hide-button-prices
 *
 * @package         EDD\HideButtonPrices
 * @author          Daniel J Griffiths <dgriffiths2section214.com>
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'EDD_Hide_Button_Prices' ) ) {


	/**
	 * Main EDD_Hide_Button_Prices class
	 *
	 * @since       1.0.1
	 */
	class EDD_Hide_Button_Prices {


		/**
		 * @var         EDD_Hide_Button_Prices $instance The one true EDD_Hide_Button_Prices
		 * @since       1.0.1
		 */
		private static $instance;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      object self::$instance The one true EDD_Hide_Button_Prices
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new EDD_Hide_Button_Prices();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Override prices
			add_filter( 'edd_purchase_link_defaults', array( $this, 'hide_button_prices' ) );
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'EDD_Hide_Button_Prices_lang_dir', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-hide-button-prices', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-hide-button-prices/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-hide-button-prices/ folder
				load_textdomain( 'edd-hide-button-prices', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-hide-button-prices/languages/ folder
				load_textdomain( 'edd-hide-button-prices', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-hide-button-prices', false, $lang_dir );
			}
		}


		/**
		 * Hide button prices
		 *
		 * @access          public
		 * @since           1.0.1
		 * @param           array $defaults
		 * @return          array
		 */
		public function hide_button_prices( $defaults ) {
			$defaults['price'] = (bool) false;

			return $defaults;
		}
	}
}


/**
 * The main function responsible for returning the one true EDD_Hide_Button_Prices
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Hide_Button_Prices The one true EDD_Hide_Button_Prices
 */
function edd_hide_button_prices() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if( ! class_exists( 'S214_EDD_Activation' ) ) {
			require_once( 'includes/class.s214-edd-activation.php' );
		}

		$activation = new S214_EDD_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();
	} else {
		return EDD_Hide_Button_Prices::instance();
	}
}
add_action( 'plugins_loaded', 'edd_hide_button_prices' );
