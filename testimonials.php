<?php
/**
* Plugin Name: Custom MB Testimonails
* Plugin URI: https://www.yourwebsiteurl.com/
* Description: Allows you to create custom testimonials.
* Version: 1.0
* Author: aldocaava
* Author URI: http://yourwebsiteurl.com/
**/


use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options_testimonials' );
function crb_attach_theme_options_testimonials() {
    Container::make( 'post_meta', 'Custom Data' )
    ->where( 'post_type', '=', 'testimonials' )
    ->add_fields( array(
        Field::make( 'image', 'crb_picture' ),
        Field::make( 'textarea', 'crb_quotation'),
        Field::make( 'select', 'crb_rating', )
        ->add_options( array(
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
        ))
    ));
}

add_action( 'after_setup_theme_testimonials', 'crb_load_testimonials' );
function crb_load_testimonials() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}

function custom_posttypes_testimonials() {

    $ui_fields = array(
        'title'
        );
            
        $ui_labels = array(
        'name' => _x('Testimonials', 'plural'),
        'singular_name' => _x('Testimonials', 'singular'),
        'menu_name' => _x('Testimonials', 'admin menu'),
        'name_admin_bar' => _x('Testimonial', 'admin bar'),
        'all_items' => __('All Testimonials'),
        'add_new' => _x('Add New', 'add new'),
        'add_new_item' => __('Add New Testimonial'),
        'new_item' => __('New Testimonial'),
        'edit_item' => __('Edit Testimonial'),
        'view_item' => __('View Testimonial'),
        'view_items' => __('View Testimonials'),
        'search_items' => __('Search Testimonials'),
        'not_found' => __('No Testimonials found.')
        );
            
        $args = array(
        'supports' => $ui_fields,
        'labels' => $ui_labels,
        'public' => true,
        'menu_position'  => 20,
        'query_var' => true,
        'rewrite' => array('slug' => 'testimonials'),
        'has_archive' => true,
        'hierarchical' => false,
        'show_in_rest' => true
        );
        register_post_type('testimonials', $args);


}

function display_testimonials() {
    $args = array( 'post_type' => 'testimonials' );
    $testimonials = new WP_Query( $args );
   $result = "";

    while ($testimonials->have_posts()) {
    	$testimonials->the_post();
      
        $result .= "<div class='testimonial-card'>";
            $result .= "<div class='testimonial-card-left'>";
            $result .= wp_get_attachment_image(carbon_get_the_post_meta( 'crb_picture' ), "full");
            $result .= "</div>";
            $result .= "<div class='testimonial-card-right'>";
            $result .= "<p class='testimonial-title'>" . get_the_title() . "</p>";
            $result .= "<div class='testimonial-quote'>" .  carbon_get_the_post_meta( 'crb_quotation' ) . "</div>";
            #get the rating number
            $result .= "<div class='testimonial-rating'>";
            $rating = carbon_get_the_post_meta( 'crb_rating' );
            for($i = 0; $i < $rating; $i++){
              $result .= "<span class='icon-wrapper'>";
              $result .= "<i class='x-icon custom-icon'  style='' data-x-icon-s=''></i>";
              $result .= "</span>";  
            }
            
            $result .= "</div>";
            
            $result .= "</div>";
        $result .= "</div>";
    
    }

    return $result;

}

function np_register_testimonial_styles() {
    wp_register_style('custom_testimonial_styles', plugins_url('styles.css', __FILE__));   
    wp_enqueue_style('custom_testimonial_styles');
}

add_action('wp_enqueue_scripts', 'np_register_testimonial_styles');
add_action( 'init', 'custom_posttypes_testimonials' );
add_shortcode('np-testimonials', 'display_testimonials');