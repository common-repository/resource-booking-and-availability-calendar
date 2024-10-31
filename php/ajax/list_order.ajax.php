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
File:			list_order.ajax.php
Author: 		cbolson.com 
Script: 		availability calendar
Version: 		3.03
Url: 			http://www.ajaxavailabilitycalendar.com
Date Created: 	2009-07-29   
Date Modified: 	2010-01-30

Use:			Update item orders in administration

Receives:		$_REQUEST["type"] 		- database table
				$_REQUEST["sort_order"]	- order of items
				$_REQUEST["order_field"]- db table that stores the order field
*/
/***********************************************/


//	admin only access
$admin_only=true;

// include common file for ajax settings
$the_file=dirname(__FILE__)."/ajax-common.inc.php";
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
else		require_once($the_file);


//	define request vars
$the_table	=	$_GET["type"];
$sort_order	=	$_GET["sort_order"];
$order_field=	$_GET["order_field"];


//	check we have all the data
if( ($the_table=="") || ($sort_order=="") ){
	die("Error with datasss<br>".print_r($_GET));
}

//	split items
$ids = explode('|',$sort_order);
//print_r($ids);	
//	run the update query for each id
foreach($ids as $index=>$id){
	if($id != ''){
		$sql = "UPDATE ".$the_table." SET ".$order_field."=".$index." WHERE id = ".$id." LIMIT 1";
		mysql_query($sql) or die(mysql_error().'<br>'.$sql);
	}
}
?>