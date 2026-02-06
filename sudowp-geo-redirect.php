<?php
/**
 * Plugin Name: SudoWP Geo Redirect
 * Plugin URI:  https://sudowp.com
 * Description: A secure, modernized geo-redirection tool based on Geolify. Features PHP 8.2 strict typing and security hardening.
 * Version:     2.1.1
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
		register_setting(
			'sudowp_geo_redirect_group',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(),
			)
		);

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
	 *
	 * @param array $input Raw input data from form submission.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ): array {
		// Security: Verify user has permission to save settings
		if ( ! current_user_can( 'manage_options' ) ) {
			add_settings_error(
				self::OPTION_KEY,
				'permission_denied',
				__( 'You do not have permission to modify these settings.', 'sudowp-geo-redirect' ),
				'error'
			);
			return get_option( self::OPTION_KEY, array() );
		}

		// Ensure input is an array
		if ( ! is_array( $input ) ) {
			$input = array();
		}

		$sanitized = array();

		// Sanitize and validate V1 IDs
		if ( isset( $input['ids'] ) ) {
			$sanitized['ids'] = $this->sanitize_id_list( $input['ids'], 'ids' );
		} else {
			$sanitized['ids'] = '';
		}

		// Sanitize and validate V2 IDs
		if ( isset( $input['v2_ids'] ) ) {
			$sanitized['v2_ids'] = $this->sanitize_id_list( $input['v2_ids'], 'v2_ids' );
		} else {
			$sanitized['v2_ids'] = '';
		}

		return $sanitized;
	}

	/**
	 * Sanitize and validate a comma-separated list of IDs
	 *
	 * @param string $id_list Raw ID list string.
	 * @param string $field_key Field key for error messages.
	 * @return string Sanitized ID list.
	 */
	private function sanitize_id_list( string $id_list, string $field_key ): string {
		// First sanitize the text
		$id_list = sanitize_text_field( $id_list );
		
		// Remove all whitespace
		$id_list = preg_replace( '/\s+/', '', $id_list );
		
		if ( empty( $id_list ) ) {
			return '';
		}

		// Split by comma and validate each ID
		$ids = explode( ',', $id_list );
		$valid_ids = array();
		$invalid_ids = array();

		foreach ( $ids as $id ) {
			$id = trim( $id );
			if ( empty( $id ) ) {
				continue;
			}

			// Security: Strict validation - only numeric IDs allowed
			if ( ctype_digit( $id ) && (int) $id > 0 ) {
				$valid_ids[] = $id;
			} else {
				$invalid_ids[] = $id;
			}
		}

		// Report invalid IDs to user
		if ( ! empty( $invalid_ids ) ) {
			add_settings_error(
				self::OPTION_KEY,
				'invalid_' . $field_key,
				sprintf(
					/* translators: %s: comma-separated list of invalid IDs */
					__( 'Invalid IDs removed (%s): Only positive numeric values are allowed.', 'sudowp-geo-redirect' ),
					esc_html( implode( ', ', $invalid_ids ) )
				),
				'warning'
			);
		}

		return implode( ',', $valid_ids );
	}

	/**
	 * Render Input Fields
	 */
	public function render_field_ids( array $args ): void {
		$options = get_option( self::OPTION_KEY, array() );
		$value   = isset( $options[ $args['key'] ] ) ? $options[ $args['key'] ] : '';
		$field_id = $args['label_for'] ?? '';
		?>
		<input type='text' 
			   id='<?php echo esc_attr( $field_id ); ?>'
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
		// Security: Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have sufficient permissions to access this page.', 'sudowp-geo-redirect' ),
				esc_html__( 'Permission Denied', 'sudowp-geo-redirect' ),
				array( 'response' => 403 )
			);
		}

		// Security: Check if settings were updated
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				self::OPTION_KEY,
				'settings_updated',
				__( 'Settings saved successfully.', 'sudowp-geo-redirect' ),
				'success'
			);
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php settings_errors( self::OPTION_KEY ); ?>
			
			<div class="notice notice-info inline">
				<p>
					<strong><?php esc_html_e( 'Getting Started:', 'sudowp-geo-redirect' ); ?></strong> 
					<?php
					printf(
						/* translators: %s: URL to Geolify.com */
						esc_html__( 'Create your redirects at %s and paste the IDs below.', 'sudowp-geo-redirect' ),
						'<a href="https://geolify.com" target="_blank" rel="noopener noreferrer">Geolify.com</a>'
					);
					?>
				</p>
			</div>

			<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
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
		$options = get_option( self::OPTION_KEY, array() );
		
		if ( ! $options || ! is_array( $options ) ) {
			return;
		}

		// Handle V1 IDs
		if ( ! empty( $options['ids'] ) ) {
			$ids = explode( ',', preg_replace( '/\s+/', '', $options['ids'] ) );
			foreach ( array_filter( $ids ) as $id ) {
				// Security: Strict validation - only positive integers
				if ( ! ctype_digit( $id ) || (int) $id <= 0 ) {
					continue;
				}
				
				// Security: Sanitize ID for use in URL
				$safe_id = absint( $id );
				
				$script_url = add_query_arg(
					array( 'id' => $safe_id ),
					'https://www.geolify.com/georedirect.php'
				);
				
				wp_enqueue_script(
					'sudowp-geo-' . $safe_id,
					esc_url( $script_url ),
					array(),
					null,
					array(
						'in_footer' => false,
						'strategy'  => 'defer',
					)
				);
			}
		}

		// Handle V2 IDs
		if ( ! empty( $options['v2_ids'] ) ) {
			$ids_v2 = explode( ',', preg_replace( '/\s+/', '', $options['v2_ids'] ) );
			
			// Secure Referrer Handling
			$referer = '';
			if ( isset( $_SERVER['HTTP_REFERER'] ) && is_string( $_SERVER['HTTP_REFERER'] ) ) {
				// Security: Validate and sanitize referrer
				$referer = filter_var( $_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL );
				if ( false !== $referer ) {
					$referer = esc_url_raw( $referer );
				} else {
					$referer = '';
				}
			}

			foreach ( array_filter( $ids_v2 ) as $id ) {
				// Security: Strict validation - only positive integers
				if ( ! ctype_digit( $id ) || (int) $id <= 0 ) {
					continue;
				}

				// Security: Sanitize ID for use in URL
				$safe_id = absint( $id );

				$script_url = add_query_arg(
					array(
						'refurl' => rawurlencode( $referer ), // Use rawurlencode for URL parameters
						'id'     => $safe_id,
					),
					'https://www.geolify.com/georedirectv2.php'
				);

				wp_enqueue_script(
					'sudowp-geo-v2-' . $safe_id,
					esc_url( $script_url ),
					array(),
					null,
					array(
						'in_footer' => false,
						'strategy'  => 'defer',
					)
				);
			}
		}
	}
}

// Boot the plugin
SudoWP_Geo_Redirect::get_instance();