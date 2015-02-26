<?php
/**
 * Plugin Name: Quicket API
 * Plugin URI: 
 * Company: 
 * Description: Wordpress plugin for Quicket API
 * Version: 1.0.0
 * Author:
 * Author URI: 
 * License: GPL2
 */


/*  Copyright 2015  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

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

//Include Plugin Styles and Scripts
// function qckt_scripts() {
	// wp_enqueue_style( 'style-qckt', WP_PLUGIN_URL . '/quicket-api/css/qckt-style.css' );
// }

function qckt_scripts() {
	wp_enqueue_style( 'style-qckt', plugins_url( 'css/qckt-style.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'qckt_scripts' );

//wp-admin scripts and styles
function qckt_admin_scripts($hook) {

	//include only for 'widgets.php' page
    if ( 'widgets.php' != $hook ) {
        return;
    }

	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', plugins_url( 'css/jquery-ui.css', __FILE__ ));
	wp_enqueue_script( 'script-qckt', plugins_url( 'js/qckt-admin-script.js', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'qckt_admin_scripts' );

/**
* Build the entire current page URL (incl query strings) and output it
* Useful for social media plugins and other times you need the full page URL
* Also can be used outside The Loop, unlike the_permalink
*
* @returns the URL in PHP (so echo it if it must be output in the template)
* Also see the_current_page_url() syntax that echoes it
*/ 
if ( ! function_exists( 'get_current_page_url' ) ) {
	function get_current_page_url() {
		global $wp;
		return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	}
} 

//Remove a Query String Key=>Value
function qckt_remove_querystring_var($url, $key) { 
	$url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);
	return $url;
}

//Get Custom Pagination Url
function qckt_pagenum_link($pagenum){
	$page_url = get_current_page_url();
	$url_query = parse_url($page_url, PHP_URL_QUERY);
	if( isset( $url_query ) && !empty( $url_query ) ){ //if url have parameters
		parse_str($url_query);
		if( isset( $qcktpg ) ){ //if 'qcktpg' parameter exists
			$url_query = qckt_remove_querystring_var($page_url, 'qcktpg');
			$url_query .= '&qcktpg=' . $pagenum;
		}
		else{
			$url_query = $page_url . '&qcktpg=' . $pagenum;
		}
	}
	else{
		$url_query = $page_url . '?qcktpg=' . $pagenum;
	}

	return $url_query;
}

//Custom Pagination Function
function qckt_pagination($pages = '', $range = 4){ 
     $showitems = ($range * 2)+1; 
 
     $paged = $_REQUEST['qcktpg'];
     if( !isset( $paged ) ){
     	$paged = $_GET['qcktpg'];
     }
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }  
 
     if(1 != $pages)
     {
         echo "<div class=\"qckt_pagination\">";
         //echo "<span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".qckt_pagenum_link(1)."' title='First'>&laquo; </a>"; //First
         if($paged > 1 && $showitems < $pages) echo "<a href='".qckt_pagenum_link($paged - 1)."' title='Previous'>&lsaquo; </a>"; //Previous
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".qckt_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".qckt_pagenum_link($paged + 1)."\" title='Next'> &rsaquo;</a>"; //Next
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".qckt_pagenum_link($pages)."' title='Last'> &raquo;</a>"; //Last
         echo "</div>\n";
     }
}

/**
 * Adds Quicket_Widget widget.
 */
