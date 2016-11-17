<?php

/* Add pet in Right Now */

function pet_right_now_content() {
    $args = array(
        'public' => true,
        '_builtin' => false
    );
    $output = 'object';
    $operator = 'and';
    $post_types = get_post_types($args, $output, $operator);

    foreach ($post_types as $post_type) {
        $num_posts = wp_count_posts($post_type->name);
        $num = number_format_i18n($num_posts->publish);

        $text = _n($post_type->labels->singular_name, $post_type->labels->name, intval($num_posts->publish));
        if (current_user_can('edit_posts')) {
            $text = "<a href='edit.php?post_type=$post_type->name' title='".__('View all your pet posts', 'wp_pet')."'>$text</a>";
        }

        echo '<li>'.$num.' '.$text.'</li>';
       // echo '<tr><td class="first b b-' . $post_type->name . '">' . $num . '</td>';
       //  echo '<td class="t ' . $post_type->name . '">' . $text . '</td></tr>';
    }
}

add_action('dashboard_glance_items', 'pet_right_now_content');




/* Display Pet picture */

function ST4_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'pet_mini');
        return $post_thumbnail_img[0];
    }
}

function pet_picture_columns($defaults) {
    $defaults['featured_image'] = __('Picture', 'wp_pet');
    return $defaults;
}

function pet_img_content_only($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        if ($column_name == 'featured_image') {
            $post_featured_image = ST4_get_featured_image($post_ID);
            if ((!empty($post_featured_image))) {
                echo '<div style="padding:3px;overflow:hidden;border:1px solid #ccc;width:50px;height:50px;display:block"><img style="width:100%;height:auto;" src="' . $post_featured_image . '" alt="" title="' . get_the_title() . '" /></div>';
            } else {
                echo '';
            }
        }
    }
}

add_filter('manage_pet_posts_columns', 'pet_picture_columns', 10);
add_action('manage_pet_posts_custom_column', 'pet_img_content_only', 10, 2);

//Functions for auto place content
//Check metadatas
function test_if_meta($arr, $key, $before = '', $after = '') {
    if ($text = $arr[$key][0])
        return $before . $text . $after;
}

//Add post pet to author page
function custom_post_author_archive($query) {
    if ($query->is_author)
        $query->set('post_type', array('pet', 'post'));
    remove_action('pre_get_posts', 'custom_post_author_archive');
}

add_action('pre_get_posts', 'custom_post_author_archive');

//Place content

