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

define("DATE_DISPLAY_FORMAT", "us");
class csRBARegistry {
    var $_objects = array();

    function &getInstance() {
        static $self;
        if (is_object($self) == true) {
            return $self;
        }
        $self = new csRBARegistry;
        return $self;
    }
    
    function set($name, &$object) {
        $this->_objects[$name] =& $object;
    }

    function &get($name) {
        return $this->_objects[$name];
    }
}

//store db tables in registry
$registry =& csRBARegistry::getInstance();
$bookings_table = $wpdb->prefix . "bookings";
$registry->set('bookings_table', $bookings_table);

$bookings_states_table = $wpdb->prefix . "bookings_states";
$registry->set('bookings_states_table', $bookings_states_table);


//	 create calendar for given month
function draw_cal($id_item,$month,$year){
	global $lang;
	global $wpdb;
	global $post;
	
	
	$month=sprintf("%02s",$month);
	//	define vars
	$today_timestamp	=   mktime(0,0,0,date('m'),date('d'),date('Y'));	# 	current timestamp - used to check if date is in past
	$this_month 		= 	getDate(mktime(0, 0, 0, $month, 1, $year));		# 	convert month to timestamp
	$first_week_day 	= $this_month["wday"];								# 	define first weekday (0-6)  
	$days_in_this_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);	#	define number of days in week
	$day_counter_tot	=	0; #	count total number of days showin INCLUDING previous and next months - use to get 6th row of dates
	
	//	get num days in previous month - used to add dates to "empty" cells
	$month_last	= $month-1;
	$year_last	= $year;
	if($month_last<1){
		$month_last=12;
		$year_last=$year-1;	
	}
	$days_in_last_month = cal_days_in_month(CAL_GREGORIAN,$month_last,$year_last);
	
	//	CREATE THE CALENDAR
	
	//	day column titles - using first letter of each day
	if($show_week_num)	$list_day_titles='<li class="weeknum_spacer"></li>';
	
	if(AC_START_DAY=="sun"){
		//$cal_row_counter=0;
		
		for($k=0; $k<7; $k++){
			$weekday = substr($lang["day_".$k.""],0,1);
			$list_day_titles.='<li class="cal_weekday"> '.$weekday.'</li>';
		}
	}else{
		//$cal_row_counter=1;
		if ($first_week_day == 0)	$first_week_day =7;
		for($k=1; $k<=7; $k++){
			//echo "<br>".$k;
			if($k==7) 	$weekday = substr($lang["day_0"][0],0,1);
			else		$weekday = substr($lang["day_".$k.""],0,1);
			$list_day_titles.='<li title="'.$lang["day_".$k.""].'"> '.$weekday.'</li>';
		}
	}
	
	
	//	Fill the first week of the month with the appropriate number of blanks.       
	$j=1;
	if(AC_START_DAY=="sun")	$first_week_day_start	=	$first_week_day;	# start sunday
	else						$first_week_day			=	$first_week_day-1;	# start monday
	
	if($first_week_day!=7){
		if($show_week_num)	$list_days.='<li class="weeknum">-</li>';
		$last_month_start_num=$days_in_last_month-$first_week_day+1;
		for($week_day = 0; $week_day < $first_week_day; $week_day++){
			$list_days.='<li class="cal_empty">'.$last_month_start_num.'</li>';   
			++$last_month_start_num;
			++$j;
			++$day_counter_tot;
		}
	}
	$week_day=$j;
	
	//	get bookings for this month and item from database
	$booked_days=array();
	$registry =& csRBARegistry::getInstance();
    $bookings_table =& $registry->get('bookings_table');
    $bookings_states_table =& $registry->get('bookings_states_table');
	$sql = "SELECT t1.the_date, t2.class, t2.desc_en AS the_state FROM $bookings_table AS t1 
			LEFT JOIN $bookings_states_table AS t2 ON t2.id=t1.id_state 
			WHERE t1.id_item=".$id_item." AND MONTH(t1.the_date)=".$month." AND YEAR(t1.the_date)=".$year."";
	
