<?php


/* Quit */
defined('ABSPATH') OR exit;


/**
* Blacklist_Updater
*
* @since 0.0.2
*/

class Blacklist_Updater
{


    /**
    * Plugin activation hook
    *
    * @since   0.0.2
    * @change  0.0.2
    */

    public static function activation_hook()
    {
        add_site_option(
            'blacklist_updater',
            array(
                'etag' => null
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

    public static function deactivation_hook()
    {
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

    public static function uninstall_hook()
    {
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

    public static function refresh_data()
    {
        /* Plugin options */
        $options = get_site_option(
            'blacklist_updater'
        );

        /* Etag header */
        if ( ! empty($options['etag']) ) {
            $args = array(
                'headers' => array(
                    'If-None-Match' => $options['etag']
                )
            );
        } else {
            $args = array();
        }

        /* Output debug infos */
        if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
            error_log('Comment Blacklist requested from GitHub');
        }

        /* Start request */
        $response = wp_remote_get(
            'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
            $args
        );

        /* Exit on error */
        if ( is_wp_error($response) ) {
            if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
                error_log('Comment Blacklist response error: ' .$response->get_error_message());
            }

            return;
        }

        /* Check response code */
        if ( wp_remote_retrieve_response_code($response) !== 200 ) {
            if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
                error_log('Comment Blacklist is up to date');
            }

            return;
        }

        /* Output debug infos */
        if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
            error_log('Comment Blacklist successfully downloaded');
        }

        /* Update blacklist */
        update_option(
            'blacklist_keys',
            wp_remote_retrieve_body($response)
        );

        /* Output debug infos */
        if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
            error_log('Comment Blacklist successfully updated');
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
                'etag' => $etag
            )
        );
    }


    /**
    * Plugin meta rows
    *
    * @since   0.0.2
    * @change  0.0.2
    *
    * @param   array   $input  Exists plugin rows
    * @param   string  $file   Current plugin file
    * @return  array           Extended plugin rows
    */

    public static function plugin_meta($input, $file)
    {
        /* Skip other plugins */
        if ( $file !== BLACKLIST_UPDATER_BASE ) {
            return $input;
        }

        /* Next update time */
        if ( $timestamp = wp_next_scheduled( BLACKLIST_UPDATER_EVENT ) ) {
            $scheduled = human_time_diff( time(), $timestamp );
        } else {
            $scheduled = esc_html('Never');
        }

        /* Plugin rows */
        return array_merge(
            $input,
            array(
                '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=ZAQUT9RLPW8QN" target="_blank">PayPal</a>',
                '<a href="https://flattr.com/t/1323822" target="_blank">Flattr</a>',
                sprintf(
                    '%s %s',
                    esc_html__('Next check in', 'blacklist_updater'),
                    $scheduled
                )
            )
        );
    }
}