function place_special_pet_content($content) {
    global $post;

    $postid = get_the_ID();
    $status = wp_get_post_terms($postid, 'pet-status', array("fields" => "all"));
    $category = wp_get_post_terms($postid, 'pet-category', array("fields" => "all"));

    //all postmeta is here:
    $petinfo = get_post_custom(get_the_ID());
    //print_r($petinfo); //uncomment to see all post meta in everypost

    $neut = get_post_meta( get_the_ID(), '_data_pet_desex', true );
    if ( !empty( $neut ) ) {
        $netinfo = '<span class="pet_vac">'. __('Neutered','wp_pet').'</span>, ';
    }

    //$thumbnail='';
    $special='';

        $special .= 
            '<table class="pet_manager_info pet_'.get_the_id().' category-'.$category[0]->slug.' status-'.$status[0]->slug.'">' .
            '<tr>'.
                '<td rowspan="6" class="pet_thumb">'.
                '<a href="' . get_permalink($post->ID) . '"><span class="pet_image">' . get_the_post_thumbnail($postid, 'pet_img') . 
                '<span class="pet_status tag-'.$status[0]->slug.'"><span class="ic ic-'.$status[0]->slug.'"></span>'.$status[0]->name.'</span></a>'.    
                '</td>'.
                '<th class="pet_category">'.__('Category', 'wp_pet').'</th>'.
                '<td class="pet_category">'.get_the_term_list($post->ID, 'pet-category').'</td>'.
                '<th class="pet_colors">'.__('Colors', 'wp_pet').'</th>'.
                '<td class="pet_colors">'.get_the_term_list($post->ID, 'pet-color', '', ', ', '').'</td>'.
            '</tr>'.
            '<tr>'.
                '<th class="pet_sex">'.__('Sex', 'wp_pet').'</th>'.
                '<td class="pet_sex">'.get_the_term_list($post->ID, 'pet-gender').'</td>'.
                '<th class="pet_pattern">'.__('Pattern', 'wp_pet').'</th>'.
                '<td class="pet_pattern">'.get_the_term_list($post->ID, 'pet-pattern', '', ', ', '').'</td>'.
            '</tr>'.  
            '<tr>'.
                '<th class="pet_age">'.__('Age', 'wp_pet').'</th>'.
                '<td class="pet_age">'.get_the_term_list($post->ID, 'pet-age').'</td>'.
                '<th class="pet_coat">'.__('Coat', 'wp_pet').'</th>'.
                '<td class="pet_coat">'.get_the_term_list($post->ID, 'pet-coat', '', ', ', '').'</td>'.                
            '</tr>'.
            '<tr>'.
                '<th class="pet_breed">'.__('Breed', 'wp_pet').'</th>'.
                '<td class="pet_breed">'.get_the_term_list($post->ID, 'pet-breed', '', ', ', '').'</td>'.
                test_if_meta($petinfo, '_data_pet_id', '<th class="pet_id">' . __('ID', 'wp_pet') . '</th><td class="pet_id">', '</td>') .
            '</tr>'.
            '<tr>'.
                '<th class="pet_size">'.__('Size', 'wp_pet').'</th>'.
                '<td class="pet_size">'.get_the_term_list($post->ID, 'pet-size').'</td>'.
                '<td class="pet_neut" colspan="2"></td>'.   
            '</tr>'.
            '<tr>'.
                '<td class="pet_extra" colspan="4">'.
                     $netinfo.
                     test_if_meta($petinfo, '_data_pet_vaccines', '<span class="pet_vac">', '</span>, ') .
                     test_if_meta($petinfo, '_data_pet_needs', '<span class="pet_special">', '</span>') .
                '</td>'.
            '</tr>'.
            '</table>';            

    if (!empty($petinfo['_data_pet_pet_address'][0])) {
        $extrapet .= '<h3>' . __('Address', 'wp_pet') . '</h3><p>' . $petinfo['_data_pet_pet_address'][0] . '</p>';
    };

    if (!empty($petinfo['_data_pet_date'][0])) {
        $extrapet .= '<p>' . date_i18n(get_option('date_format'), $petinfo['_data_pet_date'][0]);

        if (!empty($petinfo['_data_pet_time'][0])) {
            $extrapet .= ' ' . __('at', 'wp_pet') . ' ' . date(get_option('time_format'), strtotime($petinfo['_data_pet_time'][0]));
        };

        $extrapet .= '</p>';
    };

    if (!empty($petinfo['_data_pet_address'][0])) {
        $extrapet .= '<h3>'.__('Have you seen me around this place?', 'wp_pet').'</h3><div class="map_image"><img src="http://maps.google.com/maps/api/staticmap?size=650x300&zoom=16&markers=icon:http://chart.apis.google.com/chart?chst=d_map_pin_icon%26chld=glyphish_dogpaw%257CFF6357%7C' . $petinfo['_data_pet_address'][0] . '&sensor=false" /></div>';
    };



    if ($petinfo['_data_pet_email_option'][0] == 'yes') {

        if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'contact-form' ) ) {
        $extrapet .= '<h3>' . __('Contact about this pet', 'wp_pet') . '</h3>' . do_shortcode('[contact-form subject="' . get_bloginfo('title') . ' - ' . get_the_title() . '"][contact-field label="' . __('Your Name', 'wp_pet') . '" type="name" required="1"/][contact-field label="' . __('Your E-mail', 'wp_pet') . '" type="email" required="1"/][contact-field label="' . __('Your Message', 'wp_pet') . '" type="textarea" required="1"/][/contact-form]');

        }
        else{
          $extrapet .= '<h3>'.__('Please, you have to install Jetpack and activate the <a href="https://jetpack.me/support/contact-form/" target="_blank">Contact Form</a> feature in order to display the form', 'wp_pet').'</h3>';

        }

    }


    if ('pet' == get_post_type() && is_single() && is_main_query()) {
        return $special . $content . $extrapet;
    }

    if ('pet' == get_post_type() && is_archive() && is_main_query()) {
        return $special;
    }

    return $content;
}

add_filter('the_content', 'place_special_pet_content', 10);
add_filter('the_excerpt', 'place_special_pet_content', 11);

//Special content for Pet Page

function place_special_pet_content_in_pets($content) {

    $page = __('pet-list', 'wp_pet');

    if (is_page($page)) {

        $data .= '<h3>' . __('Browse Pets by Status', 'wp_pet') . '</h3><ul>' . wp_list_categories('echo=0&show_count=1&taxonomy=pet-status&title_li=') . '</ul>' .
                '<h3>' . __('Browse Pets by Category', 'wp_pet') . '</h3><ul>' . wp_list_categories('echo=0&show_count=1&taxonomy=pet-category&title_li=') . '</ul>' .
                '<h3>' . __('Search Pets', 'wp_pet') . '</h3>' . do_shortcode('[pet_search]');
    }

    return $content . $data;
}

add_filter('the_content', 'place_special_pet_content_in_pets', 20);




//print performance
function pet_know_performance($visible = false) {

    $stat = sprintf('%d queries in %.3f seconds, using %.2fMB memory', get_num_queries(), timer_stop(0, 3), memory_get_peak_usage() / 1024 / 1024
    );

    echo $visible ? $stat : "<!-- {$stat} -->";
}

add_action('wp_footer', 'pet_know_performance', 20);

/* Change pet post title field */

function pet_change_default_title($title) {
    $screen = get_current_screen();

    if ('pet' == $screen->post_type) {
        $title = __('Enter pet name here', 'wp_pet');
    }

    return $title;
}