//	echo $sql;
//	$sql = "
//	SELECT 
//		t1.the_date,
//		t2.class,
//		t2.desc_".LANG." AS the_state
//	FROM 
//		".T_BOOKINGS." AS t1
//		LEFT JOIN ".T_BOOKING_STATES." AS t2 ON t2.id=t1.id_state
//	WHERE 
//		t1.id_item=".$id_item." 
//		AND MONTH(t1.the_date)=".$month." 
//		AND YEAR(t1.the_date)=".$year."
//	";

	$rows=$wpdb->get_results($sql);
	
//	while($row=mysql_fetch_assoc($res)){
//		$booked_days[$row["the_date"]]=array("class"=>$row["class"],"state"=>$row["the_state"]);
//	}
//	$booked_days["2010-04-26"]=array("class"=>"booked_pr","state"=>"Provisional");
	
	foreach ($rows as $row) {
            $booked_days[$row->the_date] = array("class"=>$row->class,"state"=>$row->the_state);
        }
        
	
	
	
	//	loop thorugh days (til max in month) to draw calendar
	for($day_counter = 1; $day_counter <= $days_in_this_month; $day_counter++){
		
		
		
		
		//	reset xtra classes for each day
		//	note - these classes acumulate for each day according to state, current and clickable
		$day_classes 	=	"";
		$day_title_state=	" - ".$lang["available"];
		
		//	set all dates to clickable for now.... need to control this for admin OR for user side booking		
		$day_classes.=' clickable';
		
		
		//	turn date into timestamp for comparison with current timestamp (defined above)
		$date_timestamp =   mktime(0,0,0, $month,($day_counter),$year);
		
		//	get week number
		$week_num=date("W",$date_timestamp);
		if($week_num!=$last_week_num){
			//	new week
			//$list_days .= '<li>-</li>';
		}
		//	highlight current day
		if($date_timestamp==$today_timestamp)	$day_classes.=' today';
		
		//	format date for db modifying - the date is passed via ajax
		$date_db		=	$year."-".sprintf("%02s",$month)."-".sprintf("%02s",$day_counter);
        
		//raghu
//		$date_db = "2010-04-26";
        //	format date for display only
        if(DATE_DISPLAY_FORMAT=="us")	$date_format	=	$month."/".$day_counter."/".$year;
        else 			        			$date_format	=	$day_counter."/".$month."/".$year;
        
		//	check if day is available
		if(array_key_exists($date_db,$booked_days)){
			$day_classes.=" ".$booked_days[$date_db]["class"];
			$day_title_state=" - ".$booked_days[$date_db]["state"];
		}
					
		
		//	check if date is past			
		if( $date_timestamp<$today_timestamp){
			$day_classes.=" past";	#add "past" class to be modified via mootools if required
			//	overwrite clickable state if CLICKABLE_PAST is off
			if(AC_ACTIVE_PAST_DATES=="off"){
				//	date is previous - strip out "clickable" from classes
				$day_classes=str_replace(' clickable','',$day_classes);
			}
		}
		
		//	add weekend class - used in javascript to alter class or set opacity
		$getdate=getdate($date_timestamp);
		$day_num=$getdate["wday"]+1;
		if ($day_num % 7 == 1)		$day_classes.=' weekend';
		elseif ($day_num % 6 == 1)	$day_classes.=' weekend';
		
		//'.$lang["day_".$getdate["wday"].""].'
		$list_days .= '
		<li class="'.$day_classes.' "  id="'.$date_db.'" title="'.$date_format.$day_title_state.'">'.$day_counter.'</li>';
		
		//	reset weekday counter if 7 (6)
		$week_day %= 7;			#	reset weekday to 0
		++$week_day;			#	increase weekday counter
		++$day_counter_tot;		#	add 1 to total days shown
		//echo "<br>".$week_day;
		if($show_week_num){
			if ($week_day==1) $list_days .= '<li class="weeknum">'.$week_num.'</li>';
		}
		$last_week_num=$week_num;
	}
	//	add empty days till end of row
	$next_month_day=1;
	/*
	if($week_day > 1){
		for($till_day = $week_day; $till_day <=7; $till_day++){
			$list_days .= '<li class="cal_empty">'.$next_month_day.'</li>'; 
			++$next_month_day;  
			++$day_counter_tot;		#	add 1 to total days shown
		}
	}
	*/
	/*
	echo $day_counter_tot % 6;
	//	now check that we have the full six rows...
	if ($day_counter_tot % 6 !=0){
		//	add empty row
		for($till_day = 1; $till_day <=7; $till_day++){
			$list_days .= '<li class="cal_empty">'.$next_month_day.'</li>'; 
			++$next_month_day; 
			++$day_counter_tot;		#	add 1 to total days shown
		}
	}
	*/
	//	add more rows untill we get to 6
	while($day_counter_tot % 6 !=0){
		//add days until it does :)
		for($till_day = $week_day; $till_day <=7; $till_day++){
			$list_days .= '<li class="cal_empty">'.$next_month_day.'</li>'; 
			++$next_month_day;  
			++$day_counter_tot;		#	add 1 to total days shown
		}
		$week_day=1;

	}
	//	add empty dates (with next month numbers) until we get to 7
	if($week_day > 1){
		for($till_day = $week_day; $till_day <=7; $till_day++){
			$list_days .= '<li class="cal_empty">'.$next_month_day.'</li>'; 
			++$next_month_day;  
			++$day_counter_tot;		#	add 1 to total days shown
		}
	}
	
	
	//	put it all together (parent div defined in parent file)
	$the_cal='
	<div id="'.$month.'_'.$year.'" class="cal_title">'.$lang["month_".$month.""].' '.$year.'</div>
	<ul class="cal_weekday">
		'.$list_day_titles.'
	</ul>
	<ul>
		'.$list_days.'
	</ul>
	<div class="clear"></div>
	';
	return $the_cal;
}


