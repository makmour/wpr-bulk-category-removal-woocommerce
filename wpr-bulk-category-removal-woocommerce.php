<?php
/**
 * Plugin Name:       WPRepublic Bulk Category Removal for WooCommerce
 * Plugin URI:        https://github.com/makmour/wpr-bulk-category-removal-woocommerce
 * Description:       A suite of professional tools for WooCommerce. Includes a cleanup utility to safely bulk delete products and orphaned data.
 * Version:           1.1.0
 * Author:            WP Republic
 * Author URI:        https://wprepublic.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpr-bulk-category-removal-woocommerce
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Tested up to:      6.9
 * Requires PHP:      7.4
 * WC requires at least: 6.0
 * WC tested up to:      8.3
 * WC Blocks:           true
 * Requires Plugins:    woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WPR_BULK_CATEGORY_REMOVAL_VERSION', '1.1.0' );
define( 'WPR_BULK_CATEGORY_REMOVAL_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPR_BULK_CATEGORY_REMOVAL_URL', plugin_dir_url( __FILE__ ) );

/**
 * The main plugin class.
 */
final class WPR_Bulk_Category_Removal {

	/**
	 * The single instance of the class.
	 *
	 * @var WPR_Bulk_Category_Removal|null
	 */
	private static $_instance = null;

	/**
	 * Ensures only one instance of the class is loaded.
	 *
	 * @return WPR_Bulk_Category_Removal
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		// Check if WooCommerce is active.
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_woocommerce' ] );
			return;
		}

		// Load core components.
		$this->includes();

		// Instantiate classes.
		if ( $this->is_request( 'admin' ) ) {
			new WPR_Bulk_Category_Removal_Admin();
		}

		// WP-CLI integration.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'wpr-bulk-category-removal-woocommerce', 'WPR_Bulk_Category_Removal_CLI' );
		}
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		require_once WPR_BULK_CATEGORY_REMOVAL_PATH . 'includes/class-wpr-bulk-category-removal-woocommerce-core.php';
		require_once WPR_BULK_CATEGORY_REMOVAL_PATH . 'includes/class-wpr-bulk-category-removal-woocommerce-admin.php';

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once WPR_BULK_CATEGORY_REMOVAL_PATH . 'includes/class-wpr-bulk-category-removal-woocommerce-cli.php';
		}
	}

	/**
	 * Check the type of request.
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();

			case 'ajax':
				return defined( 'DOING_AJAX' ) && DOING_AJAX;

			case 'cron':
				return defined( 'DOING_CRON' ) && DOING_CRON;

			case 'frontend':
				return ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) && ! ( defined( 'DOING_CRON' ) && DOING_CRON );
		}

		return false;
	}

	/**
	 * Admin notice for missing WooCommerce.
	 */
	public function notice_missing_woocommerce() {
		?>
		<div class="notice notice-error is-dismissible">
			<p>
				<strong><?php esc_html_e( 'WPRepublic Bulk Category Removal for WooCommerce', 'wpr-bulk-category-removal-woocommerce' ); ?></strong>
				<?php esc_html_e( 'requires WooCommerce to be installed and activated.', 'wpr-bulk-category-removal-woocommerce' ); ?>
			</p>
		</div>
		<?php
	}
}

/**
 * Begins execution of the plugin.
 *
 * @return WPR_Bulk_Category_Removal
 */
function wpr_bulk_category_removal() {
	return WPR_Bulk_Category_Removal::instance();
}

// Let's go!
wpr_bulk_category_removal();