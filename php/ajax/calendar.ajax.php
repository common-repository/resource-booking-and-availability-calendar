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

if (!function_exists('add_action'))
{
	require_once("../../../../../wp-config.php");
}

$the_file = '../en.lang.php';
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);


$the_file = '../functions.inc.php';
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);

//	define variables
$id_item	= $_REQUEST["id_item"];
$the_month	= $_REQUEST["month"];
$the_year	= $_REQUEST["year"];

//	required data
if($id_item=="") 	die("no item defined");
if($the_month=="") 	die("no month defined");
if($the_year=="") 	die("no year defined");

// create the calendar
echo draw_cal($id_item,$the_month,$the_year);
?>