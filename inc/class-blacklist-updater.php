<?php
/**
 * Main plugin class
 *
 * @package BlockListUpdater
 */

/* Quit */
defined( 'ABSPATH' ) || exit;

/**
 * Blacklist_Updater
 *
 * @since 0.0.2
 */
class Blacklist_Updater {
	/**
	 * This is the original option key as it was used by WordPress up until
	 * version 5.4.
	 *
	 * @var string
	 */
	const OLD_OPTION_KEY = 'blacklist_keys';

	/**
	 * Option key for storing the list of disallowed keys in the options
	 * database.
	 *
	 * This new name is in use from WordPress 5.5 onwards.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'disallowed_keys';

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Load translations. Required due to support for WP versions before 4.6.
		load_plugin_textdomain( 'blacklist-updater' );
	}

	/**
	 * Plugin activation hook
	 *
	 * @since   0.0.2
	 * @change  0.0.2
	 */
	public static function activation_hook() {
		add_site_option(
			'blacklist_updater',
			array(
				'etag' => null,
			)
		);

		wp_schedule_event(
			time(),
			'twicedaily',
			BLACKLIST_UPDATER_EVENT
		);
	}

	/**
	 * Plugin deactivation hook
	 *
	 * @since   0.0.2
	 * @change  0.0.2
	 */
	public static function deactivation_hook() {
		wp_clear_scheduled_hook(
			BLACKLIST_UPDATER_EVENT
		);
	}

	/**
	 * Plugin uninstall hook
	 *
	 * @since   0.0.2
	 * @change  0.0.2
	 */
	public static function uninstall_hook() {
		delete_site_option(
			'blacklist_updater'
		);
	}

	/**
	 * Get blacklist from Github and update WordPress field
	 *
	 * @since   0.0.2
	 * @change  0.0.2
	 */
	public static function refresh_data() {
		/* Plugin options */
		$options = get_site_option(
			'blacklist_updater'
		);

		/* Etag header */
		if ( ! empty( $options['etag'] ) ) {
			$args = array(
				'headers' => array(
					'If-None-Match' => $options['etag'],
				),
			);
		} else {
			$args = array();
		}

		/* Output debug infos */
		if ( defined( 'WP_DEBUG_LOG ' ) && WP_DEBUG_LOG ) {
			error_log( 'Comment block list requested from GitHub' );
		}

		/* Start request */
		$response = wp_remote_get(
			'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
			$args
		);

		/* Exit on error */
		if ( is_wp_error( $response ) ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'Comment block list response error: ' . $response->get_error_message() );
			}

			return;
		}

		/* Check response code */
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'Comment block list is up to date' );
			}

			return;
		}

		/* Output debug infos */
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'Comment block list successfully downloaded' );
		}

		/* Update list of disallowed keys */
		update_option(
			version_compare( $GLOBALS['wp_version'], '5.5', '>=' )
				? self::OPTION_KEY
				: self::OLD_OPTION_KEY,
			wp_remote_retrieve_body( $response )
		);

		/* Output debug infos */
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'Comment block list successfully updated' );
		}

		/* Get & validate Etag */
		$etag = preg_replace(
			'/^[a-f0-9"]$/',
			'',
			wp_remote_retrieve_header(
				$response,
				'etag'
			)
		);

		/* Update options */
		update_site_option(
			'blacklist_updater',
			array(
				'etag' => $etag,
			)
		);
	}

	/**
	 * Plugin meta rows
	 *
	 * @since   0.0.2
	 * @change  0.0.2
	 *
	 * @param   array  $input  existing plugin rows.
	 * @param   string $file   plugin file.
	 * @return  array          updated plugin rows
	 */
	public static function plugin_meta( $input, $file ) {
		/* Skip other plugins */
		if ( BLACKLIST_UPDATER_BASE !== $file ) {
			return $input;
		}

		/* Next update time */
		$timestamp = wp_next_scheduled( BLACKLIST_UPDATER_EVENT );
		if ( $timestamp ) {
			$scheduled = sprintf(
				/* translators: %s: time until next check */
				esc_html__( 'Next check for a new block list in %s', 'blacklist-updater' ),
				human_time_diff( time(), $timestamp )
			);
		} else {
			$scheduled = esc_html__( 'No check for a new block list scheduled', 'blacklist-updater' );
		}

		/* Plugin rows */
		return array_merge(
			$input,
			array(
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=TD4AMD2D8EMZW" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Donate', 'blacklist-updater' ) . '</a>',
				'<a href="https://wordpress.org/support/plugin/blacklist-updater" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Support', 'blacklist-updater' ) . '</a>',
				$scheduled,
			)
		);
	}
}
