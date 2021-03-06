<?php
/*
Plugin Name: Pet Manager
Text Domain: wp_pet
Domain Path: /lang
Plugin URI: https://github.com/tzinmein/pet-manager
Description: Pet Manager offers a easy way to keep pet shelters websites
Version: 1.7
Author: Diana K. Cury, Ho Tzin Mein
Author URI: https://github.com/tzinmein/
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
    add_shortcode('pet_list','pet_list_shortcode');

    add_filter('widget_text', 'do_shortcode');

    add_action( 'admin_print_styles','action_admin_print_styles' );

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

add_action( 'wp_enqueue_scripts', 'themename_scripts' );
function themename_scripts() {
    wp_enqueue_style( 'themename-style', get_stylesheet_uri(), array( 'dashicons' ), '1.0' );
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

add_post_type_support( 'pet', 'buddypress-activity' );

function customize_pet_tracking_args() {
    // Check if the Activity component is active before using it.
    if ( ! bp_is_active( 'activity' ) ) {
        return;
    }
 
    bp_activity_set_post_type_tracking_args( 'pet', array(
        'bp_activity_admin_filter' => __( 'Published a new pet', 'custom-domain' ),
        'bp_activity_front_filter' => __( 'Pets', 'custom-domain' ),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __( '%1$s added a new <a href="%2$s">pet</a>', 'custom-textdomain' ),
        'bp_activity_new_post_ms'  => __( '%1$s added a new <a href="%2$s">pet</a>, on the site %3$s', 'custom-textdomain' ),
        'position'                 => 100
    ) );
}
add_action( 'bp_init', 'customize_pet_tracking_args' );

$PET_MANAGER = new PET_MANAGER();

?>