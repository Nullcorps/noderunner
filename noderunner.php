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




add_shortcode('noderunner_links_from_here','noderunner_links_from_here');
function noderunner_links_from_here($atts,$content = null)
   {
   global $nl;
   $out = "<h4>Noderunner links from here:</h4>\n"; // . $nl;
   
   $post_id = get_the_ID();
   //$out .= "Post id: " . $post_id . $nl;
   
   //$nr = get_post_meta( $post_id, "noderunner" );
   $nr = noderunner_get_meta_values5($post_id, "page");
   
   
   //$out .= "Reading postmeta: " . print_r($nr, true) . $nl;
   $out .= "<ul>";
   
   foreach ($nr as $key=>$value)
      {
      //$out .= "link to: " . $key . $nl;
      $post = get_post($key, "object");
      
      $url = get_permalink($key);
      $out .= "<li><a href=\"" . $url . "\">";
      $out .= $post->post_title . $nl;
      $out .= "</a></li>";
      }
   
   $out .= "</ul>";
   
   return do_Shortcode($out);
   }





add_shortcode('noderunner_links_to_here','noderunner_links_to_here');
function noderunner_links_to_here($atts,$content = null)
   {
   global $nl;
   $out = "<h4>Noderunner links to here:</h4>\n"; // . $nl;
   
   $this_post = get_the_ID();
   //$out .= "This post: " . $this_post . $nl;
   
   $nr = noderunner_get_meta_values6( $this_post, "page" );
   
   //$out .= print_r($nr, true) . $nl;
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
   
   foreach ($nr as $key=>$value)
      {
      //$out .= "link to: " . $key . $nl;
      $post = get_post($value, "object");
      
      $url = get_permalink($value);
      $out .= "<li><a href=\"" . $url . "\">";
      $out .= $post->post_title . $nl;
      $out .= "</a></li>";
      }
   
   $out .= "</ul>";
   
   //$out .= print_r($u, true) . $nl;

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
   
   $out = "";
   $out .= "<style>
.nr-add-link-select
{
aborder: 1px solid red;
height: 30px;
}
   
</style>";
   
   
   $out .= "<h4>Noderunner create a link:</h4>";
   $this_post = get_the_ID();
   //$out .= "This post: " . $this_post . $nl;
   
   
   
   $link_from = "";
   $link_to = "";
   $user_id = get_current_user_id();
   
   $is_admin = nr_user_has_role($user_id, "administrator");
   
   if ( $is_admin )
      {
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
            }
         }
      
      }
   
   
   
   $out .= "Link from: ";
   $out .= "<a href=\"#\" onclick=\"document.getElementById('nr_create_link_from').value=" . $this_post . "; return false;\">";
   $out .= "[this page: " . $this_post . "]" . $nl;
   $out .= "</a>";
   
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
   
   $out .= "Link to: ";
   $out .= "<a href=\"#\" onclick=\"document.getElementById('nr_create_link_to').value=" . $this_post . "; return false;\">";
   $out .= "[this page: " . $this_post . "]";
   $out .= "</a>" . $nl;
   
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
   $out .= "From: <input style=\"display: inline; width: 60px; height: 30px;\" type=text name=nr_create_link_from id=nr_create_link_from size=4> ";
   $out .= "To: <input style=\"display: inline; width: 60px; height: 30px;\" type=text name=nr_create_link_to id=nr_create_link_to size=4> ";
   $out .= "&nbsp; <input type=submit value=\"Add link\">";
   $out .= "</div>";
   $out .= "</form>";
   
   
   $out .= $nl;
   //$out .= "So basically for now what we need is a pair of textboxes ";
   //$out .= "to say what page/post to link from/to. Then have dropdowns ";
   //$out .= "for posts, and pages (for the source), and then another ";
   //$out .= "set of dropdowns for the post/page it's linking to";
   
   //$out .= "I wonder if one could have a sort of basic [noderunner] tag ";
   //$out .= "which one could put in a page/node if there wasn't any actual content ";
   //$out .= "but you could use it as a sort of 'menu/contents' thing which ";
   //$out .= "is auto-generated by the links to/from that node";
   //$out .= "(perhaps hide the nav widgets in that case so not doubling up?";
   return do_Shortcode($out);
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





function noderunner_get_meta_values5( $post_id, $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    
    //echo "IN HERE" . $nl;
    
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
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
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
   
   foreach ($r as $rr)
      {
      //$t[$rr] = $s[$cnt];
      $t[$rr] = $s[$cnt];
      $cnt++;
      }
   
    return $t;
}






function noderunner_get_meta_values6( $post_id, $type = 'post', $status = 'publish' ) {

    global $wpdb;
    global $nl;
    
    
    //echo "IN HERE" . $nl;
    
    $sql = "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key LIKE 'noderunner_%'
        AND p.post_status = %s 
        AND p.post_type = %s
    ";
    if ( strval($post_id) <> "" && is_numeric($post_id) )
      {
      $sql .= "\n AND pm.meta_value = %s";
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type, $post_id ) );
      }
   else
      {
      $sql .= "\n ORDER BY pm.meta_id DESC";
      $r = $wpdb->get_col( $wpdb->prepare( $sql, $status, $type ) );
      }
   
   $sql = "
        SELECT pm.post_id FROM {$wpdb->postmeta} pm
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
   
   $t = array();
   $cnt = 0;
   
   foreach ($r as $rr)
      {
      //$t[$rr] = $s[$cnt];
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
   
   
   