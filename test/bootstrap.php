<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package BlockListUpdater
 */

/**
 * Simulating the ABSPATH constant.
 *
 * @var boolean ABSPATH
 */
const ABSPATH = false;

// Include class files.
require_once __DIR__ . '/../inc/class-blacklist-updater.php';

// Mock options handling.
$options      = array();
$site_options = array();

function get_site_option( $option, $default_value = false, $deprecated = true ) {
	global $site_options;

	if ( isset( $site_options[ $option ] ) ) {
		return $site_options[ $option ];
	}

	return $default_value;
}

function get_option( $option, $default_value = false, $deprecated = true ) {
	global $options;

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default_value;
}

function update_option( $option, $value, $autoload = null ) {
	global $options;

	if ( isset( $options[ $option ] ) && $options[ $option ] === $value ) {
		return false;
	}
	$options[ $option ] = $value;

	return true;
}

function add_site_option( $option, $value ) {
	return update_site_option( $option, $value );
}

function update_site_option( $option, $value, $autoload = null ) {
	global $site_options;

	if ( isset( $site_options[ $option ] ) && $site_options[ $option ] === $value ) {
		return false;
	}
	$site_options[ $option ] = $value;

	return true;
}

function delete_option( $option ) {
	global $options;

	if ( isset( $options[ $option ] ) ) {
		unset( $options[ $option ] );

		return true;
	}

	return false;
}

function delete_site_option( $option ) {
	global $site_options;

	if ( isset( $site_options[ $option ] ) ) {
		unset( $site_options[ $option ] );

		return true;
	}

	return false;
}

// Mock wp_remote_get()

$http_request  = array();
$http_response = array();

function wp_remote_get( $url, $args = array() ) {
	global $http_request;
	global $http_response;

	$http_request = array(
		'url'  => $url,
		'args' => $args,
	);

	return $http_response;
}

function wp_remote_retrieve_response_code( $response ) {
	if ( is_array( $response ) ) {
		return $response['response']['code'];
	}

	return '';
}

function wp_remote_retrieve_body( $response ) {
	if ( is_array( $response ) ) {
		return $response['body'];
	}

	return '';
}

function wp_remote_retrieve_header( $response, $header ) {
	if ( is_array( $response ) && isset( $response['headers'][ $header ] ) ) {
		return $response['headers'][ $header ];
	}

	return '';
}

function is_wp_error( $thing ) {
	return false;
}

function mock_http_response( $code, $body, $headers = array() ) {
	global $http_response;

	$http_response = array(
		'response' => array(
			'code' => $code,
		),
		'body'     => $body,
		'headers'  => $headers,
	);
}

// Mock WP scheduling.
$schedule_events = array();

function wp_schedule_event( $timestamp, $recurrence, $hook, $args = array(), $wp_error = false ) {
	global $schedule_events;

	$schedule_events[ $hook ] = array(
		'timestamp'  => $timestamp,
		'recurrence' => $recurrence,
	);

	return true;
}

function wp_clear_scheduled_hook( $hook, $args = array(), $wp_error = false ) {
	global $schedule_events;

	if ( isset( $schedule_events[ $hook ] ) ) {
		unset( $schedule_events[ $hook ] );

		return 1;
	}

	return 0;
}

function get_scheduled_event( $hook ) {
	global $schedule_events;

	if ( isset( $schedule_events[ $hook ] ) ) {
		return $schedule_events[ $hook ];
	}

	return null;
}
