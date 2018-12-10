<?php
/**
 *
 * @package ASD_PageScripts
 * Author:      Michael H Fahey
 * Author URI:  https://artisansitedesigns.com/staff/michael-h-fahey
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '' );
}

/** ----------------------------------------------------------------------------
 *   adds meta boxes and callback functions for working with post meta
 *   Hooks into the admin_init action
 *  --------------------------------------------------------------------------*/
function asd_add_page_customfields() {
	if ( is_admin() ) {
		add_meta_box( 'html_fields', 'Additional Markup', 'asd_page_html_fields', 'page', 'normal', 'high' );
	}
}
add_action( 'admin_init', 'asd_add_page_customfields' );


/** ----------------------------------------------------------------------------
 *   callback defined in function asd_add_page_customfields
 *   adds post meta data fields to post
 *  --------------------------------------------------------------------------*/
function asd_page_html_fields() {
	global $post;
	$custom = get_post_custom( $post->ID );

	/*  wrapperclasses ---------------------------------------------------- */
	$wrapperclasses = '';
	if ( isset( $custom['wrapperclasses'] ) ) {
		$wrapperclasses = $custom['wrapperclasses'][0];
	}
	?>

	<div class="row">
	<label>Wrapper Classes</label></br>
	<textarea rows="4" cols="70" name="wrapperclasses"><?php echo esc_attr( $wrapperclasses ); ?></textarea>
	</div>

	<?php
	/*  page_script  ---------------------------------------------------- */
	if ( current_user_can( 'manage_options' ) ) {
		$page_script = '';
		if ( isset( $custom['page_script'] ) ) {
			$page_script = $custom['page_script'][0];
		}
		?>

	   <div style="padding-top:20px;" class="row">
		<label>Page Script</label></br>
	   <div class="row">
		 <small><i>JavaScript code placed here will be automatically inserted into page source. Do not include script tags.</i></small>
	   </div>
		<textarea rows="12" cols="70" name="page_script"><?php echo esc_attr( $page_script ); ?></textarea>
		</div>

		<?php
	}
}




/** ----------------------------------------------------------------------------
 *   function asd_page_save_meta()
 *   use the php filter_input to get and sanitize post
 *  --------------------------------------------------------------------------*/
function asd_page_save_meta() {
	global $post;
	if ( isset( $post->ID ) ) {
		$wrapperclasses = filter_input( INPUT_POST, 'wrapperclasses', FILTER_SANITIZE_STRING );
		update_post_meta( $post->ID, 'wrapperclasses', sanitize_textarea_field( $wrapperclasses ) );

		if ( current_user_can( 'manage_options' ) ) {
			 $page_script = filter_input( INPUT_POST, 'page_script' );
			 update_post_meta( $post->ID, 'page_script', $page_script );
		}
	}
}
add_action( 'save_post', 'asd_page_save_meta' );


/** ----------------------------------------------------------------------------
 *   function asd_print_page_script()
 *  --------------------------------------------------------------------------*/
function asd_print_page_script() {
   global $post;

	// leave other post types alone.
	if ( get_post_type( $post ) === 'page' ) {

      $page_script = get_post_meta( $post->ID, 'page_script', true );

      if ( $page_script )        {
         echo "\r\n" . '<script type="text/javascript">' . "\r\n";
         echo $page_script . "\r\n";
         echo '</script>' . "\r\n";
      } 

   }
}
add_filter( 'wp_print_footer_scripts', 'asd_print_page_script' );


/** ----------------------------------------------------------------------------
 *   function  asd_page_wrapper_html( $post_content )
 *   if we are using our asd_page post type, then prepend and append
 *   output with <div> tags and DOM classes
 *   Hooks into the the_content filter
 *  ----------------------------------------------------------------------------
 *
 *  @param Array $post_content - post data passed into this filter hook.
 */
function asd_page_wrapper_html( $post_content ) {
	global $post;

	$wrapped_post_content = '';

	// leave other post types alone.
	if ( get_post_type( $post ) === 'page' ) {

		$closing_divs   = '';
		$wrapperclasses = explode( "\r\n", get_post_meta( $post->ID, 'wrapperclasses', 'false' ) );

		foreach ( $wrapperclasses as $wrapperclass ) {
			$wrapped_post_content .= '<div class="' .
									sanitize_html_classes( $wrapperclass ) .
									'">' . "\r\n";
			$closing_divs         .= '</div>' . "\r\n";
		}

		$wrapped_post_content .= balanceTags( $post_content, true );
		$wrapped_post_content .= $closing_divs;

		return $wrapped_post_content;
	} else {
		return $post_content;
	}

}
add_filter( 'the_content', 'asd_page_wrapper_html' );




if ( ! function_exists( 'sanitize_html_classes' ) && function_exists( 'sanitize_html_class' ) ) {
	/** ----------------------------------------------------------------------------
	 *   function sanitize_html_classes
	 *   allows a series of DOM classes to be sanitized
	 *   (sanitize_html_class will remove the whitespace between classes)
	 *  ----------------------------------------------------------------------------
	 *
	 *  @param string $class - string of whitespace delimited html classes.
	 *  @param string $fallback - something to return if the result is nothing.
	 */
	function sanitize_html_classes( $class, $fallback = null ) {
		// Explode it, if it's a string.
		if ( is_string( $class ) ) {
			$class = explode( ' ', $class );
		}
		if ( is_array( $class ) && count( $class ) > 0 ) {
			$class = array_map( 'sanitize_html_class', $class );
			return implode( ' ', $class );
		} else {
			return sanitize_html_class( $class, $fallback );
		}
	}
}

