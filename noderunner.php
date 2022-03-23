<?php

defined('ABSPATH') or die("No script kiddies please!");


/**
 * Plugin Name: Noderunner
 * Plugin URI:
 * Description: hopefully a WP implementation of somethign resembling the old noderunner system.
 * 
 * Version: 1.0.0
 * Author: NullCorps
 * Author URI: 
 * Text Domain:
 * Domain Path:
 * Network:
 * License: GPL3
 */


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$nl = "<br>";

$noderunner_home_id    = get_option( 'noderunner_home_id' );





// =============== ADMIN PAGE STUFF

if ( is_admin() )
   {  // admin actions
   //add_action( 'admin_menu', 'add_mymenu' );
   //add_options_page( 'P8-Statto', 'P8-Statto', 'administrator', 'P8-statto/settings.php', 'statto_admin_page', 'dashicons-tickets', 6  );
   add_action( 'admin_menu', 'woo_member_admin_menu' );
   add_action( 'admin_init', 'woo_member_register_settings' );
   }
   else
   {
   // non-admin enqueues, actions, and filters
   }

function woo_member_register_settings() { // whitelist options
  register_setting( 'Noderunner', 'noderunner_home_id' );  
}

//add_action( 'admin_menu', 'botfink_admin_menu' );

function woo_member_admin_menu() {
	// add_menu_page( 'My Top Level Menu Example', 'P8-Botfink', 'manage_options', 'myplugin/myplugin-admin-page.php', 'botfink_admin_page', 'dashicons-tickets', 6  );
   add_options_page( 'Noderunner', 'Noderunner', 'administrator', 'Noderunner/admin-page.php', 'noderunner_admin_page', 'dashicons-tickets', 6  );
}








function noderunner_admin_page(){
   global $nl;

	?>
	<div class="wrap">
		<h2>WooCommerce membership by role configuration</h2>


		<form method="post" action="options.php">
		<?php
		settings_fields( 'Noderunner' );
		do_settings_sections( 'Noderunner' );
		//add_settings_field( $id, $title, $callback, $page, $section, $args );
		?>
		  <table class="form-table" border=0>

        <tr valign="top">
        <th scope="row">Noderunner "Home" id:</th>
        <td><input type="text" size=50 name="noderunner_home_id" value="<?php echo esc_attr( get_option('noderunner_home_id') ); ?>" /><br>
        ID of the page or post which should be the "starting node" for Noderunner, and should be displayed on the home/feed page.
        </td>
        </tr>
    </table>
		<?php submit_button(); ?>
      </form>
	</div>
	<?php

   echo "noderunner_home_id: "              . get_option( 'noderunner_home_id' )  . $nl;

   
   
}



























/*
 * Reminder of how this works:
 * create "links from" a post by adding meta values under the tag "noderunner"
 * with a post-id value for the post to link to
 *
 * for "links to this node" use function noderunner_get_meta_values3 to get a
 * list of all noderunner values. Iterate thru the list for any which match
 * current post id. Hey presto.
 *
 *  Except that doesn't give us the origin post id does it?
 *  How do we link back to it?
 *
 */



