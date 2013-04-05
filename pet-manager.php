<?php
/*
Plugin Name: Pet Manager
Text Domain: wp_pet
Domain Path: /lang
Plugin URI: http://dianakcury.com/dev/pet-manager
Description: Pet Manager offers a aesy way to keep pet shelters websites
Version: 1.4
Author: Diana K. Cury
Author URI: http://dianakcury.com/
*/


  /*Definitions*/
		if ( !defined('WP_CONTENT_URL') )
		    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
		if ( !defined('WP_CONTENT_DIR') )
		    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

		if (!defined('PLUGIN_URL'))
		    define('PLUGIN_URL', WP_CONTENT_URL . '/plugins');
		if (!defined('PLUGIN_PATH'))
		    define('PLUGIN_PATH', WP_CONTENT_DIR . '/plugins');

		define('TC_FILE_PATH', dirname(__FILE__));
		define('TC_DIR_NAME', basename(TC_FILE_PATH));

    //include the main class file
    require_once PLUGIN_PATH .'/'.TC_DIR_NAME . '/inc/cpt-pets.php';
    require_once PLUGIN_PATH .'/'.TC_DIR_NAME . '/inc/extend.php';
    require_once PLUGIN_PATH .'/'.TC_DIR_NAME . '/inc/widgets.php';
    require_once PLUGIN_PATH .'/'.TC_DIR_NAME . '/inc/metabox/boxes.php';

    define( 'CMB_META_BOX_URL', PLUGIN_URL.'/'.TC_DIR_NAME . '/inc/metabox/' );





class PET_MANAGER {
	function __construct() {
		$this->PET_MANAGER();
	}

