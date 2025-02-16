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

$lang=array();
$lang["month_01"]	=	"January";
$lang["month_02"]	=	"February";
$lang["month_03"]	=	"March";
$lang["month_04"]	=	"April";
$lang["month_05"]	=	"May";
$lang["month_06"]	=	"June";
$lang["month_07"]	=	"July";
$lang["month_08"]	=	"August";
$lang["month_09"]	=	"September";
$lang["month_10"]	=	"October";
$lang["month_11"]	=	"November";
$lang["month_12"]	=	"December";
$lang["day_0"]		=	"Sunday";
$lang["day_1"]		=	"Monday";
$lang["day_2"]		=	"Tuesday";
$lang["day_3"]		=	"Wednesday";
$lang["day_4"]		=	"Thursday";
$lang["day_5"]		=	"Friday";
$lang["day_6"]		=	"Saturday";
$lang["available"]	=	"Available";
$lang["legend"]		=	"Key";
$lang["prev_X_months"]	=	"Previous $numMonths months";
$lang["next_X_months"]	=	"Next $numMonths months";
$lang["inst_click_dates"]	=	"click on the dates to change the state";
$lang["admin_login"]		=	"Login";
$lang["admin_dashboard"]	=	"Dashboard";
$lang["admin_states"]		=	"Booking States";
$lang["admin_bookings"]		=	"Bookings";
$lang["admin_items"]		=	"Booking Items";
$lang["admin_config"]		=	"Calendar Configuration";
$lang["admin_reset"]		=	"Reset Calendar";
$lang["admin_profile"]		=	"Your Profile";
$lang["logout"]				=	"Logout";
$lang["see_web"]			=	"See Calendar";
$lang["admin_admin_users"]	=	"Admin Users";
$lang["username"]			=	"Username";
$lang["password"]			=	"Password";
$lang["title_add"]			=	"Add";
$lang["title_mod"]			=	"Modify";
$lang["title_delete"]		=	"Delete";
$lang["desc"]				=	"Description";
$lang["class"]				=	"CSS class";
$lang["bt_save_changes"]	=	"Save Changes";
$lang["bt_add"]				=	"Add";
$lang["bt_delete"]			=	"Delete";
$lang["msg_mod_OK"]			=	"Item modified successfully";
$lang["msg_mod_KO"]			=	"Item has NOT been modified";
$lang["msg_add_OK"]			=	"Item added successfully";
$lang["msg_add_KO"]			=	"Item has NOT been added";
$lang["msg_delete_OK"]		=	"Item has been deleted successfully";
$lang["msg_delete_KO"]		=	"Item has NOT been deleted";
$lang["select_item"]		=	"Select Item";
$lang["item_to_show"]		=	"Calendar Item";
$lang["item"]				=	"Calendar";
$lang["add_item_id"]		=	"New Item by ID";
$lang["bt_add_item"]		=	"Add Item";
$lang["bt_change_item"]		=	"Change Item";
$lang["bt_reset_calendar"]	=	"Reset Calendar";
$lang["yes"]				=	"Yes";
$lang["no"]					=	"No";
$lang["date_format"]		=	"Date Format";
$lang["date_format_us"]		=	"mm/dd/yyyy";
$lang["date_format_eu"]		=	"dd/mm/yyyy";
$lang["title"]				=	"Title";
$lang["cal_url"]			=	"Path to calendar from root";
$lang["note_cal_url"]		=	"eg- /calendar (no trailing slash)";
$lang["default_lang"]		=	"Default Language";
$lang["num_months"]			=	"Number of Months to Show";
$lang["start_day"]			=	"Start Day";
$lang["click_past_dates"]	=	"Past Dates active";
$lang["msg_new_cal_item_added"]	=	"Note - new items will only be added to the database when a date state is modified.";
$lang["warning_delete_confirm"]	=	"Are you sure that you want to delete this item?";
$lang["warning_reset_confirm"]	=	"Are you sure that you want to perform a complete reset on the calendar? - ALL DATA WILL BE LOST!";
$lang["id"]							=	"ID";
$lang["options"]					=	"Options";
$lang["password_repeat"]			=	"Repeat Password";
$lang["note_password_mod"]			=	"Only introduce the password data if you want to change it.";
$lang["note_user_calendar_only"]	=	"This user will only be able to control their own calendars";
$lang["warning_no_page_permission"]	="Page does not exist";
$lang["inst_drag"]					=	"Drag the items to change the order";
$lang["warning_no_calendar_items"]	=	"You have not added any calendar items yet.  Click on the button to add your calendar.";
$lang["warning_item_not_exist"]		=	"This item does not exist";
$lang["state"]="State";
$lang["last_update"]			=	"Last updated";
$lang["admin_languages"]		=	"Languages";
$lang["language"]				=	"Language";
$lang["new_lang_code"]			=	"New language code (ISO)";
$lang["note_add_language"]		=	"Adding a new language will add the required fields to the database and copy the english language file ready to be translated.<br>Note - the language folder and files must have write permissions (chmod 777)";
$lang["warning_new_lang_confirm"]=	"Are you sure that you want to add a new language?\n This will alter the database and create a new language file.";

$lang["states_method_click_through"]	=	"click-through (cycle through all states)";
$lang["click_method"]	=	"Click Method";
$lang["inst_calendar_click"]	=	"click on the dates to change the state";
$lang["tip_add_new_item"]	=	"Add new Item";
$lang["tip_edit_item"]	=	"Edit this item";
$lang["tip_add_new_state"]	=	"Add new State";
$lang["tip_delete_item"]	=	"Delete this item";
$lang["tip_see_item_calendar"]	= "See calendar for this item";
$lang["drag_to_order"]		=	"Drag here to change item order";
$lang["msg_warning"]		=	"Information:";
$lang["msg_order_update_OK"]=	"Item Order modified";
$lang["msg_order_update_KO"]=	"Unable to modify Item Order";
$lang["msg_state_mod_OK"]	=	"Item State modified";
$lang["msg_state_mod_KO"]	=	"Unable to modify Item State";
$lang["click_update_state"]	=	"Click to update the Item State";
$lang["id_ref_external"]	=	"External Item ID";
$lang["note_id_ref_external"]=	"Optional - to reference item to existing items database table";
$lang["theme"]	=	"Calendar Theme";
$lang["item_modified"]	=	"modified";
$lang["item_added"]	=	"added";
$lang["warning_no_active_items"]="You have no active calendar items for which to show the calendar.";

?>