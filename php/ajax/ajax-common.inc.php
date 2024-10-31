<?php

/*
Plugin Name: Resource Booking and Availability Calendar
Plugin URI: http://cstart.blogspot.com/2010/05/ajax-booking-availability-calendar.html
Description: Wordpress plugin for Resource booking and availability. 
Supports, resource booking on per author/subscriber basis.
Version: 1.0
Author: Raghavendra Deshpande
Author URI: http://cstart.blogspot.com
License: GPL2

 Copyright 2010  Raghavendra Deshpande  (email : raghu.deshpande@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/***********************************************/
/*
File:	ajax-common.inc.php
Author: cbolson.com 
Script: availability calendar
Version:3.03
Url: 	http://www.ajaxavailabilitycalendar.com
Date 	Created: 2010-01-30
Use: 	Inlcuded in all ajax files
		Defines settings, connects to db, includes common files
*/
/***********************************************/
//	activate this to prevent the url from being accessed via the url
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	//	only allow ajax requests - no calling from the url
	header("location",$_SERVER["DOCUMENT_ROOT"]);
}

if(isset($admin_only)){
	//	some ajax pages should only be reached via the admin panel
	
	session_start();
	//	check only admin allowed -  no direct calls
	if(!isset($_SESSION["admin_id"])){
		die("KO");
	}
}
//	check we are getting fresh info and define charset
header("Cache-Control: private, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: private");
header('Content-type: text/html; charset=utf-8');


//define("DIR_ROOT", str_replace("ajax","",dirname(__FILE__)));	# this doesn't work on my version due to the string "ajax" in the url
//define("AC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/calendar_v3.2/");
define("AC_ROOT", str_replace("ac-includes/ajax","",dirname(__FILE__)));


	
//	calendar functions
$the_file=AC_INLCUDES_ROOT."functions.inc.php";
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
else		require_once($the_file);
	
	
//	define language
if(!isset($_REQUEST["lang"])) $_REQUEST["lang"]=AC_DEFAULT_LANG;
define("LANG", $_REQUEST["lang"]);

//	include lang file
$the_file=AC_DIR_LANG.LANG.".lang.php";
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
else		require_once($the_file);
?>