# Blacklist Updater #
* Contributors:      pluginkollektiv
* Tags:              spam, antispam, comments, discussion, blacklist
* Donate link:       https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=TD4AMD2D8EMZW
* Requires at least: 3.8
* Tested up to:      5.4
* Stable tag:        trunk
* License:           GPLv2 or later
* License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Automatic updating of the comment blacklist in WordPress with antispam keys from GitHub.

## Description ##
Few users are familiar with the comment blacklist built into WordPress. Located in the WordPress admin area under “Settings”—“Discussion”, that blacklist for incoming comments accepts values (words) to identify spam by.

Additionally to plugins like [Antispam Bee](https://wordpress.org/plugins/antispam-bee/) in order to fight spam successfully a curated comment blacklist is recommendable. You can either update the list manually, or utilize a very detailed global [comment blacklist](https://github.com/splorp/wordpress-comment-blacklist) that gets updated on a regular basis.

Blacklist Updater has been developed to keep your comment blacklist in your WordPress installation up to speed with the curated global list on GitHub.

The plugin will check the global comment blacklist on GitHub multiple times a day. Whenever new anti-spam values have been added to the global list, Blacklist Updater will read the global list and update your WordPress database accordingly. While the check-up process will run several times a day, the plugin will only update the database when it detects an actual change of the global comment blacklist on GitHub.

### Support ###
* Community support via the [support forums on wordpress.org](https://wordpress.org/support/plugin/blacklist-updater)
* We don’t handle support via e-mail, Twitter, GitHub issues etc.

### Contribute ###
* Active development of this plugin is handled [on GitHub](https://github.com/pluginkollektiv/blacklist-updater).
* Pull requests for documented bugs are highly appreciated.
* If you think you’ve found a bug (e.g. you’re experiencing unexpected behavior), please post at the [support forums](https://wordpress.org/support/plugin/blacklist-updater) first.
* If you want to help us translate this plugin you can do so [on WordPress Translate](https://translate.wordpress.org/projects/wp-plugins/blacklist-updater).

### Credits ###
* Author: [Sergej Müller](https://sergejmueller.github.io/)
* Maintainers: [pluginkollektiv](https://pluginkollektiv.org/)

## Installation ##
* If you don’t know how to install a plugin for WordPress, [here’s how](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

### Requirements ###
* PHP 5.2.4 or greater
* WordPress 3.8 or greater

## Changelog ##

### 0.0.5 ###
* Implement automated deployment

### 0.0.4 ###
* Update donation link

### 0.0.3 ###
* WordPress 4.2 support
* Russian translation

### 0.0.2 ###
* Plugin refactoring
* Moving to wordpress.org

### 0.0.1 ###
* *Blacklist Updater* goes live
