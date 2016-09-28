<?php
/*
Plugin Name: SQL Executioner
Version: 1.4
Plugin URI: http://justinsomnia.org/2008/02/the-wordpress-sql-executioner/
Description: Execute SQL commands on your WordPress database. Goto <a href="tools.php?page=sql-executioner">Tools &gt; SQL Executioner</a> to operate.
Author: Justin Watt
Author URI: http://justinsomnia.org/

LICENSE
Copyright 2012-2016 Justin Watt justincwatt@gmail.com

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

new SQL_Executioner_Plugin();
class SQL_Executioner_Plugin {
	const version = 1.3;
	private $db;
	private $tables;

	public function __construct() {
		global $wpdb;

		add_action( 'admin_init', array( $this, 'register_scripts') );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// set up our own db connection so as to not interfer with WordPress'
		$this->db = mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

		// get list of tables and create dollar-sign shortcuts
		$rst = mysqli_query( $this->db, "show tables" );
		while ( $row = mysqli_fetch_array( $rst ) ) {
			$this->tables[$row[0]] = '$' . preg_replace( "/^$wpdb->prefix/", '', $row[0] );
		}		
	}

	public function add_admin_menu() {
		$page = add_management_page( 'SQL Executioner', 'SQL Executioner', 'manage_options', 'sql-executioner', array( $this, 'admin_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_scripts' ) );
	}

	public function admin_page() {
		$sql = '';
		$results = array();

		if ( isset( $_POST['sql'] ) ) {
			// We stripslashes here because WordPress forcefully adds
			// magic quotes in wp-settings.php, regardless of get_magic_quotes_gpc(),
			$sql = trim( stripslashes( $_POST['sql'] ) );
			$results = $this->execute_sql( $sql );
		}

		require_once( 'form.php' );
		require_once( 'csv.php' );
	}

	public function register_scripts() {
		wp_register_style( 'sql-executioner', plugins_url( 'style.css', __FILE__ ), array(), self::version );
		wp_register_script( 'sql-executioner', plugins_url( 'script.js', __FILE__ ), array(), self::version );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'sql-executioner' );
		wp_enqueue_script( 'sql-executioner' );
	}	

	public function execute_sql($sql) {
		if ( !check_admin_referer( 'sql-executioner-submit' ) )
			return false;

		$results = array();
		$results['rows'] = array();

		// interpolate real table names for dollar-sign abbreviated "stubs"
		foreach ( $this->tables as $table_name => $table_stub ) {
			$sql = str_replace( $table_stub, $table_name, $sql );
		}
		$results['sql'] = $sql;

		if ( $rst = mysqli_query( $this->db, $sql ) ) {

			if ( preg_match( "/^\s*(alter|create|drop|rename|insert|delete|update|replace|truncate) /i", $sql ) ) {
				$results['affected_rows'] = mysqli_affected_rows( $this->db );
			} else {
				$first = true;
				while ( $row = mysqli_fetch_assoc( $rst ) ) {
					if ( $first ) {
						$results['rows'][] = array_keys( $row );
						$first = false;
					}
					$results['rows'][] = array_values( $row );
				}
			}
			
		} else {
			$results['error'] = mysqli_error( $this->db );
		}

		return $results;
	}

	// From: https://gist.github.com/johanmeiring/2894568
	public static function str_putcsv($input, $delimiter = ',', $enclosure = '"') {
		$fp = fopen( 'php://temp', 'r+b' );
		fputcsv( $fp, $input, $delimiter, $enclosure );
		rewind( $fp );
		$data = rtrim(stream_get_contents( $fp ), "\n" );
		fclose( $fp );
		return $data;
	}
}