add_shortcode('noderunner_links_from_here','noderunner_links_from_here');
function noderunner_links_from_here($atts,$content = null)
   {
   global $nl;
   global $noderunner_home_id;
   
   $is_home = is_home();
   //$out .= "has_posts: " . $has_posts;

 
  
   if ( $is_home )
      {
      //$recent_posts = wp_get_recent_posts( array( 'numberposts' => '1' ) );
      //$post_id = $recent_posts[0]['ID'];
      $post_id = $noderunner_home_id; 
      }
   else
      {
      $post_id = get_the_ID();
      }
   //$out .= "Post id: " . $post_id . $nl;
   
   //$out .= "add the is_home() check for feed/posts pages" . $nl;
   
   //$nr = get_post_meta( $post_id, "noderunner" );
   $nrpage = noderunner_get_meta_values5a($post_id, "page");
   
   $nrpost = noderunner_get_meta_values5a($post_id, "post");
   
   //$out .= "NRPAGE:" . $nl . print_r($nrpage, true) . $nl;
   
   //$out .= "NRPOST:" . $nl . print_r($nrpost, true) . $nl;
   
   
   
   $nr = array();
   
   $cnt = 0;
   
   foreach ( $nrpage as $key=>$value )
      {
      //$nr[$cnt] = $value;
      $nr[$key] = $value;
      //$out .= $key . " -> " . $value . $nl;
      $cnt++;
      }

   foreach ( $nrpost as $key=>$value )
      {
      //$nr[$cnt] = $value;
      $nr[$key] = $value;
      //$out .= $key . " -> " . $value . $nl;
      $cnt++;
      }
    
   
   //$nr = $nrpost;
   $user_id = get_current_user_id();
   $is_admin = nr_user_has_role($user_id, "administrator");
   
   //$nr = $nrpost;
   //$out .= "Reading postmeta: " . print_r($nr, true) . $nl;
   //$out .= "<div class=\"nr-container nr-links-from-here-container\">";
   $out .= "<h4>Noderunner links from here:</h4>\n"; // . $nl;
   
   if ( is_array($nr) )
      {
      //$out .= "It's an array" . $nl;
      //$out .= "Count: " . count($nr);
      if (count($nr) > 0 )
         {
         //$out .= "got here";
         
         $out .= "<ul>";
         
         foreach ($nr as $key=>$value)
            {
            //$out .= "link to: " . $key . " => " . $value . $nl;
            $post = get_post($value, "object");
            
            $url = get_permalink($value);
            $out .= "<li style=\"font-size: 18px; line-height: 140%; \"><a href=\"" . $url . "\">";
            $out .= $post->post_title;
            $out .= "</a>";
            
            if ( $is_admin )
               {
               $out .= " <a href=\"?key=" . $key . "&post=" . $post_id . "\" style=\"font-size: 12px;\">&#10060;</a>";
               }
            
            $out .= "</li>";
            }
         
         $out .= "</ul>";
         }
      else
         {
         $out .= "[none yet]";
         }
      $out .= "<div style=\"text-align: right;\">";
      $out .= "<a href=\"#\" onclick=\"location.href='" . get_page_link() . "';\">";
      $out .= "&circlearrowright;";
      $out .= "</a>";
      $out .= "</div>";
      }
   //$out .= "</div>";
   return do_Shortcode($out);
   }














add_shortcode('noderunner_links_to_here','noderunner_links_to_here');
function noderunner_links_to_here($atts,$content = null)
   {
   global $nl;
   
   //$out .= "<div class=\nr-container nr-links-to-here-container\">";
   $out = "<h4>Noderunner links to here:</h4>\n"; // . $nl;
   
   //$this_post = get_the_ID();
   //$out .= "This post: " . $this_post . $nl;
   
   $is_home = is_home();
   //$out .= "has_posts: " . $has_posts . $nl;
   
   $user_id = get_current_user_id();
   $is_admin = nr_user_has_role($user_id, "administrator");
   
   
   if ( $is_home )
      {
      $recent_posts = wp_get_recent_posts( array( 'numberposts' => '1' ) );
      $post_id = $recent_posts[0]['ID'];
      }
   else
      {
      $post_id = get_the_ID();
      }
   //$out .= "Post id: " . $this_post . $nl;
   
   
   //$nr = noderunner_get_meta_values6( $this_post, "page" );
   
   $nrpage = noderunner_get_meta_values6a($post_id, "page");
   
   $nrpost = noderunner_get_meta_values6a($post_id, "post");
   
   //$out .= print_r($nrpage, true) . $nl;
   $nr = array();
   
   $cnt = 0;
   
   foreach ( $nrpage as $key=>$value )
      {
      $nr[$key] = $value;
      //$out .= $key . " -> " . $value . $nl;
      $cnt++;
      }

   foreach ( $nrpost as $key=>$value )
     {
     $nr[$key] = $value;
     //$out .= $key . " -> " . $value . $nl;
     $cnt++;
     }
   
   //$out .= $nl;
   
   //$out .= "nr_merged: " . print_r($nr, true) . $nl;
   //$out .= "NR_ALL: " . print_r($nr_all, true) . $nl;
   ////$out .= $nl . implode( '<br />', $nr_all );
   //$out .= $nl; 
   //
   //$u = array();
   //
   //foreach ( $nr_all as $key=>$value )
   //   {
   //   $out .= "\"" . $key . "\" from post: " . $value . $nl;
   //   if ( $key == $this_post )
   //      { $u[$key] = $value; }
   //   }
 
   $out .= "<ul>";
   $done_the_thing = false;
   
   foreach ($nr as $key=>$value)
      {
      //$out .= "link to: " . $value . $nl;
      //$out .= "key: " . $key . $nl;
      //$out .= "value: " . $value . $nl;
         
      if ( 1==1 )
         {
         $post = get_post($value, "object");
         $url = get_permalink($value);
         $out .= "<li style=\"font-size: 18px; line-height: 140%; \"><a href=\"" . $url . "\">";
         $out .= $post->post_title;
         $out .= "</a>";
         if ( $is_admin )
            {
            $out .= " <a href=\"?key=" . $key . "&post=" . $value . "\" style=\"font-size: 12px;\">&#10060;</a>";
            }
            
         $out .= "</li>";
         $done_the_thing = true;
         }
      }
   
   $out .= "</ul>";
   
   
   if ( count($nr) == 0 || !$done_the_thing )
      { $out .= "[none yet]"; }
   //$out .= print_r($u, true) . $nl;

   $out .= "<div style=\"text-align: right;\">";
   $out .= "<a href=\"#\" onclick=\"location.href='" . get_page_link() . "';\">";
   $out .= "&circlearrowright;";
   $out .= "</a>";
   $out .= "</div>";
   
   //$out .= "</div>";
   
   return do_Shortcode($out);
   }










