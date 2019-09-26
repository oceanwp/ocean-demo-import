<?php
/**
 * Plugin Name:			Ocean Demo Import
 * Plugin URI:			https://oceanwp.org/extension/ocean-demo-import/
 * Description:			Import the OceanWP demo content, widgets and customizer settings with one click.
 * Version:				1.0.11
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	4.0.0
 * Tested up to:		5.2
 *
 * Text Domain: ocean-demo-import
 * Domain Path: /languages/
 *
 * @package Ocean_Demo_Import
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Ocean_Demo_Import to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Ocean_Demo_Import
 */
function Ocean_Demo_Import() {
	return Ocean_Demo_Import::instance();
} // End Ocean_Demo_Import()

Ocean_Demo_Import();

/**
 * Main Ocean_Demo_Import Class
 *
 * @class Ocean_Demo_Import
 * @version	1.0.0
 * @since 1.0.0
 * @package	Ocean_Demo_Import
 */
final class Ocean_Demo_Import {
	/**
	 * Ocean_Demo_Import The single instance of Ocean_Demo_Import.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $widget_areas = array() ) {
		$this->token 			= 'ocean-demo-import';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.11';

		define( 'ODI_PATH', $this->plugin_path );
		define( 'ODI_URL', $this->plugin_url );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	/**
	 * Main Ocean_Demo_Import Instance
	 *
	 * Ensures only one instance of Ocean_Demo_Import is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Ocean_Demo_Import()
	 * @return Main Ocean_Demo_Import instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'ocean-demo-import', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

    /**
     * Display admin notice
     *
     * @since   1.2.6
     */
    public static function admin_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        } ?>
        
        <div class="notice notice-warning">
            <p><?php echo esc_html__( 'All free demos have been incorporated to the latest version of the Ocean Extra plugin, so the Ocean Demo Import plugin can be removed from your website.', 'ocean-extra' ); ?></p>
        </div>
    <?php
    }

} // End Class

#--------------------------------------------------------------------------------
#region Freemius
#--------------------------------------------------------------------------------

if ( ! function_exists( 'ocean_demo_import_fs' ) ) {
    // Create a helper function for easy SDK access.
    function ocean_demo_import_fs() {
	    global $ocean_demo_import_fs;

	    if ( ! isset( $ocean_demo_import_fs ) ) {
		    $ocean_demo_import_fs = OceanWP_EDD_Addon_Migration::instance( 'ocean_demo_import_fs' )->init_sdk( array(
			    'id'              => '3811',
			    'slug'            => 'ocean-demo-import',
			    'public_key'      => 'pk_28285e0f391b4955f4460589da147',
			    'is_premium'      => false,
			    'is_premium_only' => false,
			    'has_paid_plans'  => false,
		    ) );
	    }

	    return $ocean_demo_import_fs;
    }

    function ocean_demo_import_fs_addon_init() {
        if ( class_exists( 'Ocean_Extra' ) ) {
            OceanWP_EDD_Addon_Migration::instance( 'ocean_demo_import_fs' )->init();
        }
    }

    if ( 0 == did_action( 'owp_fs_loaded' ) ) {
        // Init add-on only after parent theme was loaded.
        add_action( 'owp_fs_loaded', 'ocean_demo_import_fs_addon_init', 15 );
    } else {
        if ( class_exists( 'Ocean_Extra' ) ) {
            /**
             * This makes sure that if the theme was already loaded
             * before the plugin, it will run Freemius right away.
             *
             * This is crucial for the plugin's activation hook.
             */
            ocean_demo_import_fs_addon_init();
        }
    }
}

#endregion
