<?php

/*
Plugin Name: Creditable Paywall
Description: Creditable Paywall Integrates pay-per-article into your website. Easily monetize your website by charging readers to pay with universal credits (micropayments)
Version: 1.0.6
Author:  eValue8 New Media Applications
Author URI: https://www.evalue8.nl
License: GPLv2 or later
*/

namespace creditablepaywall;

use creditablepaywall\classes\CreditablepaywallController;

defined( 'ABSPATH' ) || exit;

define( "CREDITABLEPAYWALL_BASE_DIR", plugin_dir_path( __FILE__ ) );
define( "CREDITABLEPAYWALL_TEMPLATES", plugin_dir_path( __FILE__ ) . "templates/" );
define( "CREDITABLEPAYWALL_DIR_URL", plugin_dir_url( __FILE__ ) );
define( "CREDITABLEPAYWALL_VERSION", '1.0.6' );

require_once dirname( __FILE__ ) . '/Autoloader.php';
Autoloader::register( 'creditablepaywall\\' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

class Creditablepaywall {
	const PREFIX = 'creditablepaywall';
	const SETTINGS_OPTION_KEY = 'creditablepaywall_settings';

	private static $instances = [];
    private static $result = null;

	protected function __construct() {
		$this->initHooks();
	}

	public function initHooks() {
		add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );
		add_action( 'admin_menu', [ __CLASS__, 'activation_redirect' ] );
        self::blockAssets();
		add_filter( 'the_content',[ __CLASS__, 'add_creditablepaywall' ] , 7 );
		add_action('wp_head', function(){
            if(is_admin() || !has_block( 'creditablepaywall/paywall' )){
                return;
            }
            $creditable = CreditablepaywallController::getCreditable();

            wp_enqueue_style('creditable-paywall-header-style', $creditable->getCssDependency());
        });
        add_action('wp_footer', function(){
            if(is_admin() || !has_block( 'creditablepaywall/paywall' )){
                return;
            }
            if (self::$result === null) {
                self::$result = CreditablepaywallController::getPaywallCheck();
            }
            $result = self::$result;
            $creditable = CreditablepaywallController::getCreditable();
            if($result && !$result->isPaid()){
                $settings = Creditablepaywall::get_settings();
                wp_register_script('creditable-paywall-footer-script', $creditable->getJsDependency() );
                wp_add_inline_script('creditable-paywall-footer-script', "const cUid = '" . esc_js($result->getUid()) . "';");
                wp_add_inline_script('creditable-paywall-footer-script', "var cAff_id = '" . esc_js($settings['aff_id']) . "';");
                wp_enqueue_script('creditable-paywall-footer-script');
            }
        });
	}

    public static function blockAssets(){
	    wp_register_script('creditablepaywallblock', CREDITABLEPAYWALL_DIR_URL . 'build/index.js', ['wp-blocks', 'wp-element']);
	    wp_register_style('creditablepaywallblock-css', CREDITABLEPAYWALL_DIR_URL . 'build/index.css');
        register_block_type("creditablepaywall/paywall",[
            'editor_script' => 'creditablepaywallblock',
            'editor_style' => 'creditablepaywallblock-css',
            'render_callback'   => [__CLASS__,'theHTML']
        ]);
	}

	public static function add_creditablepaywall($content){
	    if(is_admin()){
	        return;
        }

        if ( has_block( 'creditablepaywall/paywall' ) ) {
            if ( strpos($content,'<!-- wp:creditablepaywall/paywall /-->') === false ) {
                return $content;
            }
        } else {
            return $content;
        }
        if (self::$result === null) {
            self::$result = CreditablepaywallController::getPaywallCheck();
        }
        $result = self::$result;

        if($result && $result->isPaid()){
            return $content;
        }

        $lock_svg = plugins_url( 'images/lock-paywall.svg', __FILE__ );
        $settings = Creditablepaywall::get_settings();

        $paywall_title = isset( $settings['paywall_title'] ) ? $settings['paywall_title'] : __( 'Pay-per-article to continue reading', 'creditable-paywall' );
		$paywall_description = isset( $settings['paywall_description'] ) ? $settings['paywall_description'] : __( 'Pay for this article to access to the rest of this post.', 'creditable-paywall' );

		$access_heading =  sprintf(
            esc_html__( '%s', 'creditable-paywall' ),
            esc_html( $paywall_title )
        );
		
		$subscribe_text = sprintf(
            esc_html__( '%s', 'creditable-paywall' ),
            esc_html( $paywall_description )
        );
		
        $paywalled_content = '
<!-- wp:group {"style":{"border":{"width":"1px","radius":"4px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"}}},"borderColor":"primary","className":"jetpack-subscribe-paywall","layout":{"type":"constrained","contentSize":"400px"}} -->
<div class="wp-block-group subscribe-paywall has-border-color has-primary-border-color" style="border-width:1px;border-radius:4px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px">
<!-- wp:image {"align":"center","width":24,"height":24,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image aligncenter size-large is-resized"><img src="' . $lock_svg . '" alt="" width="24" height="24"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"24px"},"layout":{"selfStretch":"fit"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="font-size:24px;font-style:normal;font-weight:600">' . $access_heading . '</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px"},"spacing":{"margin":{"top":"10px","bottom":"10px"}}}} -->
<p class="has-text-align-center" style="margin-top:10px;margin-bottom:10px;font-size:14px">' . $subscribe_text . '</p>
<!-- /wp:paragraph -->

