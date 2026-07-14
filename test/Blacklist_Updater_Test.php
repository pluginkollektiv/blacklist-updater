<?php
/**
 * Test cases for Blacklist_Updater.
 *
 * @package BlockListUpdater
 */

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Blacklist_Updater.
 */
class Blacklist_Updater_Test extends TestCase {

	/**
	 * Test activation hook.
	 */
	public function test_activation_hook() {
		delete_site_option( 'blacklist_updater' );
		wp_clear_scheduled_hook( 'blacklist_updater_refresh_data' );

		if ( ! defined( 'BLACKLIST_UPDATER_EVENT' ) ) {
			define( 'BLACKLIST_UPDATER_EVENT', 'blacklist_updater_refresh_data' );
		}

		Blacklist_Updater::activation_hook();

		self::assertSame(
			array( 'etag' => null ),
			get_site_option( 'blacklist_updater' ),
			'site option was not added'
		);
		self::assertSame(
			'twicedaily',
			get_scheduled_event( 'blacklist_updater_refresh_data' )['recurrence'],
			'update hook was not scheduled as expected'
		);
	}

	/**
	 * Test deactivation hook.
	 */
	public function test_deactivation_hook() {
		wp_schedule_event( 0, 'twicedaily', 'blacklist_updater_refresh_data' );

		Blacklist_Updater::deactivation_hook();

		self::assertNull(
			get_scheduled_event( 'blacklist_updater_refresh_data' ),
			'update hook was not unscheduled'
		);
	}

	/**
	 * Test uninstallation hook.
	 */
	public function test_uninstall_hook() {
		update_site_option( 'blacklist_updater', 'my value' );
		self::assertSame( 'my value', get_site_option( 'blacklist_updater' ) );
		Blacklist_Updater::uninstall_hook();
		self::assertNull(
			get_site_option( 'blacklist_updater', null ),
			'option was not deleted on uninstall'
		);
	}

	/**
	 * Test refresh data routine.
	 */
	public function test_refresh_data() {
		global $http_request;

		$GLOBALS['wp_version'] = '5.5';

		update_option( 'blacklist_keys', 'old value' );
		update_option( 'disallowed_keys', 'old value' );
		update_site_option( 'blacklist_updater', array( 'etag' => null ) );

		mock_http_response(
			200,
			'test value',
			array( 'etag' => '"47d1d8273710fd6f6a5995fac1a0983fe0e8828c288e35e80450ddc5c4412def"' )
		);

		// Update for the first time.
		Blacklist_Updater::refresh_data();

		self::assertSame(
			array(
				'url'  => 'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
				'args' => array(),
			),
			$http_request,
			'unexpected HTTP request'
		);
		self::assertSame(
			'test value',
			get_option( 'disallowed_keys' ),
			'disallowed_keys option was not updated as expected'
		);
		self::assertSame(
			array( 'etag' => '"47d1d8273710fd6f6a5995fac1a0983fe0e8828c288e35e80450ddc5c4412def"' ),
			get_site_option( 'blacklist_updater' ),
			'valid etag was not stored in options'
		);

		// Update again without changes.
		mock_http_response(
			304,
			'',
			array( 'etag' => '"47d1d8273710fd6f6a5995fac1a0983fe0e8828c288e35e80450ddc5c4412def"' )
		);

		Blacklist_Updater::refresh_data();

		self::assertSame(
			array(
				'url'  => 'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
				'args' => array(
					'headers' => array(
						'If-None-Match' => '"47d1d8273710fd6f6a5995fac1a0983fe0e8828c288e35e80450ddc5c4412def"',
					),
				),
			),
			$http_request,
			'unexpected HTTP request'
		);
		self::assertSame(
			'test value',
			get_option( 'disallowed_keys' ),
			'disallowed_keys option was changed unexpectedly'
		);
		self::assertSame(
			array( 'etag' => '"47d1d8273710fd6f6a5995fac1a0983fe0e8828c288e35e80450ddc5c4412def"' ),
			get_site_option( 'blacklist_updater' ),
			'valid etag was not stored in options'
		);

		// Third update with changes.
		mock_http_response(
			200,
			'new value',
			array( 'etag' => 'W/"9c51d0"' )
		);

		Blacklist_Updater::refresh_data();

		self::assertSame(
			array(
				'url'  => 'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
				'args' => array(
					'headers' => array(
						'If-None-Match' => '"47d1d8273710fd6f6a5995fac1a0983fe0e8828c288e35e80450ddc5c4412def"',
					),
				),
			),
			$http_request,
			'unexpected HTTP request'
		);
		self::assertSame(
			'new value',
			get_option( 'disallowed_keys' ),
			'disallowed_keys option was not updated'
		);
		self::assertSame(
			'old value',
			get_option( 'blacklist_keys' ),
			'blacklist_keys option should not be changed on WP 5.5'
		);
		self::assertSame(
			array( 'etag' => 'W/"9c51d0"' ),
			get_site_option( 'blacklist_updater' ),
			'valid (weak) etag was not updated'
		);

		// Fourth update with changes and invalid etag.
		mock_http_response(
			200,
			'value #4',
			array( 'etag' => 'invalid' )
		);

		Blacklist_Updater::refresh_data();

		self::assertSame(
			array(
				'url'  => 'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
				'args' => array(
					'headers' => array(
						'If-None-Match' => 'W/"9c51d0"',
					),
				),
			),
			$http_request,
			'unexpected HTTP request'
		);
		self::assertSame(
			'value #4',
			get_option( 'disallowed_keys' ),
			'disallowed_keys option was not updated'
		);
		self::assertSame(
			array( 'etag' => null ),
			get_site_option( 'blacklist_updater' ),
			'invalid etag was not removed'
		);

		// Legacy option for WP 5.4 and lower.
		$GLOBALS['wp_version'] = '5.4';
		Blacklist_Updater::refresh_data();
		self::assertSame(
			'value #4',
			get_option( 'blacklist_keys' ),
			'blacklist_keys option was not updated on WP 5.4'
		);
	}
}