function nr_user_has_role($user_id, $role_or_cap) {

    $u = new \WP_User( $user_id );
    //$u->roles Wrong way to do it as in the accepted answer.
    $roles_and_caps = $u->get_role_caps(); //Correct way to do it as wp do multiple checks to fetch all roles

    if( isset ( $roles_and_caps[$role_or_cap] ) and $roles_and_caps[$role_or_cap] === true ) 
       {
           return true;
       }
 }   
   











add_shortcode('noderunner_create_a_link','noderunner_create_a_link');
function noderunner_create_a_link($atts,$content = null)
   {
   global $nl;
   
   
   $user_id = get_current_user_id();
   
   $is_admin = nr_user_has_role($user_id, "administrator");
   
   if ( $is_admin )
      {   
      $out = "";
      $out .= "<style>
.nr-add-link-select
{
aborder: 1px solid red;
height: 30px;
}

.nr-create-link-subtitle
{ margin-bottom: 8px; }
</style>";
      

      
      //$out .= "<div class=\"nr-container nr-create-a-link-container\">";
      $out .= "<h4>Noderunner create a link:</h4>";
      //$this_post = get_the_ID();
      //$out .= "This post: " . $this_post . $nl;
      
      
      $is_home = is_home();
      //$out .= "has_posts: " . $has_posts . $nl;
      
      if ( $is_home )
         {
         $recent_posts = wp_get_recent_posts( array( 'numberposts' => '1' ) );
         $this_post = $recent_posts[0]['ID'];
         }
      else
         {
         $this_post = get_the_ID();
         }
      //$out .= "Post id: " . $this_post . $nl;
    
    
    
          //$out .= "Admin" . $nl;
      if ( isset($_GET['key']) && isset($_GET['post']) )
         {
         $key = sanitize_text_field($_GET['key']);
         $post = sanitize_text_field($_GET['post']);
         
         if ( strval($key) <> "" && strval($post) <> "" )
            {
            $del = delete_post_meta( $post, $key );
            //$out .= "Deleted: " . $del . $nl;
            //$out .= "Reload this post: " . $this_post . $nl;
            $pl = get_permalink($this_post);
            //$out .= "Permalink: " . $pl . $nl;
            $out .= "<script language=javascript>location.href='" . $pl . "';</script>";
            }
         }
    
    
      $link_from = "";
      $link_to = "";
    

      //$out .= "is_admin" . $nl . $nl;
      if ( isset($_POST['nr_create_link_from']) )
         {
         $link_from = sanitize_text_field($_POST['nr_create_link_from']);
         }
         
      if ( isset($_POST['nr_create_link_to']) )
         {
         $link_to = sanitize_text_field($_POST['nr_create_link_to']);
         }
      
      if ( $link_from <> "" && $link_to <> "" )
         {
         $out .= "Got both links: " . $link_from . " -> " . $link_to . $nl;
         $out .= "Does this link exist already?" . $nl;
         $nr = get_post_meta( $link_from, "noderunner" );
         
         $out .= print_r($nr, true) . $nl;
         
         $exists = false;
         
         foreach ( $nr as $n )
            {
            $out = "nr: " . $n . $nl;
            if ( $link_to == $n )
               { $exists = true; }
            }
            
         
         if ( !$exists )   
            {
            $out .= "link doesn't seem to exist already, creating" . $nl; 
            //update_post_meta( $link_from, "noderunner", array($link_to) );
            $rand = md5(microtime());
            $rand = substr($rand,4,10);
            //echo "RAND: " . $rand . $nl;
            update_post_meta( $link_from, "noderunner_" . $rand, $link_to );
            $pl = get_permalink($this_post);
            //$out .= "Permalink: " . $pl . $nl;
            $out .= "<script language=javascript>location.href='" . $pl . "';</script>";
            }
         }
      

   
   
      
      $out .= "<div class=\"nr-create-link-subtitle\">Link from: ";
      $out .= "<a href=\"#\" onclick=\"document.getElementById('nr_create_link_from').value=" . $this_post . "; return false;\">";
      $out .= "[ this page: " . $this_post . " ] or:" . $nl;
      $out .= "</a>";
      $out .= "</div>";
      
      $args = array( 'numberposts' => 20 );
   
      $posts = get_pages($args);
      $out .= "<select id=nr_pages_from class=\"nr-add-link-select\" onchange=\"document.getElementById('nr_create_link_from').value=this[this.selectedIndex].value;\">";
      $out .= "<option>- Select a page -</option>";
      foreach ( $posts as $post )
         {
         //$out .= $post->post_title . $nl;
         $out .= "<option value=" . $post->ID . " >" . $post->post_title . "</option>";
         }
      $out .= "</select>";
   
   
      $posts = get_posts($args);
      $out .= "<select id=nr_posts_from class=\"nr-add-link-select\" onchange=\"document.getElementById('nr_create_link_from').value=this[this.selectedIndex].value;\">";
      $out .= "<option>- Select a post -</option>";
      foreach ( $posts as $post )
         {
         //$out .= $post->post_title . $nl;
         $out .= "<option value=" . $post->ID . " >" . $post->post_title . "</option>";
         }
      $out .= "</select>";
      
      
      
      $out .= $nl . $nl;
      
      $out .= "<div class=\"nr-create-link-subtitle\">Link to: ";
      $out .= "<a href=\"#\" onclick=\"document.getElementById('nr_create_link_to').value=" . $this_post . "; return false;\">";
      $out .= "[ this page: " . $this_post . " ] or:";
      $out .= "</a>" . $nl;
      $out .= "</div>";
      
      $args = array( 'numberposts' => 20 );
   
      $posts = get_pages($args);
      $out .= "<select id=nr_pages_to class=\"nr-add-link-select\" onchange=\"document.getElementById('nr_create_link_to').value=this[this.selectedIndex].value;\">";
      $out .= "<option>- Select a page -</option>";
      foreach ( $posts as $post )
         {
         //$out .= $post->post_title . $nl;
         $out .= "<option value=" . $post->ID . " >" . $post->post_title . "</option>";
         }
      $out .= "</select>";
   
      $posts = get_posts($args);
      $out .= "<select id=nr_posts_to class=\"nr-add-link-select\" onchange=\"document.getElementById('nr_create_link_to').value=this[this.selectedIndex].value;\">";
      $out .= "<option>- Select a post -</option>";
      foreach ( $posts as $post )
         {
         //$out .= $post->post_title . $nl;
         $out .= "<option value=" . $post->ID . " >" . $post->post_title . "</option>";
         }
      $out .= "</select>";
   
   

   
   
   
      $out .= "<div style=\"margin-top: 12px; \">";
      $out .= "<form method=post id=nr_create_link>";
      $out .= "From: <input style=\"display: inline; width: 70px; height: 30px;\" type=text name=nr_create_link_from id=nr_create_link_from size=4> ";
      $out .= "To: <input style=\"display: inline; width: 70px; height: 30px;\" type=text name=nr_create_link_to id=nr_create_link_to size=4> ";
      $out .= "&nbsp; <input type=submit value=\"Add\" class=\"nr-add-link-button\">";
      $out .= "</div>";
      $out .= "</form>";
      
      //$out .= "Post_id: " . $this_post . $nl;
      $out .= $nl;
      
      $out .= "todo:<Br>- add 'Create link to NEW page/post'! Build page structure super quickly with few clicks. ";
      $out .= "Set page title and maybe a standard message in the form" . $nl;
      //$out .= "So basically for now what we need is a pair of textboxes ";
      //$out .= "to say what page/post to link from/to. Then have dropdowns ";
      //$out .= "for posts, and pages (for the source), and then another ";
      //$out .= "set of dropdowns for the post/page it's linking to";
      
      //$out .= "I wonder if one could have a sort of basic [noderunner] tag ";
      //$out .= "which one could put in a page/node if there wasn't any actual content ";
      //$out .= "but you could use it as a sort of 'menu/contents' thing which ";
      //$out .= "is auto-generated by the links to/from that node";
      //$out .= "(perhaps hide the nav widgets in that case so not doubling up?";
      
      //$out .= "</div>";
      
      }
   return do_Shortcode($out);
   }












