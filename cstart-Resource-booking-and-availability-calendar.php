<?php
/*
Plugin Name: Resource Booking and Availability Calendar
Plugin URI: http://cstart.blogspot.com/2010/05/ajax-booking-availability-calendar.html
Description: Wordpress plugin for Resource booking and availability. 
Supports, resource booking on per author/subscriber basis.
Version: 1.0.1
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

define("CS_RBA_DB_VERSION", "1.0");
define("NUM_MONTHS",2);
define("LANG","en");
define("DB_PREFIX","wp_");
define('CS_RBA_META_KEY', 'cs_RBA_resource');

//	define tables
define("T_BOOKINGS_ITEMS",	"".DB_PREFIX."bookings_items");		# calendar items
define("T_BOOKINGS",		"".DB_PREFIX."bookings"); 			# bookings dates
define("T_BOOKING_STATES",	"".DB_PREFIX."bookings_states");		# booking types (am, pm, etc)
define("T_BOOKING_UPDATE",	"".DB_PREFIX."bookings_last_update");# holds las calendar update date
define("T_BOOKINGS_ADMIN",	"".DB_PREFIX."bookings_admin_users");# admin users
define("T_BOOKINGS_CONFIG",	"".DB_PREFIX."bookings_config");		# general config

$the_file = WP_PLUGIN_DIR . '/resource-booking-and-availability-calendar/php/en.lang.php';
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);


$the_file = WP_PLUGIN_DIR . '/resource-booking-and-availability-calendar/php/functions.inc.php';
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);

if (!class_exists("cstartResourceBookingAvailability")) {
    class cstartResourceBookingAvailability {
		var $m_adminOptionsName = "cstartResourceBookingAvailabilityAdminOptions";
        function cstartResourceBookingAvailability() { //constructor
            
        }
		
		function init() {
            $this->getAdminOptions();
        }


		//Returns an array of admin options
        function getAdminOptions() {
            $adminOptions = array('show_header' => 'true',
                'add_content' => 'true', 
                'comment_author' => 'true', 
                'content' => '');
            $storedAdminOptions = get_option($this->m_adminOptionsName);
            if (!empty($storedAdminOptions)) {
                foreach ($storedAdminOptions as $key => $option)
                    $adminOptions[$key] = $option;
            }            
            update_option($this->m_adminOptionsName, $adminOptions);
            return $adminOptions;
        }

		//Adds the appropriate JavaScript files into the head tag
		function addHeader() {
		//Load the appropriate files only if the page is single -- Wouldn't want to hit on a non-single post. :)
			if (is_single() || is_page()) {
				$this->addHeaderJSCSS();
			}
		}//End function addHeaderJS

		
		
		function addAdminHeader() {
			if (function_exists('wp_enqueue_script') && function_exists('wp_register_script')) {
				wp_register_script('mootools-core', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-1.2.4-core-yc.js');

				wp_enqueue_script('mootools-more', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-1.2.4.4-more.js', array('mootools-core'), '1.2.4');				
				wp_enqueue_script('mootools-cal-admin', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-cal-admin.js', array('mootools-core'), '1.2.4');
				wp_enqueue_script('mootools-flext', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-flext.js', array('mootools-core'), '1.2.4');
				wp_enqueue_script('mootools-roar', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-availability-calendar-calendar/js/mootools-roar.js', array('mootools-core'), '1.2.4');				
			}			
			echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/css/avail-calendar.css\" />\n";
//			echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/css/admin.css\" />\n";
			echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/css/mootools-roar.css\" />\n";
			$this->addAdminHeaderCode();
		}
		function addHeaderJSCSS() {
			if (function_exists('wp_enqueue_script') && function_exists('wp_register_script')) {
				wp_register_script('mootools-core', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-1.2.4-core-yc.js');

				wp_enqueue_script('mootools-more', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-1.2.4.4-more.js', array('mootools-core'), '1.2.4');				
				wp_enqueue_script('mootools-cal-public', get_bloginfo('wpurl') . '/wp-content/plugins/resource-booking-and-availability-calendar/js/mootools-cal-public.js', array('mootools-core'), '1.2.4');
				echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/css/avail-calendar.css\" />\n";
			}
		}
		
	

		function addAdminHeaderCode() {
			global $lang;
			global $post;
			?>
			<script type="text/javascript">		

				//admin user js
				var date_hover 			= true;	//	true=on, false=off
				var show_message 		= true; //	true=on, false=off
				
				var url_ajax_cal 		= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/php/ajax/calendar.ajax.php"; ?>'; 	//	ajax file for loading calendar via ajax
				var url_ajax_update 	= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/php/ajax/update_calendar.ajax.php"; ?>'; //	ajax file for update calendar state
	
				var img_loading_day		= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/images/ajax-loader-day.gif"; ?>'; // animated gif for loading	
				var img_loading_month	= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/images/ajax-loader-month.gif"; ?>'; // animated gif for loading	
		
				//	don't change these values
				var id_item 			= '<?php echo $post->ID; ?>'; // id of item to be modified (via ajax)
				var lang 				= '<?php echo "en"; ?>'; // language
				var months_to_show		= <?php echo "2"; ?>; // number of months to show
				var clickable_past		= '<?php echo "off"; ?>'; // previous dates

				var txtWarning = '<?php echo $lang["msg_warning"]; ?>';
				var txtOrderUpdateOK = '<?php echo $lang["msg_order_update_OK"]; ?>';
				var txtOrderUpdateKO = '<?php echo $lang["msg_order_update_KO"]; ?>';
				var txtStateModOK = '<?php echo $lang["msg_state_mod_OK"]; ?>';
				var txtStateModKO = '<?php echo $lang["msg_state_mod_KO"]; ?>';
	
			</script>
			<?php 
		}//End function addHeaderCode()		
	

		function addHeaderCode() {
			global $post;
			?>
				<script type="text/javascript">		
				//	define vars
				var url_ajax_cal 		= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/php/ajax/calendar.ajax.php"; ?>'; // ajax file for loading calendar via ajax
				var img_loading_day		= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/images/ajax-loader-day.gif"; ?>'; // animated gif for loading	
				var img_loading_month	= '<?php echo get_bloginfo('wpurl') . "/wp-content/plugins/resource-booking-and-availability-calendar/images/ajax-loader-month.gif"; ?>'; // animated gif for loading	
				//	don't change these values
				var id_item 			= '<?php echo $post->ID; ?>'; // id of item to be modified (via ajax)
				var lang 				= '<?php echo "en"; ?>'; // language
				var months_to_show		= <?php echo "2"; ?>; // number of months to show
				var clickable_past		= '<?php echo "off"; ?>'; // previous dates
				</script>
			<?php 
		}//End function addHeaderCode()



		//Prints out the admin page
		function printAdminPage() {

			$adminOptions = $this->getAdminOptions();
								
			if (isset($_POST['cs_RBA_updateSettings'])) { 
				if (isset($_POST['cs_RBA_header'])) {
					$adminOptions['show_header'] = $_POST['cs_RBA_header'];
				}	
				if (isset($_POST['cs_RBA_addContent'])) {
					$adminOptions['add_content'] = $_POST['cs_RBA_addContent'];
				}	
				if (isset($_POST['cs_RBA_author'])) {
					$adminOptions['comment_author'] = $_POST['cs_RBA_author'];
				}	
				if (isset($_POST['cs_RBA_content'])) {
					$adminOptions['content'] = apply_filters('content_save_pre', $_POST['cs_RBA_content']);
				}
				update_option($this->m_adminOptionsName, $adminOptions);
				
				?>
			<div class="updated"><p><strong><?php _e("Settings Updated.", "cstartResourceBookingAvailability");?></strong></p></div>
			<?php
			}
            ?>
			<h2>Resource Booking and Availability - Administration</h2>

			<div class=wrap>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<h3>Content to Add to the End of a Post</h3>
			<textarea name="cs_RBA_content" style="width: 80%; height: 100px;"><?php _e(apply_filters('format_to_edit',$adminOptions['content']), 'cstartResourceBookingAvailability') ?></textarea>
			<h3>Allow Comment Code in the Header?</h3>
			<p>Selecting "No" will disable the comment code inserted in the header.</p>
			<p><label for="cs_RBA_header_yes"><input type="radio" id="cs_RBA_header_yes" name="cs_RBA_header" value="true" <?php if ($adminOptions['show_header'] == "true") { _e('checked="checked"', "cstartResourceBookingAvailability"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="cs_RBA_header_no"><input type="radio" id="cs_RBA_header_no" name="cs_RBA_header" value="false" <?php if ($adminOptions['show_header'] == "false") { _e('checked="checked"', "cstartResourceBookingAvailability"); }?>/> No</label></p>

			<h3>Allow Content Added to the End of a Post?</h3>
			<p>Selecting "No" will disable the content from being added into the end of a post.</p>
			<p><label for="cs_RBA_addContent_yes"><input type="radio" id="cs_RBA_addContent_yes" name="cs_RBA_addContent" value="true" <?php if ($adminOptions['add_content'] == "true") { _e('checked="checked"', "cstartResourceBookingAvailability"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="cs_RBA_addContent_no"><input type="radio" id="cs_RBA_addContent_no" name="cs_RBA_addContent" value="false" <?php if ($adminOptions['add_content'] == "false") { _e('checked="checked"', "cstartResourceBookingAvailability"); }?>/> No</label></p>

			<h3>Allow Comment Authors to be Uppercase?</h3>
			<p>Selecting "No" will leave the comment authors alone.</p>
			<p><label for="cs_RBA_author_yes"><input type="radio" id="cs_RBA_author_yes" name="cs_RBA_author" value="true" <?php if ($adminOptions['comment_author'] == "true") { _e('checked="checked"', "cstartResourceBookingAvailability"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="cs_RBA_author_no"><input type="radio" id="cs_RBA_author_no" name="cs_RBA_author" value="false" <?php if ($adminOptions['comment_author'] == "false") { _e('checked="checked"', "cstartResourceBookingAvailability"); }?>/> No</label></p>

			<div class="submit">
			<input type="submit" name="cs_RBA_updateSettings" value="<?php _e('Update Settings', 'cstartResourceBookingAvailability') ?>" /></div>
			</form>
			</div>
		<?php
		}//End function printAdminPage()
        
	    function store_post_options($post_id) {
	    	// Add the post id into "booking items"
	    	
			if (!isset($_POST['cs_RBA_Resource'])) {
				return;
			}
			$post = get_post($post_id);
			if (!$post || $post->post_type == 'revision') {
				return;
			}
			$meta = get_post_meta($post->ID, CS_RBA_META_KEY, true);
			$posted = intval($_POST['cs_RBA_Resource']);
			switch ($posted) {
				case 1:
					if (intval($meta) == 1) { // already set
						return;
					}
					add_post_meta($post_id, CS_RBA_META_KEY, 1);
					break;
				case 0:
					delete_post_meta($post_id, CS_RBA_META_KEY, 1); // turn it off
					break;
			}
	    }
			      
	    
	    function post_options() {
			global $post;
			if (!$post->ID) return;
			echo '<div class="postbox">
				<h3>'.__('Resource Availability & Booking Calendar', 'cstartResourceBookingAvailability').'</h3>
				<div class="inside">
				<p>'.__('Enable Booking?', 'cstartResourceBookingAvailability').'
				';
			$resource = get_post_meta($post->ID, CS_RBA_META_KEY, true);
			if ($resource == '') {
				$resource = '0';
			}
			echo '
			<input type="radio" name="cs_RBA_Resource" id="cs_RBA_Resource_1" value="1" ',checked('1', $resource), 'onclick="document.getElementById(\'testbox\').style.display=\'block\';" ',' /> <label for="cs_RBA_Resource_1">'.__('Yes', 'cstartResourceBookingAvailability').'</label> &nbsp;&nbsp;
			<input type="radio" name="cs_RBA_Resource" id="cs_RBA_Resource_0" value="0" ',checked('0', $resource),'onclick="document.getElementById(\'testbox\').style.display=\'none\';" ',' /> <label for="cs_RBA_Resource_0">'.__('No', 'cstartResourceBookingAvailability').'</label>
			';
			$txt = $this->addPublicCalendar("",true);
			if ($resource) {
				echo '
				</p>
				<div id="testbox" style="display:block;">
					' . $txt .'
				</div>
				</div><!--.inside-->
				<p>&nbsp;&nbsp;<b>Note:</b>&nbsp;Click on the desired date to change its booking status.</p>
				</div><!--.postbox-->
				';
			}
			else {
				echo '
				</p>
				<div id="testbox" style="display:none;">
					' . $txt .'
				</div>
				</div><!--.inside-->
				<p>&nbsp;&nbsp;<b>Note:</b>&nbsp;Click on the desired date to change its booking status.</p>
				</div><!--.postbox-->
				';
			}
			
	    }

		function addContent($content = '') {
			global $wpdb;
//			$adminOptions = $this->getAdminOptions();
			if (/*$adminOptions['add_content'] == "true" && */ is_single()) {
				$content .= '<h2>Availability Calendar.</strong></h2>';
				$content .= $this->addPublicCalendar("",false);
			}
			return $content;
		}	
			    
	    
		function addPublicCalendar($txt = '',$onDemandMeta) {
			global $wpdb, $post;

				$resource = get_post_meta($post->ID, CS_RBA_META_KEY, true);				
			    if ($resource != '1' && !$onDemandMeta) return $txt;
				//	define id of item to modify
				if(isset($_REQUEST["id_item"])) 		define("ID_ITEM",	$_REQUEST["id_item"]);	#	id sent via  url, form session etc
				else{
					//	define default id manually
					define("ID_ITEM", $post->ID);						
				}
				
				if($no_id){
					//	no id - calendar hasn't been set up yet
					$calendar_months='
					<ul>
						<li>You have not yet added any calendar items to the database.</li>
					</ul>
					';
				}else{
					//	define start month and year
					$this_year		=	date('Y');	# current year
					$this_month		=	date('m');	# current month
					
					//	create array of months from which to make calendars
					for($k=0; $k< NUM_MONTHS; ++$k){
						
						//	add month layer to page - calendar loaded via ajax
						$calendar_months.='<div id="'.$this_month.'_'.$this_year.'" class="cal_month load_cal"></div>';
						if($this_month==12){
							//	start new year and reset month numbers
							$this_month	=	$this_month=1;	#	set to 1
							$this_year	=	$this_year+1;	#	add 1 to current year
						}else{
							++$this_month;
						}
					}
				}
				
				//	define calendar states for key
				$list_states	= "";
				$sel_list_states= "";
				$registry =& csRBARegistry::getInstance();
    			$bookings_table =& $registry->get('bookings_table');
    			$bookings_states_table =& $registry->get('bookings_states_table');
				
				$sql="SELECT id,class,desc_".LANG." AS the_desc FROM ".$bookings_states_table." WHERE state=1 ORDER BY id ASC";
				
				$rows=$wpdb->get_results($sql);
				foreach ($rows as $row) {
            		$booked_days[$row->the_date] = array("class"=>$row->class,"state"=>$row->the_state);
            		$list_states.='<li class="'.$row->class.'" title="'.$row->the_desc.'"><span>'.$row->the_desc.'</span></li>
					';
					$sel_list_states.='<option value="'.$row->id.'">'.$row->the_desc.'</option>';
				}
				
				$calendar_states='
				<div id="key" class="cal_month">
					<div class="cal_title">'.'Legend'.'</div>
					<ul>
						<li><span>'.'Available'.'</span></li>
						'.$list_states.'
					</ul>
				</div>
				';
			
				
				$txt .= 
				'<div id="cal_controls">
				<div id="cal_prev" title="Prev"><img src="'.get_bloginfo('wpurl').'/wp-content/plugins/resource-booking-and-availability-calendar/images/icon_prev.gif" class="cal_button"><small>Previous Month</small></div>
				<div id="cal_next" title="Next">&nbsp;&nbsp;<small>Next Month</small><img src="'.get_bloginfo('wpurl'). '/wp-content/plugins/resource-booking-and-availability-calendar/images/icon_next.gif" class="cal_button"></div>
				</div>
				<div class="clear"></div>
				<div id="cal_wrapper">
					<div id="the_months">
					'.$calendar_months.'
					</div>';
				
				$txt .=
				'<div id="key_wrapper">
				'.$calendar_states.'
				</div>
				</div>
				<div class="clear"></div>';
			return $txt;
		}//End function printAdminPage()
		
		function createTables() {
			$cs_RBA_db_version = CS_RBA_DB_VERSION;
			   global $wpdb;
			   global $cs_RBA_db_version;

			   $table_name = $wpdb->prefix . "bookings";
			   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				  
				  $sql = "CREATE TABLE " . $table_name . " (
				  id mediumint(11) NOT NULL AUTO_INCREMENT,
				  id_item bigint(20) DEFAULT '0' NOT NULL,
				  the_date date NOT NULL,
				  id_state bigint(11) NOT NULL,
				  id_booking bigint(10) NOT NULL,
				  UNIQUE KEY id (id)
				  );";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			   }

			   $table_name = $wpdb->prefix . "bookings_states";
			   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				  
				  $sql = "CREATE TABLE " . $table_name . " (
				  id mediumint(11) NOT NULL AUTO_INCREMENT,
				  desc_en varchar(100),
				  state int(1) NOT NULL,
				  class varchar(30),
				  id_booking bigint(10) NOT NULL,
				  UNIQUE KEY id (id)
				  );";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

			   }	
				$wpdb->insert( $table_name, array( 'id' => 1, 'desc_en' => 'Booked', 'state' => 1, 'class' => 'booked', 'id_booking' => 0 ), array( '%d', '%s', '%d', '%s', '%d' ) );
				$wpdb->insert( $table_name, array( 'id' => 2, 'desc_en' => 'Provisional', 'state' => 1, 'class' => 'booked_pr', 'id_booking' => 0 ), array( '%d', '%s', '%d', '%s', '%d' ) );
				//$wpdb->insert( $table_name, array( 'id' => 3, 'desc_en' => 'Offer', 'state' => 1, 'class' => 'offer', 'id_booking' => 0 ), array( '%d', '%s', '%d', '%s', '%d' ) );																
			   
				add_option("cs_RBA_db_version", $cs_RBA_db_version);

		} //End function createTables
	} //End Class cstartResourceBookingAvailability
} //end if class check


