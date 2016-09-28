=== SQL Executioner ===
Contributors: justincwatt, olarmarius
Donate link: http://justinsomnia.org/2008/02/the-wordpress-sql-executioner/
Tags: phpMyAdmin, MySQL, query, SQL, DBA, database, database administration, admin, CSV
Requires at least: 3.0
Tested up to: 4.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Execute arbitrary SQL queries against your WordPress database from the Admin.

== Description ==

Instead of needing a tool like [phpMyAdmin](http://www.phpmyadmin.net/home_page/index.php) 
or the mysql command line client to view and modify your WordPress database, 
the SQL Executioner allows you to run arbitrary SQL queries against your 
WordPress database from within the Admin. In many cases this allows you to bypass
the inherent limitations of the WordPress Admin interface, and use the full expressive
power of SQL to analyze and update your blog's database.

To use simply install and visit the Tools > SQL Executioner page.

If you're interested in contributing to the code behind this plugin, it's also hosted on GitHub:
https://github.com/justincwatt/wp-sql-executioner

== Installation ==

Extract the zip file, drop the sql-executioner folder in your wp-content/plugins/ 
directory, and then activate from the Plugins page.

== Frequently Asked Questions ==

= Does this plugin have any undo? =

No. It executes SQL queries directly against your database. If in doubt, run a `SELECT` 
query before attempting an `UPDATE` or `DELETE` query to confirm what
you are about to modify.

= Can I irreparably damage my WordPress database using this plugin? =

Yes.

= Do I have to know SQL to use this plugin? =

Yes. Other than a basic facility that will `DESCRIBE` each table for you, there 
is no GUI (graphical user interface).

== Screenshots ==

1. This is what you get after describing the posts table.

== Changelog ==
= 1.4 =
* Added CSV export format (hat tip: olarmarius)

= 1.3 =
* Updated to work with PHP 7

= 1.2 =
* Cleaned code up to submit to WP Plugin Repo
* Wrapped functions in class

= 1.1 =
* Add wp_nonce_field check, minor code cleanup

= 1.0 =
* Initial version

== Upgrade Notice ==
= 1.3 =
Compatibility Update: supports PHP 7

= 1.1 =
Security Update: added wp_nonce_field check

= 1.0 =
Initial version