function noderunner_get_meta_values5( $post_id, $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    
    //echo "IN HERE" . $nl;
    //echo "POST ID: " . $post_id . $nl;
    
    $sql = "
        SELECT pm.post_id FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ";
    if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND pm.post_id = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   //echo $nl . $sql . $nl;
         
   $sql = "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ";
    
   if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND pm.post_id = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
   // old like nwas LIKE: WHERE pm.meta_key LIKE 'noderunner%'  << note extra %
   // but we're just matching on 'noderunner' now so doesn't need it
   
   $t = array();
   $cnt = 0;
   
   //echo "R: " . $nl;
   //print_r($r);

   //echo "S: " . $nl;
   //print_r($s);
   //echo $nl;
   
   foreach ($r as $rr)
      {
      //$t[$rr] = $s[$cnt];
      //echo "rr: " . $rr . " - s[cnt]: " . $s[$cnt] . $nl;
      $t[$cnt] = $s[$cnt];
      $cnt++;
      }
      
   //echo $nl . "T: " . $nl;
   //print_r($t);
    return $t;
}






function noderunner_get_meta_values5a( $post_id, $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    
    //echo "IN HERE" . $nl;
    //echo "POST ID: " . $post_id . $nl;
    
    $sql = "
        SELECT pm.meta_key FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ";
    if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND pm.post_id = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   //echo $nl . $sql . $nl;
         
   $sql = "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ";
    
   if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND pm.post_id = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
   // old like nwas LIKE: WHERE pm.meta_key LIKE 'noderunner%'  << note extra %
   // but we're just matching on 'noderunner' now so doesn't need it
   
   $t = array();
   $cnt = 0;
   
   //echo "R: " . $nl;
   //print_r($r);

   //echo "S: " . $nl;
   //print_r($s);
   //echo $nl;
   
   foreach ($r as $rr)
      {
      //$t[$rr] = $s[$cnt];
      //echo "rr: " . $rr . " - s[cnt]: " . $s[$cnt] . $nl;
      //$t[$cnt] = $s[$cnt];
      $t[$rr] = $s[$cnt];
      $cnt++;
      }
      
   //echo $nl . "T: " . $nl;
   //print_r($t);
    return $t;
}






