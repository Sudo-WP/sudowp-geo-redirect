<?php
/**
 * Plugin Name: SudoWP Geo Redirect
 * Plugin URI:  https://sudowp.com
 * Description: A secure, modernized geo-redirection tool based on Geolify. Features PHP 8.2 strict typing and security hardening.
 * Version:     2.1.0
 * Author:      SudoWP
 * Author URI:  https://sudowp.com
 * Text Domain: sudowp-geo-redirect
 * License:     GPLv2 or later
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Class for SudoWP Geo Redirect
 */
final class SudoWP_Geo_Redirect {

	/**
	 * Option key for database storage
	 */
	private const OPTION_KEY = 'sudowp_geo_redirect_settings';

	/**
	 * Singleton Instance
	 */
	private static ?SudoWP_Geo_Redirect $instance = null;

	/**
	 * Initialize the plugin
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		// Admin Hooks
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );

		// Frontend Hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_geo_scripts' ) );
	}

	/**
	 * Register Admin Menu
	 */
	public function add_admin_menu(): void {
		add_menu_page(
			'SudoWP Geo Redirect',
			'Geo Redirect',
			'manage_options',
			'sudowp_geo_redirect',
			array( $this, 'options_page_html' ),
			'dashicons-globe',
			80
		);
	}

	/**
	 * Initialize Settings
	 */
	public function settings_init(): void {
		register_setting( 'sudowp_geo_redirect_group', self::OPTION_KEY, array( $this, 'sanitize_settings' ) );

		add_settings_section(
			'sudowp_geo_section',
			'',
			null, // No callback needed for empty section intro
			'sudowp_geo_redirect'
		);

		add_settings_field(
			'geo_redirect_ids',
			__( 'Geo Redirect IDs (comma separated)', 'sudowp-geo-redirect' ),
			array( $this, 'render_field_ids' ),
			'sudowp_geo_redirect',
			'sudowp_geo_section',
			array( 'label_for' => 'geo_redirect_ids', 'key' => 'ids' )
		);

		add_settings_field(
			'geo_redirect_v2_ids',
			__( 'Geo Redirect V2 IDs (comma separated)', 'sudowp-geo-redirect' ),
			array( $this, 'render_field_ids' ),
			'sudowp_geo_redirect',
			'sudowp_geo_section',
			array( 'label_for' => 'geo_redirect_v2_ids', 'key' => 'v2_ids' )
		);
	}

	/**
	 * Sanitize input settings
	 */
	public function sanitize_settings( array $input ): array {
		$sanitized = array();
		$sanitized['ids'] = isset( $input['ids'] ) ? sanitize_text_field( $input['ids'] ) : '';
		$sanitized['v2_ids'] = isset( $input['v2_ids'] ) ? sanitize_text_field( $input['v2_ids'] ) : '';
		return $sanitized;
	}

	/**
	 * Render Input Fields
	 */
	public function render_field_ids( array $args ): void {
		$options = get_option( self::OPTION_KEY );
		$value   = isset( $options[ $args['key'] ] ) ? $options[ $args['key'] ] : '';
		?>
		<input type='text' 
			   name='<?php echo esc_attr( self::OPTION_KEY . '[' . $args['key'] . ']' ); ?>' 
			   value='<?php echo esc_attr( $value ); ?>' 
			   class="regular-text"
			   placeholder="e.g. 1234, 5678">
		<p class="description"><?php esc_html_e( 'Enter the IDs provided by Geolify, separated by commas.', 'sudowp-geo-redirect' ); ?></p>
		<?php
	}

	/**
	 * Admin Options Page HTML
	 */
	public function options_page_html(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<div class="notice notice-info inline">
				<p>
					<strong>Getting Started:</strong> 
					Create your redirects at <a href="https://geolify.com" target="_blank" rel="noopener noreferrer">Geolify.com</a> and paste the IDs below.
				</p>
			</div>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'sudowp_geo_redirect_group' );
				do_settings_sections( 'sudowp_geo_redirect' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue Frontend Scripts securely
	 */
	public function enqueue_geo_scripts(): void {
		$options = get_option( self::OPTION_KEY );
		
		if ( ! $options || ! is_array( $options ) ) {
			return;
		}

		// Handle V1 IDs
		if ( ! empty( $options['ids'] ) ) {
			$ids = explode( ',', preg_replace( '/\s+/', '', $options['ids'] ) );
			foreach ( array_filter( $ids ) as $id ) {
				if ( ! is_numeric( $id ) ) continue; // Security check: Ensure ID is numeric
				
				wp_enqueue_script(
					'sudowp-geo-' . $id,
					'https://www.geolify.com/georedirect.php?id=' . $id,
					array(),
					null,
					false
				);
			}
		}

		// Handle V2 IDs
		if ( ! empty( $options['v2_ids'] ) ) {
			$ids_v2 = explode( ',', preg_replace( '/\s+/', '', $options['v2_ids'] ) );
			
			// Secure Referrer Handling
			$referer = '';
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				$referer = esc_url_raw( $_SERVER['HTTP_REFERER'] );
			}

			foreach ( array_filter( $ids_v2 ) as $id ) {
				if ( ! is_numeric( $id ) ) continue; // Security check

				$script_url = add_query_arg(
					array(
						'refurl' => urlencode( $referer ), // Encode for safety
						'id'     => $id,
					),
					'https://www.geolify.com/georedirectv2.php'
				);

				wp_enqueue_script(
					'sudowp-geo-v2-' . $id,
					$script_url,
					array(),
					null,
					false
				);
			}
		}
	}
}

// Boot the plugin
SudoWP_Geo_Redirect::get_instance();