if (class_exists("cstartResourceBookingAvailability")) {
    $cs_RBA_plugin = new cstartResourceBookingAvailability();
}

//Initialize the admin panel
if (!function_exists("cstartResourceBookingAvailability_AdminPanel")) {
	function cstartResourceBookingAvailability_AdminPanel() {
		global $cs_RBA_plugin;
		if (!isset($cs_RBA_plugin)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('Resource Booking & Availability', 'cstart Resource Booking & Availability', 9, basename(__FILE__), array(&$cs_RBA_plugin, 'printAdminPage'));
		}
	}	
}

//Actions and Filters   
if (isset($cs_RBA_plugin)) {
    //Actions
	register_activation_hook(__FILE__,array(&$cs_RBA_plugin, 'createTables'));
//	add_action('admin_menu', 'cstartResourceBookingAvailability_AdminPanel');
    add_action('wp_head', array(&$cs_RBA_plugin, 'addHeaderCode'), 1);
    add_action('wp_print_scripts', array(&$cs_RBA_plugin, 'addHeader'));
	add_filter('the_content', array(&$cs_RBA_plugin, 'addContent'),1);
	add_action('activate_resource-booking-and-availability-calendar/cstart-Resource-booking-availability-calendar.php',  array(&$cs_RBA_plugin, 'init'));
	
	add_action('admin_print_scripts', array(&$cs_RBA_plugin, 'addAdminHeader'),1);
	
	add_action('draft_post', array(&$cs_RBA_plugin,'store_post_options'),1);
	add_action('publish_post', array(&$cs_RBA_plugin,'store_post_options'),1);
	add_action('save_post', array(&$cs_RBA_plugin,'store_post_options'),1);
	add_action('edit_form_advanced', array(&$cs_RBA_plugin,'post_options'),1);
	

    //Filters
}

?>