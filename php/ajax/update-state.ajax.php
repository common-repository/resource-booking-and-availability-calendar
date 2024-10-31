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
File:			update-state.ajax.php
Author: 		cbolson.com 
Script: 		availability calendar
Version: 		3.03
Url: 			http://www.ajaxavailabilitycalendar.com
Date Created: 	2010-02-06

Use:			Update item state in administration

Receives:		$_POST["cur_state"] 		- current item state (1 or 0)
				$_POST["type"]			- the db table
				$_POST["id_item"]		- id of item to modify
				$_POST["field"]			- state field (eg "state")
*/
/***********************************************/


//	admin only access
$admin_only=true;

// include common file for ajax settings
$the_file=dirname(__FILE__)."/ajax-common.inc.php";
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
else		require_once($the_file);


//	define vars
$cur_state	=	$_POST["cur_state"];
$table		=	$_POST["type"];
$id_item	=	$_POST["id_item"];
$field		=	$_POST["field"];

if($cur_state==1) 	$new_state=0;
else 				$new_state=1;

//	check we have data
if($cur_state=="") 	die("KO - cur state");
if($table=="") 		die("KO - type");
if($id_item=="") 	die("KO - id item");

$update="UPDATE ".$table." SET ".$field."='".$new_state."' WHERE id=".$id_item." LIMIT 1";
mysql_query($update) or die("Error updating state");
echo $new_state;
?>