	function PET_MANAGER() {
		global $wp_version;

    add_theme_support( 'post-thumbnails' );
    add_image_size( 'pet_img', 200, 200, true );
    add_image_size( 'pet_mini', 50, 50, true );

	  }
  }


  //Starts everything
  add_action( 'init', 'petmanager_setup',1 );

  function petmanager_setup(){
    //Load the text domain, first of all
    load_plugin_textdomain('wp_pet', true, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

    //pet_add_pages();

    //Enables Pet and Lost Types
    $PETPostType = new PETPostType();

		//Register the post type
		add_action('init', array($PETPostType,'register'),3 );

    add_action( 'init', 'pet_add_pages');

    //Register pet type taxonomies
    add_action( 'init', 'create_pet_category_taxonomy');
    add_action( 'init', 'create_pet_color_taxonomy');
    add_action( 'init', 'create_pet_status_taxonomy');
    add_action( 'init', 'create_pet_genre_taxonomy');
    add_action( 'init', 'create_pet_age_taxonomy');
    add_action( 'init', 'create_pet_breed_taxonomy');
    add_action( 'init', 'create_pet_size_taxonomy');
    add_action( 'init', 'create_pet_coat_taxonomy');
    add_action( 'init', 'create_pet_pattern_taxonomy');

    add_action( 'init', 'pet_remove_pets_support');
    add_action( 'admin_menu' , 'remove_taxonomies_boxes' );
    add_shortcode( 'pet_shortcode', 'pet_shortcode_form' );
    add_shortcode( 'pet_search', 'pet_search_form' );
    add_filter('widget_text', 'do_shortcode');

    add_action( 'admin_print_styles','action_admin_print_styles' );

    //Widgets
    add_action('widgets_init', create_function('', 'return register_widget("PET_Widget_Searchform");'));
    add_action('widgets_init', create_function('', 'return register_widget("PET_Widget_Display");'));

  }

    function pet_especial_queries(){
        global $wp;
        $wp->add_query_var('meta_key');
        $wp->add_query_var('meta_value');
        $wp->add_query_var('meta_compare');
    }

  	function pet_remove_pets_support() {
  		remove_post_type_support( 'pet', 'excerpt' );
  		remove_post_type_support( 'pet', 'comments' );
  	}


    // Shortcode implementation
    function pet_shortcode_form() {

       ob_start();
       include(PLUGIN_PATH .'/'.TC_DIR_NAME .'/inc/form.php');
       $content = ob_get_clean();
       return $content;
    }


  function pet_form() {
    include('inc/form-action.php');
  }
  add_filter('get_header','pet_form');


    // Add needed scripts  array( 'jquery' )
    function pet_manager_scripts() {

	          wp_enqueue_script('jquery_check', plugins_url('/js/jquery_check.js', __FILE__), array('jquery')
	       );


    }
    add_action( 'wp_enqueue_scripts', 'pet_manager_scripts' );

    function pet_manager_stylesheet() {
        // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'pet-style', plugins_url('/inc/pet_styles.css', __FILE__) );
        wp_enqueue_style( 'pet-style' );
    }
    add_action( 'wp_enqueue_scripts', 'pet_manager_stylesheet' );


    /*Allow upload files*/
    function insert_attachment($file_handler,$post_id,$setthumb='false') {
    	// check to make sure its a successful upload
    	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

    	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    	$attach_id = media_handle_upload( $file_handler, $post_id );

    	if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
    	return $attach_id;
    }


    /* Create some pages such Pets and Add a pet */
    function pet_add_pages(){
        $pets = '';
        $addpet= '';

        if( get_page_by_title(__('Pets','wp_pet')) == false )
        $pets = array(
        'post_title' => __('Pets','wp_pet'),
        'post_name' => __('pet-list','wp_pet'),
        'post_type' => 'page',
        'post_status' =>'publish',
        'comment_status' => 'closed',
        );
        wp_insert_post( $pets );

        if( get_page_by_title(__('Add a pet','wp_pet')) == false )
        $addpet = array(
        'post_title' => __('Add a pet','wp_pet'),
        'post_type' => 'page',
        'post_content' => '[pet_shortcode]',
        'post_status' =>'publish',
        'comment_status' => 'closed',
        );
        wp_insert_post( $addpet );
    }

add_action('admin_menu', 'ada_add_pages');

// action function for above hook
function ada_add_pages() {

add_submenu_page('edit.php?post_type=pet', __('Options & About','wp_pet'), __('Options & About','wp_pet'), 'manage_options', 'pet_options_page', 'pet_options_page' );


}



function pet_options_page() {
    include('pet_options_page.php');
}


	function action_admin_print_styles() {
		global $current_screen;

	?>
<style type="text/css">

.pet-header{background: url('<?php echo PLUGIN_URL ."/".TC_DIR_NAME . '/inc/img/pet-32.png' ;?>') no-repeat left center;padding:4px 0 4px 30px}
.donate {font-size:10px;text-align:center; width:20%; margin-right:10px;float:left;padding:5px;background:#f7f7f7}
.donate:hover {background:#f6f6f6}
</style>
	<?php
	}


/*Record pet activity in BuddyPress*/

add_action( 'save_post', 'pet_activity_bp_log', 10, 2 );
function pet_activity_bp_log( $post_id, $post, $user_id = false ) {

if(function_exists('bp_is_active')){

    global $bp, $wpdb;

    $post_id = (int)$post_id;
    $blog_id = (int)$wpdb->blogid;

    if ( !$user_id )
    $user_id = (int)$post->post_author;


    /* Don't record this if it's not a post */
    if ( $post->post_type != 'pet' )
                        return false;


    if ( 'publish' == $post->post_status && '' == $post->post_password ) {
        if ( (int)get_blog_option( $blog_id, 'blog_public' ) || !bp_core_is_multisite() ) {
        /* Record this in activity streams */
        $post_permalink = get_permalink( $post_id );

        $thumb = '<a href="'.get_permalink($post_id).'"><figure>'.get_the_post_thumbnail($post_id,'pet_mini').'</figure></a>';
        $activity_action = sprintf( __( '%s added a new pet: %s', 'wp_pet' ), bp_core_get_userlink( (int)$post->post_author ), '<a href="' . $post_permalink . '">' . $post->post_title . '</a>');
        $activity_content = $post->post_content.$thumb;

        bp_blogs_record_activity( array(
                                        'user_id' => (int)$post->post_author,
                                        'action' => apply_filters( 'bp_blogs_activity_new_post_action', $activity_action, $post, $post_permalink ),
                                        'content' => apply_filters( 'bp_blogs_activity_new_post_content', $activity_action.$thumb, $post, $post_permalink ),
                                        'primary_link' => apply_filters( 'bp_blogs_activity_new_post_primary_link', $post_permalink, $post_id ),
                                        'type' => 'new_blog_post',
                                        'item_id' => $blog_id,
                                        'secondary_item_id' => $post_id,
                                        'recorded_time' => $post->post_date_gmt
                                ));
                        }
        } else
          bp_blogs_remove_post( $post_id, $blog_id );
          bp_blogs_update_blogmeta( $blog_id, 'last_activity', bp_core_current_time() );
          do_action( 'bp_blogs_new_blog_post', $post_id, $post, $user_id );
    }

}



$PET_MANAGER = new PET_MANAGER();


?>