<?php
/*
Plugin Name: Login Log
Plugin URI: http://iran98.org/category/wordpress/login-log/
Description: Show IP, browser and time last login.
Version: 1.0
Author: Mostafa Soufi
Author URI: http://iran98.org/
License: GPL2
*/
load_plugin_textdomain('login_log','wp-content/plugins/login-log/lang');
register_activation_hook(__FILE__,'loging_log_install');
register_activation_hook(__FILE__,'login_log_install_data');
global $login_log_db_version;
$login_log_db_version = "1.0";

function loging_log_install() {
	global $login_log_db_version;
	$table_name = $wpdb->prefix . "wp_login_log";
	$sql = "CREATE TABLE " . $table_name . " (
	  id int,
	  PRIMARY KEY(id),
	  time datetime NOT NULL,
	  useragent longtext,
	  ip longtext NOT NULL
    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
 
	add_option("login_log_db_version", $login_log_db_version);
}

echo "<style>
	p.ip_login{
		background-color: #FFFFFF;
		padding: 5px;
		margin: 5px;
		text-align: center;
	}

	p.last_notices{
		font-size: 11px;
		padding-left: 10px;
		padding-right: 10px;
		margin-bottom: 0px;
	}
</style>";

function login_log_install_data() {
	global $wpdb;
	$get_table_id	=	"SELECT id FROM wp_login_log";
	$get_id			=	$wpdb->query($get_table_id);
	$get_id++;
	$get_time		=	current_time('mysql');		
	$get_user_agent	=	$_SERVER['HTTP_USER_AGENT'];	
	$get_ip			=	$_SERVER['REMOTE_ADDR'];
	$insert = "INSERT INTO wp_login_log(id, time, useragent, ip) VALUES ('$get_id', '$get_time', '$get_user_agent', '$get_ip')";
	$results = $wpdb->query($insert);
	echo "<p class='ip_login' align='center'><img title='Your ip: $get_ip' src='".get_bloginfo('wpurl')."/wp-content/plugins/login-log/img/ip.png' /></p>";
}
	add_action('login_form', 'login_log_install_data');

function last_login() {
	global $wpdb;
	$get_last_ip = $wpdb->get_var( $wpdb->prepare("SELECT ip FROM wp_login_log") );
	$get_last_agent = $wpdb->get_var( $wpdb->prepare("SELECT useragent FROM wp_login_log") );
	$get_last_time = $wpdb->get_var( $wpdb->prepare("SELECT time FROM wp_login_log") );
	echo "<p class='last_notices'>";
		_e('Last IP:', 'login_log'); echo " $get_last_ip ";
		_e('By browser:', 'login_log'); echo " $get_last_agent ";
		_e('On:', 'login_log'); echo " $get_last_time ";
}
	add_action('admin_notices', 'last_login');

function all_login() {
	global $wpdb;

	$select_ip = "SELECT ip FROM wp_login_log LIMIT 3";
	$select_ip = $wpdb->get_col($select_ip);

	$select_time = "SELECT time FROM wp_login_log LIMIT 3";
	$select_time = $wpdb->get_col($select_time);

	$select_useragent = "SELECT useragent FROM wp_login_log LIMIT 3";
	$select_useragent = $wpdb->get_col($select_useragent);

	echo "<table>";
		echo "<tr>";
			echo "<td>" .__('IP', 'login_log'). ":</td>";
		foreach ($select_ip as $get_select_ip) {
			echo "<td>$get_select_ip</td>"; }
		echo "</tr>";
		
		echo "<tr>";
			echo "<td>". __('Time', 'login_log'). ":</td>";
		foreach ($select_time as $get_select_time) {
			echo "<td>$get_select_time</td>"; }
		echo "</tr>";

		echo "<tr>";
			echo "<td>". __('Browser', 'login_log'). ":</td>";
		foreach ($select_useragent as $get_select_useragent) {
			echo "<td>$get_select_useragent</td>"; }
		echo "</tr>";	
	echo "</table>";
}

function wpll_widget() {
	all_login();
}

function wpll_widget_setting() {
	wp_add_dashboard_widget( 'wpll_widget', __('Login log', 'login_log'), 'wpll_widget' );
}
	add_action('wp_dashboard_setup', 'wpll_widget_setting');
?>