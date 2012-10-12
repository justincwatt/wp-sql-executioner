<?php
/*
Plugin Name: SQL Executioner
Version: 1.1
Plugin URI: http://justinsomnia.org/2008/02/the-wordpress-sql-executioner/
Description: Execute SQL commands on your WordPress database. Goto <a href="tools.php?page=sql-executioner.php">Tools &gt; SQL Executioner</a> to operate.
Author: Justin Watt
Author URI: http://justinsomnia.org/

1.1
Add wp_nonce_field check, minor code cleanup

1.0
initial version

LICENSE

wp-sql-executioner.php
Copyright (C) 2012 Justin Watt
justincwatt@gmail.com
http://justinsomnia.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

function add_sql_executioner() {
	// Add a new menu under Manage:
	add_management_page('SQL Executioner', 'SQL Executioner', 'manage_options', __FILE__, 'sql_executioner');
}
add_action('admin_menu', 'add_sql_executioner');


function sql_executioner() 
{
	if (!empty($_POST)) {
		check_admin_referer('sql-executioner-submit');
	}

	global $wpdb;

	// set up our own db connection
	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, true);
	mysql_select_db(DB_NAME, $db);

	// get list of tables and dollar-sign shortcuts
	$rst = mysql_query("show tables", $db);
	while ($row = mysql_fetch_array($rst)) {
		$tables[$row[0]] = '$' . preg_replace("/^$wpdb->prefix/", '', $row[0]);
	}

	// because wordpress forcefully adds magic quotes in wp-settings.php
	// regardless of get_magic_quotes_gpc(), we forcefully stripslashes here
	$sql = trim(stripslashes($_POST['sql']));

	?>
	<div class='wrap'>
		<h2>SQL Executioner</h2>
		<script type="text/javascript">
		
		function submit_desc(table_stub) {
			document.getElementById('sql').value = 'describe ' + table_stub;
			document.forms['sql_executioner'].submit();
		}

		function check_sql() {
			sql = document.getElementById('sql').value;
			if (sql.match(/\s*(alter|create|drop|rename|insert|delete|update|replace|truncate) /i)) {
				return confirm("This query may modify data in your database. There is no undo. Are you sure?");
			} else {
				return true;
			}
		}

		</script>
		<form method="post" name="sql_executioner">
			<?php wp_nonce_field('sql-executioner-submit'); ?>
			<strong>Tables</strong><br />
			<?php 
			$first = true;
			foreach ($tables as $table) {
				if (!$first) {
					print ', ';
				} else {
					$first = false;
				}
				print "<a href='#' onclick='submit_desc(this.innerHTML);return false;' title='Click to describe'>" . htmlentities($table) . "</a>";
			}
			?>
			<br /><br />
			<strong>SQL</strong><br />
			<textarea id='sql' name='sql' rows="6" cols="60" style="width:100%"><?php print htmlentities($sql); ?></textarea><br />
			<p><strong>Use with extreme caution!</strong> The author of this plugin assumes no liability whatsoever for the potential destructive effects of its use.</p>
			<input type="submit" class="button" name="function" value="Execute SQL" onclick='return check_sql();'/>
		</form>
	<?php

	if (!empty($_POST)) {

		// interpolate real table names
		foreach ($tables as $table_name => $table_stub) {
			$sql = str_replace($table_stub, $table_name, $sql);
		}

		print "<br /><strong>Results</strong><br />";
		print "Query: " . htmlentities($sql);
		print "<br />";

		if ($rst = mysql_query($sql, $db)) {

			if (preg_match("/^\s*(alter|create|drop|rename|insert|delete|update|replace|truncate) /i", $sql)) {
			
				print mysql_affected_rows($db);
				if (mysql_affected_rows($db) == 1) {
					print " row affected";
				} else {
					print " rows affected";
				}

			} else {
		 
				print "<div style='width:100%;overflow:auto;padding:2px;'>";
				print "<table border='1' style='border-collapse:collapse;background:#F4F4F4;'>";
				$print_headers = true;
				while ($row = mysql_fetch_assoc($rst)) {
					if ($print_headers) {
						print "<thead>";
						print "<tr>";
						foreach ($row as $name => $value) {
							print "<th>" . htmlentities($name) . "</th>";
						}
						print "</tr>";
						print "</thead>";
						print "<tbody>";
						$print_headers = false;
					}
					
					print "<tr>";
					foreach ($row as $value) {
						print "<td>" . htmlentities($value) . "</td>";
					}
					print "</tr>";
				}
				print "</tbody>";
				print "</table>";
				print "</div>";
			}
			
		} else {
			print "Error: " . htmlentities(mysql_error($db));
		}
	}
	print "</div>";
}