<div id="creditable-container" class="creditable-container">
  <!-- the button -->
  <div id="creditable-button" style="text-align: center;"></div>
  <!-- popup window -->
  <div id="creditable-window"></div>
</div>

</div>
<!-- /wp:group -->
';
        return strstr( $content, '<!-- wp:creditablepaywall/paywall /-->', true ) . $paywalled_content;
    }


    public static function theHTML($block_attributes) {
        return '';
    }

	public static function getInstance() {
		$cls = static::class;
		if ( ! isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new static();
		}

		return self::$instances[ $cls ];
	}

    public static function activation_redirect(){
        if ( get_option( 'creditablepaywall_activation_redirect', false ) ) {
            delete_option( 'creditablepaywall_activation_redirect' );
            // Redirect to your plugin's settings page upon activation
            wp_redirect( admin_url( 'options-general.php?page=creditablepaywall_settings' ) );
            exit;
        }
    }

	public static function add_settings_page() {
		add_submenu_page( 'options-general.php', __( 'Creditable Paywall Settings', 'creditable-paywall' ), __( 'Creditable Paywall', 'creditable-paywall' ), 'activate_plugins', 'creditablepaywall_settings', array(
			__CLASS__,
			'render_settings'
		) );
	}

	public static function get_settings() {
		$settings = get_option( self::SETTINGS_OPTION_KEY );
		return $settings;
	}

	public static function render_settings() {
// Check if the form has been submitted
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'creditablepaywall_settings_save', 'creditablepaywall_settings_nonce' ) ) {
			// Assume $_POST['settings'] is an array of your settings.
			// Validate and sanitize each setting
			$sanitized_settings = [];
			if ( isset( $_POST['creditablepaywall_settings'] ) && !empty($_POST['creditablepaywall_settings']) && is_array( $_POST['creditablepaywall_settings'] ) ) {
				foreach ( $_POST['creditablepaywall_settings'] as $key => $value ) {
					// Sanitize the setting value
					// We don't sanitize as we need the full html
					// Use wp_unslash to remove any magic quotes added to the string
					$value                      = wp_unslash( $value );
					$sanitized_settings[ $key ] = sanitize_text_field($value);
				}
			}


			// Save the sanitized settings
			self::save_settings( $sanitized_settings );

			// Optionally, add an admin notice for feedback
			add_settings_error( 'creditablepaywall_settings', 'creditablepaywall_settings_saved', __( 'Settings saved.', 'creditable-paywall' ), 'updated' );
		}

		// Always prepare to show the settings form, including after saving
		settings_errors( 'creditablepaywall_settings' ); // Display any settings errors registered by add_settings_error()

		include CREDITABLEPAYWALL_TEMPLATES . 'settings.php';
	}

	/*
	 * Custom hook for wpdatatables
	 */

	/**
	 * Save plugin settings.
	 *
	 * @param array $settings Associative array of settings to save.
	 */
	public static function save_settings( array $settings ) {
		update_option( self::SETTINGS_OPTION_KEY, $settings );
	}

}



register_activation_hook( __FILE__,  function (){
    update_option( 'creditablepaywall_activation_redirect', true );
});

//###################

add_action( 'init', function () {
	global $creditablepaywall;
	$creditablepaywall = Creditablepaywall::getInstance();
} );


