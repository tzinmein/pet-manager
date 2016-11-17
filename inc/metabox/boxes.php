<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'pet_sample_metaboxes' );



/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function pet_sample_metaboxes( array $meta_boxes ) {


	// Start with an underscore to hide fields from custom fields list
	$prefix = '_data_';

	$meta_boxes[] = array(
		'id'         => 'pet_profile',
		'title'      => 'Pet Manager',
		'pages'      => array( 'pet' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(

			array(
				'name' => __('Pet Info','wp_pet'),
				'desc' => __('Fill the pet information here, you can add and change all info anytime.','wp_pet'),
				'id'   => $prefix . 'pet_title_2',
				'type' => 'title',
			),
			array(
				'name' => __('#ID / Microchip','wp_pet'),
				'desc' => '',
				'id'   => $prefix . 'pet_id',
				'type' => 'text_medium',
			),				
		     array(
		     	'name' => __('Category','wp_pet'),
		     	'id' => $prefix . 'pet_category',
		     	'taxonomy' => 'pet-category',
		     	'type' => 'taxonomy_select',
		     ),
			array(
				'name'    => __('Status','wp_pet'),
				'desc'    => 'field description (optional)',
				'id'      => $prefix . 'pet_status',
				'type'    => 'taxonomy_select',
				'taxonomy'    => 'pet-status'
			),
			array(
		       	'name' => __('Sex','wp_pet'),
		       	'id' => $prefix . 'pet_gender',
		       	'taxonomy' => 'pet-gender',
		       	'type' => 'taxonomy_select',
			),
			array(
		       	'name' => __('Age','wp_pet'),
		       	'id' => $prefix . 'pet_age',
		       	'taxonomy' => 'pet-age',
		       	'type' => 'taxonomy_select',
			),
			array(
				'name' => __('Breed','wp_pet'),
				'id'   => $prefix . 'pet_breed',
       			'taxonomy' => 'pet-breed',
				'type' => 'taxonomy_multicheck',
			),
			array(
		       	'name' => __('Size','wp_pet'),
		       	'id' => $prefix . 'pet_size',
		       	'taxonomy' => 'pet-size',
		       	'type' => 'taxonomy_select',
			),
      		array(
		      	'name' => __('Coat','wp_pet'),
		      	'id' => $prefix . 'pet_coat',
		      	'taxonomy' => 'pet-coat',
		      	'type' => 'taxonomy_multicheck',
      		),
		    array(
		      	'name' => __('Pattern','wp_pet'),
		      	'id' => $prefix . 'pet_pattern',
		      	'taxonomy' => 'pet-pattern',
		      	'type' => 'taxonomy_select',
		    ),
		    array(
		      	'name' => __('Colors','wp_pet'),
		      	'id' => $prefix . 'pet_color',
		      	'taxonomy' => 'pet-color',
		      	'type' => 'taxonomy_multicheck',
		    ),
			array(
				'name'    => __('Vaccines','wp_pet'),
				'id'      => $prefix . 'pet_vaccines',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => __('Vacinated','wp_pet'), 'value' => __('Vacinated','wp_pet'), ),
					array( 'name' => __('Dose Interval','wp_pet'), 'value' => __('Dose Interval','wp_pet'), ),
					array( 'name' => __('Unknown','wp_pet'), 'value' => __('Unknown','wp_pet'), ),
				),
			),
			array(
			    'name' => __('Neutered','wp_pet'),
			    'desc' => 'field description (optional)',
			    'id' => $prefix . 'pet_desex',
			    'type' => 'checkbox'
			),
			array(
				'name'    => __('Special needs','wp_pet'),
				'id'      => $prefix . 'pet_needs',
				'type'    => 'select',
				'options' => array(
	        array( 'name' => '', 'value' => '', ),
						array( 'name' => __('Special needs','wp_pet'), 'value' => __('Special needs','wp_pet'), ),
						array( 'name' => __('No special needs','wp_pet'), 'value' => __('No special needs','wp_pet'), ),
				 ),
			),
		),
	);

	$meta_boxes[] = array(
		'id'         => 'contact_pet',
		'title'      => 'Contact Form',
		'pages'      => array( 'pet' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => '',
				'desc'    => __('Display a <a href="https://jetpack.me/support/contact-form/" target="_blank">JetPack</a> contact form for the provided email below','wp_pet'),
				'id'      => $prefix . 'pet_email_option',
				'type'    => 'checkbox',
				'options' => array(
					'yes' => __( 'Check One', 'wp_pet' ),
					//array( 'name' => __('Yes','wp_pet'), 'value' => 'yes', ),
					//array( 'name' => __('No','wp_pet'), 'value' => 'no', ),
				),
			),
			array(
				'name' => __('E-mail address','wp_pet'),
				'desc' => __('The e-mail address to send','wp_pet'),
				'id'   => $prefix . 'pet_another_email',
				'type' => 'text_medium',
			),					
		)
	);


	$meta_boxes[] = array(
		'id'         => 'lost_pet',
		'title'      => 'Lost & Found',
		'pages'      => array( 'pet' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => __('Lost & Found Information','wp_pet'),
				'desc' => __('Add an address here to display a map if your lost or found a wondering pet.','wp_pet'),
				'id'   => $prefix . 'pet_title_3',
				'type' => 'title',
			),
			array(
				'name' => __('Address','wp_pet'),
				'desc' => __('Address or place reference','wp_pet'),
				'id'   => $prefix . 'pet_address',
				'type' => 'text_medium',
			),					
		)
	);

	return $meta_boxes;
}

add_action( 'init', 'pet_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function pet_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}