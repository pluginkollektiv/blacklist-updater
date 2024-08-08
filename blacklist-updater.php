<?php
/**
 * Plugin Name: Block List Updater
 * Description: Automatic updating of the comment block list in WordPress with antispam keys from "Comment Blocklist for WordPress" (on GitHub).
 * Author:      pluginkollektiv
 * Author URI:  https://pluginkollektiv.org
 * Plugin URI:  https://wordpress.org/plugins/blacklist-updater/
 * Text Domain: blacklist-updater
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html für
 * Version:     1.0.1
 *
 * @package BlockListUpdater
 */

/*
Copyright (C)  2014-2015 Sergej Müller

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/* Quit */
defined( 'ABSPATH' ) || exit;

/* Constants */
define(
	'BLACKLIST_UPDATER_BASE',
	plugin_basename( __FILE__ )
);
define(
	'BLACKLIST_UPDATER_EVENT',
	'blacklist_updater_refresh_data'
);

/* Initialize the plugin. */
add_action( 'plugins_loaded', array( 'Blacklist_Updater', 'init' ), 99 );

/* Register */
register_activation_hook(
	__FILE__,
	array(
		'Blacklist_Updater',
		'activation_hook',
	)
);
register_deactivation_hook(
	__FILE__,
	array(
		'Blacklist_Updater',
		'deactivation_hook',
	)
);
register_uninstall_hook(
	__FILE__,
	array(
		'Blacklist_Updater',
		'uninstall_hook',
	)
);

/* Hooks */
add_action(
	BLACKLIST_UPDATER_EVENT,
	array(
		'Blacklist_Updater',
		'refresh_data',
	)
);
add_filter(
	'plugin_row_meta',
	array(
		'Blacklist_Updater',
		'plugin_meta',
	),
	10,
	2
);

/* Autoload */
spl_autoload_register( 'blacklist_updater_autoload' );

/**
 * Plugin autoloader.
 *
 * @param string $class The classname.
 */
function blacklist_updater_autoload( $class ) {
	if ( in_array( $class, array( 'Blacklist_Updater' ) ) ) {
		require_once sprintf(
			'%s/inc/class-%s.php',
			dirname( __FILE__ ),
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}