function noderunner_get_meta_values6( $post_id, $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    
    //echo "IN HERE" . $nl;
    //echo "post_id: " .  $post_id . $nl;
    
    $sql = "
        SELECT pm.post_id FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'";
    
    if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND p.post_status = %s"; 
      $sql .= "\n AND p.post_type = %s";
      $sql .= "\n AND pm.meta_value = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n AND p.post_status = %s"; 
      $sql .= "\n AND p.post_type = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
   //print_r($sql);
   //echo $nl;
   //
   //$sql = "
   //     SELECT pm.meta_value FROM {$wpdb->postmeta} pm
   //     LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
   //     WHERE pm.meta_key LIKE 'noderunner_%'
   //     AND p.post_status = %s 
   //     AND p.post_type = %s
   // ";
   // 
   //if ( strval($post_id) <> "" && is_numeric($post_id) )
   //   {
   //   $sql .= "\n AND pm.meta_value = %s";
   //   $sql .= "\n ORDER BY pm.meta_id DESC";
   //   $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
   //   }
   //else
   //   {
   //   $sql .= "\n ORDER BY pm.meta_id DESC";
   //   $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
   //   }
   
   // old like nwas LIKE: WHERE pm.meta_key LIKE 'noderunner%'  << note extra %
   // but we're just matching on 'noderunner' now so doesn't need it
   //echo "R (get_meta_values6):" . $nl;
   //print_r($r);
   //echo $nl;
   
   //echo "S:" . $nl;
   //print_r($s);
   //echo $nl;
   
   //$t = array();
   //$cnt = 0;
   //
   //foreach ($r as $rr)
   //   {
   //   //$t[$rr] = $s[$cnt];
   //   $t[$cnt] = $s[$cnt];
   //   $cnt++;
   //   }
   //
   //echo $nl;
   //print_r($t);
   //echo $nl;
   
   return $r;
}