add_filter('enter_title_here', 'pet_change_default_title',22);


/* Place pending mod note */

function pet_place_note($content) {

    if (is_preview() && 'pet' == get_post_type())
        $note = '<div class="note">' . __('This post is still waiting for moderator approval.', 'wp_pet') . '<a href="' . get_edit_post_link(get_the_ID()) . '">' . __('Edit this post and add more info', 'wp_pet') . '</a></div>';

    return $content . $note;
}

add_filter('the_content', 'pet_place_note', 20);


/* Shortcode Pet list */

function pet_list_shortcode($content) {


        $data .= '<h3>' . __('Browse Pets by Status', 'wp_pet') . '</h3><ul>' . wp_list_categories('echo=0&show_count=1&taxonomy=pet-status&title_li=') . '</ul>' .
                '<h3>' . __('Browse Pets by Category', 'wp_pet') . '</h3><ul>' . wp_list_categories('echo=0&show_count=1&taxonomy=pet-category&title_li=') . '</ul>' .
                '<h3>' . __('Search Pets', 'wp_pet') . '</h3>' . do_shortcode('[pet_search]');


        return $content . $data;
}    

/* Shortcode Search form */

function pet_search_form() {

    $types = get_terms('pet-category', array('hide_empty' => 1));
    foreach ($types as $type) {
        $pet_types .= "<option value='$type->slug'" . selected($type->slug, true, false) . ">$type->name" . "&nbsp;(" . "$type->count" . ")" . "</option>";
    }

    $statuses = get_terms('pet-status', array('hide_empty' => 1));
    foreach ($statuses as $status) {
        $pet_status .= "<option value='$status->slug'" . selected($status->slug, true, false) . ">$status->name" . "&nbsp;(" . "$status->count" . ")" . "</option>";
    }

    $genders = get_terms('pet-gender', array('hide_empty' => 1));
    foreach ($genders as $gender) {
        $pet_genders .= "<option value='$gender->slug'" . selected($gender->slug, true, false) . ">$gender->name" . "&nbsp;(" . "$gender->count" . ")" . "</option>";
    }

    $sizes = get_terms('pet-size', array('hide_empty' => 1));
    foreach ($sizes as $size) {
        $pet_sizes .= "<option value='$size->slug'" . selected($size->slug, true, false) . ">$size->name" . "&nbsp;(" . "$size->count" . ")" . "</option>";
    }

    $ages = get_terms('pet-age', array('hide_empty' => 1));
    foreach ($ages as $age) {
        $pet_ages .= "<option value='$age->slug'" . selected($size->slug, true, false) . ">$age->name" . "&nbsp;(" . "$age->count" . ")" . "</option>";
    }

    $searchform .= '<form action="' . get_home_url() . '/" method="get" id="searchpetform"><fieldset><ol>' .
            '<li id="item-status"><label for="pet_status">' . __('Status', 'wp_pet') . '</label><select id="pet_status" name="pet-status">' .
            '<option value="0"></option>' .
            $pet_status .
            '</select></li>' .
            '<li id="item-category"><label for="pet_category">' . __('Category', 'wp_pet') . '</label><select id="pet_category" name="pet-category">' .
            '<option value="0"></option>' .
            $pet_types .
            '</select></li>' .
            '<li id="item-gender"><label for="pet_gender">' . __('Sex', 'wp_pet') . '</label><select id="pet_gender" name="pet-gender">' .
            '<option value="0"></option>' .
            $pet_genders .
            '</select></li>' .
            '<li id="item-size"><label for="pet_size">' . __('Size', 'wp_pet') . '</label><select id="pet_size" name="pet-size">' .
            '<option value="0"></option>' .
            $pet_sizes .
            '</select></li>' .
            '<li id="item-age"><label for="pet_age">' . __('Age', 'wp_pet') . '</label><select id="pet_size" name="pet-size">' .
            '<option value="0"></option>' .
            $pet_ages .
            '</select></li>' .
            '<input type="hidden" name="post_type" value="pet" />' .
            '<br /><input type="submit" id="searchpet" name="search" value="' . __('Search pet', 'wp_pet') . '">' .
            '' .
            '' .
            '</ol></fieldset></form>';

    return $searchform;
}

/* Only edit your own posts */

function pet_parse_query_useronly($wp_query) {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/edit.php') !== false) {
        if (!current_user_can('level_10')) {
            global $current_user;
            $wp_query->set('author', $current_user->id);
        }
    }
}

add_filter('parse_query', 'pet_parse_query_useronly');

/* Add pet thumb in feeds */

function insertThumbnailRSS($content) {
    global $post;
    if (has_post_thumbnail($post->ID)) {


        if ($post->post_type == 'pet') {
            $content = '' . get_the_post_thumbnail($post->ID, 'thumbnail') . '' . $content;
        }
    }
    return $content;
}

add_filter('the_excerpt_rss', 'insertThumbnailRSS');
add_filter('the_content_feed', 'insertThumbnailRSS');
?>