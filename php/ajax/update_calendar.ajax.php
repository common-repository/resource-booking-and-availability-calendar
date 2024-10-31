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

//	admin only access
$admin_only=true;

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


//	define request vars
$the_date	=	$_GET["the_date"];
$id_item	=	$_GET["id_item"];

//	clear cache to ensure data is up to date
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past


//	check we have all the data
if( ($_REQUEST["id_item"]=="") || ($_REQUEST["the_date"]=="")){
	die("Error");
}

//$debug=true;

$registry =& csRBARegistry::getInstance();
$bookings_table =& $registry->get('bookings_table');
$bookings_states_table =& $registry->get('bookings_states_table');

//	get states in order
$list_states=array();
$sql="SELECT * FROM ".$bookings_states_table." WHERE state=1 ORDER BY id ASC";
$rows=$wpdb->get_results($sql);
foreach ($rows as $row) {
            $list_states[$row->id] = array("class"=>$row->class,"desc"=>$row->desc_en);
        }

if($_GET["id_state"]=="free"){
	//	remove from db
	$update="DELETE FROM ".$bookings_table." WHERE id_item='".$id_item."' AND the_date='".$the_date."' LIMIT 1";
	$new_class="";
	$new_desc	=$lang["available"];
}else{
	
	//	get current state
	$sql="SELECT id_state FROM ".$bookings_table." WHERE id_item='".$id_item."' AND the_date='".$the_date."'";
	$row=$wpdb->get_row($sql);
	
	if($wpdb->num_rows == 0){
		//	new booking - define new state as first in $list_states;
		if($_GET["id_state"]!="")	$new_state	=	$_GET["id_state"];	//	admin has the option to "force" a state for all clicks
		else 						$new_state	=	key($list_states); 	//	should return the key (id) for the first item in the array
		if($debug) echo "<br>New state first key ".$new_state;
		$new_desc	=	$list_states[$new_state]["desc"];
		$new_class	=	$list_states[$new_state]["class"];
		$update		=	"INSERT INTO ".$bookings_table." SET id_item='".$id_item."',the_date='".$the_date."', id_state='".$new_state."'";
	}else {

		if($debug) echo "<br>".print_r($row);
		
		//	need to get next state in order
		$current_state_id=$row->id_state;
		if($debug) echo "<br>Current ID: ".$current_state_id;
		
		//	loop though states array until we find this one
		foreach($list_states as $id=>$val){
			if($id==$current_state_id) break;
			//	advance the pointer to next
	 		next($list_states);
	 		//	stop if id is the same as current
	 		
		}
		
		//	define for db update
		if($_GET["id_state"]!="")	$new_state	=	$_GET["id_state"];
		else 						$new_state	=	key($list_states);
		if($debug) echo "<br>New State: ".$new_state;
		if($new_state==""){
			//	finished array - delete from db
			$update="DELETE FROM ".$bookings_table." WHERE id_item='".$id_item."' AND the_date='".$the_date."' LIMIT 1";
			$new_class="";
			$new_desc	=$lang["available"];
		}else{
			$update="UPDATE ".$bookings_table." SET id_state='".$new_state."' WHERE id_item='".$id_item."' AND the_date='".$the_date."' LIMIT 1";
			//	define for class to return
			$new_desc	=	$list_states[$new_state]["desc"];
			$new_class	=	$list_states[$new_state]["class"];
		}
		
	}
	//echo $update;
	
}

//	update db with new state
$row=$wpdb->get_row($update);


//	update last update with now
//$sql="SELECT * FROM `".T_BOOKING_UPDATE."` WHERE id_item='".$id_item."'";
//if(!$res=mysql_query($sql)) die("ERROR GETTING CHECKING UPDATE DATE.<br>".mysql_error());
//if(mysql_num_rows($res)==0)	$update="INSERT INTO `".T_BOOKING_UPDATE."` SET id_item='".$id_item."', date_mod=now()";
//else						$update="UPDATE `".T_BOOKING_UPDATE."` SET date_mod=now() WHERE id_item='".$id_item."' LIMIT 1";
////echo $update;
//mysql_query($update) or die("Error with last modified date");



if($debug) echo "<br>SQL: ".$update."<br>New Class: ";

//	format date for db modifying - the date is passed via ajax
$date_bits		=	explode("-",$the_date);
        
//	format date for display only
if(DATE_DISPLAY_FORMAT=="us")	$date_format	=	$date_bits[1]."/".$date_bits[2]."/".$date_bits[0];
else 			        			$date_format	=	$date_bits[2]."/".$date_bits[1]."/".$date_bits[0];

echo $new_class."|".$date_format."|".$new_desc;
?>