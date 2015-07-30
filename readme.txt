=== Blacklist Updater ===
Contributors: pluginkollektiv
Tags: spam, antispam, comments, discussion, blacklist
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAQUT9RLPW8QN
Requires at least: 3.8
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Automatic updating of the comment blacklist in WordPress with antispam keys from GitHub.



== Description ==

Few users are familiar with the comment blacklist built into WordPress. Located in the WordPress admin area under “Settings”—“Discussion”, that blacklist for incoming comments accepts values (words) to identify spam by.

Additionally to plugins like [Antispam Bee](https://wordpress.org/plugins/antispam-bee/) in order to fight spam successfully a curated comment blacklist is recommendable. You can either update the list manually, or utilize a very detailed global [comment blacklist](https://github.com/splorp/wordpress-comment-blacklist) that gets updated on a regular basis.

Blacklist Updater has been developed to keep your comment blacklist in your WordPress installation up to speed with the curated global list on GitHub.

The plugin will check the global comment blacklist on GitHub multiple times a day. Whenever new anti-spam values have been added to the global list, Blacklist Updater will read the global list and update your WordPress database accordingly. While the check-up process will run several times a day, the plugin will only update the database when it detects an actual change of the global comment blacklist on GitHub.


= Requirements =
* PHP 5.2.4
* WordPress 3.8


= Memory usage =
* Back-end: ~ 0,04 MB
* Front-end: ~ 0,02 MB


= Languages =
* German
* English
* Русский


= Authors =
* [Sergej Müller](http://wpcoder.de)
* [Caspar Hübinger](http://glueckpress.com)


= Donuts =
* [Flattr](https://flattr.com/thing/536e5e2d0ce8de72eccc08731a4514e6)
* [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAQUT9RLPW8QN)



== Changelog ==

= 0.0.3 / 21.04.2015 =
* WordPress 4.2 support
* Russian translation

= 0.0.2 =
* Plugin refactoring
* Moving to wordpress.org

= 0.0.1 =
* *Blacklist Updater* goes live


== Installation ==

* WordPress Dashboard > Plugins > Add New
* Search for *Blacklist Updater* > Install
* No settings required
