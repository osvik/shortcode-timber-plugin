<?php
/**
 * @package shortcode_timber_plugin
 * @version 0.1
 */
/*
Plugin Name: Shortcode Timber plugin
Plugin URI: https://add-url-here.org
Description: Boilerplate shortcode that renders view with Tinder and Twig
Author: Osvaldo Gago
Text Domain: shortcode-timber-plugin
Domain Path: /languages
Version: 0.1
Author URI: https://osvaldo.pt
*/

defined( 'ABSPATH' ) or die( 'You can\'t do that !' );

/**
 * Initiate plugin's translations
 */
function shortcode_timber_load_plugin_textdomain() {
    load_plugin_textdomain( 'shortcode-timber-plugin', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'shortcode_timber_load_plugin_textdomain' );


/**
 * Timber plugin detection
 */
function timber_plugin_detection() {
	if ( !class_exists('Timber') ) {
		add_action( 'admin_notices', 'timber_plugin_notices' );
	}
}

/**
 * Timber plugin notice
 */
function timber_plugin_notices() {
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<div class="error message">
			<p>Timber not found. You need to enable it!</p>
		</div>
		<?php
	}
}

add_action( 'init', 'timber_plugin_detection' );

/**
 * Generates the shortcode
 */
function shortcode_timber_plugin($atts = [], $content = null, $tag = '') {

    // Assets loading
    
    wp_enqueue_script('shortcode_timber_js', plugin_dir_url(__FILE__) . 'js/shortcode-timber-plugin.js', array(), '0.1');
    
    wp_enqueue_style('shortcode_timber', plugin_dir_url(__FILE__) . 'css/shortcode-timber-plugin.css', array(), '0.1' );
    
    $example_image = plugin_dir_url(__FILE__) . 'images/calendar.svg';
    
    // Shortcode attributes and content loading
    
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    $attributes = shortcode_atts([
        'title' => 'Please add a title',
        'description' => 'Please add a description'
    ], $atts, $tag);
    
    $attributes['content'] = $content;
    
    // Render output
    
    if ( class_exists('Timber') ) {
        
        // Output with timber
        
        $output =  Timber::compile( 'views/shortcode-timber.twig', $attributes );

        return $output;
        
    } else {

        // Fallback output without timber
        
        $output = '';
        $output .= '<p>' . __('Hello', 'shortcode-timber-plugin') . ' ' . esc_html($attributes['title']) .'</p>';
        $output .= '<h3>' . __('Content', 'shortcode-timber-plugin') .  '</h3>' ;
        $output .= '<div>' . $attributes['content'] . '</div>';
        $output .= '<div>' . $example_image . '</div>';
        $output .= '<script>';
        $output .= '    
                document.addEventListener("DOMContentLoaded", function(){
                    console.log("This runs if timber is not enabled");
                });
        ';
        $output .= '</script>';
        return $output;
        
    }
    
}

add_shortcode('timber_plugin', 'shortcode_timber_plugin');

?>