class Quicket_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'quicket_widget', // Base ID
			__( 'Quicket' ), // Name
			array( 'description' => __( 'A Quicket API Widget' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		//Get Widget Options Values
		$api_key = $instance['api_key'];
		$token_key = $instance['token_key'];
		$num_events = ! empty( $instance['num_events'] ) ? $instance['num_events'] : '10';
		$cat_ids = $instance['cat_ids'];
		$affil_code = $instance['affil_code'];
		$show_pagi = $instance['show_pagi'];
		$show_modif = $instance['show_modif'];
		$show_mode = ! empty( $instance['show_mode'] ) ? $instance['show_mode'] : 1;

		//set current page number
		$pagenum = $_REQUEST['qcktpg'];
		if( !isset( $pagenum ) ){
			$pagenum = $_GET['qcktpg'];
		}

		//Affiliate Code parameter - event link modificator
		$affil_code_prefix = '';
		if( isset( $affil_code ) && !empty( $affil_code ) ){
			$affil_code_prefix = '?r=' . $affil_code;
		}

		if( isset( $api_key ) && !empty( $api_key ) ){

			//Initialize REST API Calls data
			//CURL Request
		    $ch = curl_init();

		    if( $show_mode == 1 ){
		    	$endpoint_url = 'https://api.quicket.co.za/api/events'; //get public events
		    }
		    else{
		    	if( isset( $token_key ) && !empty( $token_key ) ){
		    		$endpoint_url = 'https://api.quicket.co.za/api/users/savedevents'; //get saved events
		    		//$PostArr["usertoken"] = $token_key;
		    		curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("usertoken: $token_key"));
		    	}
		    	else{
		    		echo _e('Error: User Token is undefined. You must have a user token to get saved events. To get your user token, please go to the API Keys page in your Quicket account or email us on support@quicket.co.za.');
		    	}
		    }

			$PostArr["api_key"] = $api_key; //subscription key
			//if( isset( $num_events ) ){
				$PostArr["pageSize"] = $num_events; //Number of items to return per page
			//}
			if( isset( $pagenum ) && !empty( $pagenum ) ){
				$PostArr["page"] = $pagenum;
			}

			if( isset( $cat_ids ) && !empty( $cat_ids ) ){
				$PostArr["categories"] = $cat_ids;
			}

			if( isset( $show_modif ) && !empty( $show_modif ) ){
				$PostArr["lastModified"] = $show_modif;
			}

			$PostStr = http_build_query($PostArr);
			if(!$bPost){
				$endpoint_url .= "?".$PostStr;
			}
			curl_setopt($ch, CURLOPT_URL, $endpoint_url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			//POST DATA
			if($bPost){		
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $PostStr);
			}
		    $output = curl_exec($ch);

			$quicket_obj = json_decode( $output);
			if( isset( $quicket_obj ) && !empty( $quicket_obj ) ){

				if( $quicket_obj->statusCode != '0' ){ //show error message
					echo $quicket_obj->message;
				}
				else{ ?>

					<div id="quicket-events-wrap">

						<?php foreach( $quicket_obj->results as $q_event ){ ?>

							<div id="quicket-event-<?php echo $q_event->id; ?>" class="quicket-event">
								
								<div class="qckt-event-thumb">
									<a href="<?php echo $q_event->url . $affil_code_prefix; ?>" title="<?php echo $q_event->name; ?>" target="_blank">
										<img src="<?php echo $q_event->imageUrl; ?>" alt="<?php echo $q_event->name; ?>">
									</a>
								</div>

								<div class="qckt-event-content">

									<h2 class="widget-title qckt-padding">
										<a href="<?php echo $q_event->url . $affil_code_prefix; ?>" title="<?php echo $q_event->name; ?>" target="_blank">
											<?php echo $q_event->name; ?>
										</a>
									</h2>
									<hr/>

									<div class="qckt-date-area qckt-padding">
										<span>
											<strong><?php _e( 'Starts:' ); ?> </strong>
											<?php $startDate = date("D d M Y \a\\t g:i A", strtotime($q_event->startDate));
											echo $startDate; ?>
										</span>
										&nbsp;|&nbsp;
										<span>
											<strong><?php _e( ' Ends:' ); ?> </strong>
											<?php $endDate = date("D d M Y \a\\t g:i A", strtotime($q_event->endDate));
											echo $endDate; ?>
										</span>
									</div>
									<div class="qckt-padding">
										<strong><?php _e( 'VENUE:' ); ?> </strong>

										<?php $coma_flag = 0;
										if( isset( $q_event->venue->name ) && !empty( $q_event->venue->name ) ){ 
											echo $q_event->venue->name; 
											$coma_flag = 1;
										}
										if( isset( $q_event->venue->addressLine1 ) && !empty( $q_event->venue->addressLine1 ) ){ 
											if( isset( $coma_flag ) && !empty( $coma_flag ) ){
												echo ', ';
											}
											else{
												$coma_flag = 1;
											}
											echo $q_event->venue->addressLine1;
										} 
										if( isset( $q_event->venue->addressLine2 ) && !empty( $q_event->venue->addressLine2 ) ){
											if( isset( $coma_flag ) && !empty( $coma_flag ) ){
												echo ', ';
											}
											else{
												$coma_flag = 1;
											}
											echo $q_event->venue->addressLine2;
										} ?>

									</div>
								</div>

							</div>

							<div class="qckt-clear"></div>

						<hr/>
						<?php } ?>

						<?php if (function_exists("qckt_pagination")) {
							//Show pagination
							if( isset( $show_pagi ) && !empty( $show_pagi ) ){
						    	qckt_pagination($quicket_obj->pages, 2);
							}
						} ?>

						<div class="qckt-clear"></div>

						<div class="qckt-footer">Powered by <img class="qckt-logo" src="<?php echo WP_PLUGIN_URL . '/quicket-api/images/logo.png'; ?>" alt="" /></div>

					</div><!-- #quicket-events-wrap -->

				<?php }
			}
		}
		else{
			echo _e( 'Error: API Key is undefined. You must enter an API Key. To get your API Key, please register on the Quicket developer portal at https://developer.quicket.co.za . Once you have registered, you will get an API key. For help, please email support@quicket.co.za.' );
		}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Quicket' );
		$show_mode = ! empty( $instance['show_mode'] ) ? $instance['show_mode'] : 1;
		$api_key = $instance['api_key'];
		$num_events = ! empty( $instance['num_events'] ) ? $instance['num_events'] : '10';
		$cat_ids = $instance['cat_ids'];
		$affil_code = $instance['affil_code'];
		$show_pagi = $instance['show_pagi'];
		$show_modif = $instance['show_modif']; 
		$token_key = $instance['token_key']; ?>

		<script type="text/javascript">
		(function($) {
			$(document).ready(function(){
				//Initilize and Load Datepicker
			    $('.qckt-datepicker').datepicker({
			        dateFormat : 'yy-mm-dd'
			    });

			    //Show Mode Handler
			    if( $('.qckt-utoken').length>0 ){
			    	$('.qckt-widget-options').each(function(){
				    	var showmode = $(this).find('.qckt-showmode select').val();
				    	//Show or hide 'User Token' field
				    	if( showmode == 2 ){
				    		$(this).find('.qckt-utoken').removeClass('hidden');
				    	}
			    	});
			    }

			    $('.qckt-showmode select').on('change', function() {
			    	$(this).closest('.qckt-widget-options').find('.qckt-utoken').toggleClass('hidden');
			    });

			});
		})(jQuery);
		</script>

		<div class="qckt-widget-options">

			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>

			<!-- Show Public events or Saved Events (select box) -->
			<p class="qckt-showmode">
			<label for="<?php echo $this->get_field_id( 'show_mode' ); ?>"><?php _e( 'Show:' ); ?></label> 
			<select autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'show_mode' ); ?>" name="<?php echo $this->get_field_name( 'show_mode' ); ?>">
				<option value="1" <?php if( $show_mode == 1 ){ echo 'selected="selected"'; }?>>Public Events</option>
				<option value="2" <?php if( $show_mode == 2 ){ echo 'selected="selected"'; }?>>Saved Events</option>
			</select>
			</p>

			<!-- Subscriber Key (text) -->
			<p>
			<label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API Key:' ); ?></label> 
			<input autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" type="text" value="<?php echo esc_attr( $api_key ); ?>">
			</p>

			<!-- User Token (text) -->
			<p class="qckt-utoken <?php if( $show_mode == 1 ){ echo 'hidden'; } ?>">
			<label for="<?php echo $this->get_field_id( 'token_key' ); ?>"><?php _e( ' User Token:' ); ?></label> 
			<input autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'token_key' ); ?>" name="<?php echo $this->get_field_name( 'token_key' ); ?>" type="text" value="<?php echo esc_attr( $token_key ); ?>">
			</p>

			<!-- Number of Events (text) -->
			<p>
			<label for="<?php echo $this->get_field_id( 'num_events' ); ?>"><?php _e( 'Number of Events:' ); ?></label> 
			<input autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'num_events' ); ?>" name="<?php echo $this->get_field_name( 'num_events' ); ?>" type="text" value="<?php echo esc_attr( $num_events ); ?>">
			</p>

			<!-- category ID’s (text) -->
			<p>
			<label for="<?php echo $this->get_field_id( 'cat_ids' ); ?>"><?php _e( 'Category ID’s:' ); ?></label> 
			<input autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'cat_ids' ); ?>" name="<?php echo $this->get_field_name( 'cat_ids' ); ?>" type="text" value="<?php echo esc_attr( $cat_ids ); ?>">
			</p>

			<!-- include Affiliate Code (text) -->
			<p>
			<label for="<?php echo $this->get_field_id( 'affil_code' ); ?>"><?php _e( 'Include Affiliate Code:' ); ?></label> 
			<input autocomplete="off" class="widefat" id="<?php echo $this->get_field_id( 'affil_code' ); ?>" name="<?php echo $this->get_field_name( 'affil_code' ); ?>" type="text" value="<?php echo esc_attr( $affil_code ); ?>">
			</p>

			<!-- Last Modified Date (text box with date picker) -->
			<p>
			<label for="<?php echo $this->get_field_id( 'show_modif' ); ?>"><?php _e( 'Last Modified Date' ); ?></label> 
			<input class="widefat qckt-datepicker" id="<?php echo $this->get_field_id( 'show_modif' ); ?>" name="<?php echo $this->get_field_name( 'show_modif' ); ?>" type="text" value="<?php echo esc_attr( $show_modif ); ?>">
			</p>

			<!-- Show Pagination (checkbox) -->
			<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'show_pagi' ); ?>" name="<?php echo $this->get_field_name( 'show_pagi' ); ?>" type="checkbox" value="1" <?php if( isset( $show_pagi ) && !empty( $show_pagi ) ){ echo 'checked="checked"'; } ?>>
			<label for="<?php echo $this->get_field_id( 'show_pagi' ); ?>"><?php _e( 'Show Pagination' ); ?></label> 
			</p>

		</div><!-- .qckt-widget-options -->

		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['api_key'] = ( ! empty( $new_instance['api_key'] ) ) ? strip_tags( $new_instance['api_key'] ) : '';
		$instance['token_key'] = ( ! empty( $new_instance['token_key'] ) ) ? strip_tags( $new_instance['token_key'] ) : '';
		$instance['num_events'] = ( ! empty( $new_instance['num_events'] ) ) ? strip_tags( $new_instance['num_events'] ) : '';
		$instance['cat_ids'] = ( ! empty( $new_instance['cat_ids'] ) ) ? strip_tags( $new_instance['cat_ids'] ) : '';
		$instance['affil_code'] = ( ! empty( $new_instance['affil_code'] ) ) ? strip_tags( $new_instance['affil_code'] ) : '';
		$instance['show_pagi'] = ( ! empty( $new_instance['show_pagi'] ) ) ? strip_tags( $new_instance['show_pagi'] ) : '';
		$instance['show_modif'] = ( ! empty( $new_instance['show_modif'] ) ) ? strip_tags( $new_instance['show_modif'] ) : '';
		$instance['show_mode'] = ( ! empty( $new_instance['show_mode'] ) ) ? strip_tags( $new_instance['show_mode'] ) : '';
		return $instance;
	}

} // class Quicket_Widget

// register Quicket_Widget widget
function register_quicket_widget() {
    register_widget( 'Quicket_Widget' );
}
add_action( 'widgets_init', 'register_quicket_widget' );