function noderunner_get_meta_values6a( $post_id, $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    
    //echo "IN HERE" . $nl;
    //echo "post_id: " .  $post_id . $nl;
    
    $sql = "
        SELECT pm.post_id FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'";
    
    if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND p.post_status = %s"; 
      $sql .= "\n AND p.post_type = %s";
      $sql .= "\n AND pm.meta_value = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n AND p.post_status = %s"; 
      $sql .= "\n AND p.post_type = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
   //print_r($sql);
   //echo $nl;
   
   $sql = "
        SELECT pm.meta_key FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ";
    
   if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND pm.meta_value = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $s = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
   // old like nwas LIKE: WHERE pm.meta_key LIKE 'noderunner%'  << note extra %
   // but we're just matching on 'noderunner' now so doesn't need it
   //echo "R (get_meta_values6):" . $nl;
   //print_r($r);
   //echo $nl;
   
   //echo "S:" . $nl;
   //print_r($s);
   //echo $nl;
   
   $t = array();
   $cnt = 0;
   
   foreach ($r as $rr)
      {
      //$t[$rr] = $s[$cnt];
      
      //$t[$rr] = $s[$cnt];
      $t[$s[$cnt]] = $rr;
      $cnt++;
      }
   
  // echo $nl;
   //print_r($t);
   //echo $nl;
   
   return $t;
}


























































function noderunner_get_meta_values2( $meta_key,  $post_type = 'post' ) {

    $posts = get_posts(
        array(
            'post_type' => $post_type,
            'meta_key' => $meta_key,
            'posts_per_page' => -1,
        )
    );

    $meta_values = array();
    foreach( $posts as $post ) {
        $meta_values[] = get_post_meta( $post->ID, $meta_key, true );
    }

    return $meta_values;

}




function noderunner_get_meta_values3( $key = '', $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    if( empty( $key ) )
        return;
    echo "IN HERE" . $nl;
    
    $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ", $status, $type ) );
   
   // old like nwas LIKE: WHERE pm.meta_key LIKE 'noderunner%'  << note extra %
   // but we're just matching on 'noderunner' now so doesn't need it
   
    return $r;
}