function get_cal_update_date($id_item){
	if(DATE_DISPLAY_FORMAT=="us")	$date_format	= "%m-%d-%Y";
	else 								$date_format	= "%d-%m-%Y";
	
	$sql="SELECT DATE_FORMAT(date_mod, '".$date_format."') as date_mod FROM `".T_BOOKING_UPDATE."` WHERE id=".$id_item."";
	$res=mysql_query($sql) or die("error getting last calendar update date");
	$row=mysql_fetch_assoc($res);
	return $row["date_mod"];
}
//	get calendar items for select list
function sel_list_items($id_item_current){
	$list_items="";
	$sql="SELECT id, desc_".LANG." as the_item FROM ".T_BOOKINGS_ITEMS." WHERE state=1 ORDER BY list_order";
	$res=mysql_query($sql) or die("Error checking items");
	while($row=mysql_fetch_assoc($res)){
		$list_items.='<option value="'.$row["id"].'"';
		if($row["id"]==$id_item_current) $list_items.=' selected="selected"';
		$list_items.='>'.$row["the_item"].'</option>';
	}
	return $list_items;
}

function list_numbers($start,$end,$num){
	$list_numbers='';
	for($k=$start;$k<=$end;$k++){
		$list_numbers.='<option value="'.$k.'"';
		if($k==$num) $list_numbers.=' selected="selected"';
		$list_numbers.='>'.$k.'</option>';
	}
	return $list_numbers;
}
//	get item title
function itemTitle($id){
	$sql="SELECT desc_".LANG." as item_title FROM ".T_BOOKINGS_ITEMS." WHERE id=".$id."";
	$res=mysql_query($sql) or die("Error getting item name");
	$row=mysql_fetch_assoc($res);
	return $row["item_title"];
}
?>