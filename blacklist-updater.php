<?php
/*
Plugin Name: Blacklist Updater
Text Domain: blacklist_updater
Domain Path: /lang
Description: Automatic updating of the <a href='options-discussion.php'>comment blacklist</a> in WordPress with antispam keys from <a href='https://github.com/splorp/wordpress-comment-blacklist' target='_blank'>GitHub</a>.
Author: Sergej M&uuml;ller
Author URI: http://wpcoder.de
Plugin URI: https://wordpress.org/plugins/blacklist-updater/
License: GPLv2 or later
Version: 0.0.3
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
defined('ABSPATH') OR exit;


/* Constants */
define(
    'BLACKLIST_UPDATER_BASE',
    plugin_basename(__FILE__)
);
define(
    'BLACKLIST_UPDATER_EVENT',
    'blacklist_updater_refresh_data'
);


/* Register */
register_activation_hook(
    __FILE__,
    array(
        'Blacklist_Updater',
        'activation_hook'
    )
);
register_deactivation_hook(
    __FILE__,
    array(
        'Blacklist_Updater',
        'deactivation_hook'
    )
);
register_uninstall_hook(
    __FILE__,
    array(
        'Blacklist_Updater',
        'uninstall_hook'
    )
);


/* Hooks */
add_action(
    BLACKLIST_UPDATER_EVENT,
    array(
        'Blacklist_Updater',
        'refresh_data'
    )
);
add_filter(
    'plugin_row_meta',
    array(
        'Blacklist_Updater',
        'plugin_meta'
    ),
    10,
    2
);


/* Autoload */
spl_autoload_register('blacklist_updater_autoload');

function blacklist_updater_autoload($class) {
    if ( in_array( $class, array('Blacklist_Updater') ) ) {
        require_once(
            sprintf(
                '%s/inc/%s.class.php',
                dirname(__FILE__),
                strtolower($class)
            )
        );
    }
}