function noderunner_get_meta_values4( $key = '', $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    if( empty( $key ) )
        return;
    echo "IN HERE" . $nl;
    
    $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ", $status, $type ) );
   
   
   $s = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.post_id FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ", $status, $type ) );
   // old like nwas LIKE: WHERE pm.meta_key LIKE 'noderunner%'  << note extra %
   // but we're just matching on 'noderunner' now so doesn't need it
   
   $t = array();
   $cnt = 0;
   
   foreach ($r as $rr)
      {
      $t[$rr] = $s[$cnt];
      $cnt++;
      }
   
    return $t;
}










function noderunner_get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

    global $wpdb;

    if( empty( $key ) )
        return;
 
    $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ", $status, $type ) );


   // original
    //$r = $wpdb->get_col( $wpdb->prepare( "
    //    SELECT pm.meta_value FROM {$wpdb->postmeta} pm
    //    LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    //    WHERE pm.meta_key = %s
    //    AND p.post_status = %s 
    //    AND p.post_type = %s
    //", $key, $status, $type ) );
   
    
    
    return $r;
}











add_shortcode('noderunner_write_test','noderunner_write_test');
function noderunner_write_test($atts,$content = null)
   {
   global $nl;
   $out = "IN NODERUNNER WRITE TEST" . $nl;
   
   $post_id = get_the_ID();
   $out .= "Post id: " . $post_id . $nl;
   
   update_post_meta( $post_id, "noderunner", "noderunner_target_post_id" );
   update_post_meta( 116, "noderunner", "noderunner_target_post_id2" );
   update_post_meta( 2, "noderunner", "noderunner_target_post_id3" );
   
   update_post_meta( $post_id, "noderunner", "740" );
   update_post_meta( $post_id, "noderunner_001", "some other post" );
   update_post_meta( $post_id, "noderunner_002", "yet a diff post" );
   
   return do_shortcode($out);
   }





add_shortcode('noderunner_read_test','noderunner_read_test');
function noderunner_read_test($atts,$content = null)
   {
   global $nl;
   $out = "IN NODERUNNER READ TEST" . $nl;
   
   
   $this_post = get_the_ID();
   $out .= "This post: " . $this_post . $nl;
   
   $post_id = 737; // get_the_ID();
   $out .= "Post id: " . $post_id . $nl;
   
   $nr = get_post_meta( $post_id, "noderunner" );
   $out .= "Reading postmeta: " . print_r($nr, true) . $nl;
   
   
   //$nr_all = noderunner_get_meta_values( 'noderunner', "page" );
   
   $nr_all = noderunner_get_meta_values( "noderunner", "page" );
   
   $out .= "NR_ALL: " . print_r($nr_all, true) . $nl;
   $out .= implode( '<br />', $nr_all );
   
   $out .= $nl . $nl;
   
   $nr_all = noderunner_get_meta_values4( "noderunner", "page" );
   
   $out .= "NR_ALL: " . print_r($nr_all, true) . $nl;
   //$out .= $nl . implode( '<br />', $nr_all );
   $out .= $nl; 
   
   foreach ( $nr_all as $key=>$value )
      {
      $out .= "\"" . $key . "\" from post: " . $value . $nl;
      }
   
   $out .= $nl;
   $out .= "if this works then that should be a flat list of all noderunner links which we can iterate through quickly to find the reverse links too" . $nl;
   $out .= $nl;

      
   return do_shortcode($out);
   }













// WORKS BUT WRONG TYPE OF LIKE   
   //$q2 = new WP_Query(
   //               array(
   //                  'post_type' => 'page',
   //                  'metaquery' => array(
   //                                    array(
   //                                       'compare_key' => 'LIKE',
   //                                       'key'         => 'noderunner_',
   //                                         ),
   //                                       ),
   //                     )
   //                  );
   //
   //if ( $q2->have_posts() )
   //   {
   //   echo "HAS POSTS" . $nl;
   //   $ids_array = $q2->get_posts();
   //   $out .= "<pre>" . print_r($ids_array, true) . "</pre>" . $nl;
   //
   //   //$out .= print_r($ids_array, true) . $nl;
   //   
   //   
   //   
   //   //foreach ($ids_array as $id)
   //   //   {
   //   //   $out .= "ID: " . print_r($id,true) . $nl;
   //   //   }
   //      
   //   }
   //   
   
   
   