<?php
/*
Carya - Tekserve Studio Child Theme
*/


/***
	Enqueue Parent Styles
***/

add_action( 'wp_enqueue_scripts', 'tekserve_studio_enqueue_parent_styles' );
function tekserve_studio_enqueue_parent_styles() {
   
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . "/css/bootstrap.css" );
    wp_enqueue_style( 'style', get_template_directory_uri() . "/style.css" );
    wp_enqueue_style( 'animate', get_template_directory_uri() . "/css/animate.min.css" );
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . "/css/font-awesome.css" );
    wp_enqueue_style( 'flexslider', get_template_directory_uri() . "/css/flexslider.css" );
    wp_enqueue_style( 'style2', get_template_directory_uri() . "/css/style.css" );
    wp_enqueue_style( 'responsive', get_template_directory_uri() . "/css/responsive.css" );

}	//end tekserve_studio_enqueue_styles()



/***
	Register Typography.com stylesheet and enqueue
***/

add_action( 'wp_enqueue_scripts', 'tekserve_studio_enqueue_typography' );
function tekserve_studio_enqueue_typography() {

	wp_register_style( 'typography_gotham', '//cloud.typography.com/6883574/655028/css/fonts.css', array( 'style' ), '1.0a' );
	wp_enqueue_style( 'typography_gotham' );

}	//end tekserve_studio_enqueue_typography()



/***
	Register local stylesheet / js and enqueue
***/

add_action( 'wp_enqueue_scripts', 'tekserve_studio_enqueue_local_styles' );
function tekserve_studio_enqueue_local_styles() {

	wp_register_style( 'tekserve_studio', get_stylesheet_directory_uri() . "/style.css", array( 'style', 'bootstrap', 'responsive' ), '1.0a' );
	wp_enqueue_style( 'tekserve_studio' );
	if( is_page() ) {
	
		wp_enqueue_script( 'tekserve_studio_h2', get_stylesheet_directory_uri() . "/js/lineh2.js", array( 'jquery' ) );
	
	}	//end if( is_single() )

}	//end tekserve_studio_enqueue_local_styles()



/***
	Add 'tekserve-studio' body class for CSS/JS overrides
***/

add_filter( 'body_class', 'tekserve_studio_body_class' );
function tekserve_studio_body_class( $classes ) {
	// add 'class-name' to the $classes array
	$classes[] = 'tekserve-studio';
	// return the $classes array
	return $classes;
}	//end tekserve_studio_body_class( $classes )



/***
	Override how some Carya styles are applied to inline stylesheet on wp_head
***/

add_action( 'wp_head', 'tekserve_studio_override_carya_inline_styling' );
function tekserve_studio_override_carya_inline_styling() {

	$option_values = array(
	
		'menu_active'	=> '',
		'footer_bg'		=> '',
		'footer_color'	=> '',
	
	);
	
	if( function_exists( 'carya_option' ) ) {
	
		foreach( $option_values as $key => $value ) {
		
			$option_values[$key] = carya_option( $key );
		
		}	//end foreach( $option_values as $key => $value )
	
	}
	else {
	
		$options = get_option( 'vpt_option' );
		if( $options !== FALSE ) {
			foreach( $option_values as $key => $value ) {
		
				$temp = NULL;
				if( array_key_exists( $key, $options ) ) {
			
					$temp = $options[$key];
					// $temp = $temp->get_options();
					$option_values[$key] = $temp;
			
				}	//end if( array_key_exists( 'menu_active', $options ) )
		
			}	//end foreach( $option_values as $key => $value )
		
		}	//end if( $options !== FALSE )
	
	}	//end if( function_exists( 'carya_option' ) )
	
	?>
	<style type="text/css" id="tekserve-studio-carya-overrides">
	<?php if( !empty( $option_values['menu_active'] ) ): ?>
        .tekserve-studio .menu a:hover, .tekserve-studio .menu-expand:hover { color: <?php echo esc_attr( $option_values['menu_active'] ); ?>; background: transparent; }
        .tekserve-studio .menu .current-menu-item > a { color: <?php echo esc_attr( $option_values['menu_active'] ); ?>; background: transparent; }
    <?php endif; ?>
    <?php if( !empty( $option_values['footer_bg'] ) ): ?>
        .tekserve-studio .footer { background: transparent; }
        <?php 
        $footer_bg_styles = 'background-color: #';
        $footer_bg_colors = cAc_css2rgba( $option_values['footer_bg'], '0.95' );
        $footer_bg_styles .= $footer_bg_colors['hex'] . ';
        background-color: ';
        $footer_bg_styles .= $footer_bg_colors['rgba'] . ';';
        ?>
        .tekserve-studio .footer .fixed-footer { <?php echo $footer_bg_styles; ?> }
    <?php endif ?>
    <?php if( !empty( $option_values['footer_color'] ) ): ?>
        .tekserve-studio .footer { color: #444; }
        .tekserve-studio .footer .fixed-footer { color: <?php echo esc_attr( $option_values['footer_color'] ); ?>; }
    <?php endif ?>
	</style>
	<?php

}	//end tekserve_studio_override_carya_inline_styling()



/***
	Unregister last two columns in footer
***/

add_action( 'widgets_init', 'remove_carya_footer_cols', 11 );
function remove_carya_footer_cols() {

	unregister_sidebar( 'footer2' );
	unregister_sidebar( 'footer3' );

}	//end remove_carya_footer_cols()



/***
	Widget Class for Sliding Footer
***/

class Tekserve_Studio_Sliding_Footer_Widget extends WP_Widget {



	public function __construct() {
	
		// widget actual processes
		parent::__construct(
			'tekserve_studio_sliding_footer', // Base ID
			__('Sliding Footer Form'), // Name
			array( 'description' => 'Given a target (fixed) element selector and a button element, shows active content in target when button is clicked and expands to fill screen. Optional close button and open button html.' ) // Args
		);
		
		// include js
		add_action( 'wp_enqueue_scripts', array( &$this, 'js' ) );
		
	}	//end __construct()
	
	

	public function widget( $args, $instance ) {
	
		extract( $args );
		
		// these are the widget options
		if( $instance['alttext'] ) {
		
			$alttext = trim( $instance['alttext'] );
			
		}
		else {
		
			$alttext = '';
	
		}	//end if( $instance['alttext'] ) *****Implement in form*****
		if( $instance['target'] ) {
		
			$target = trim( $instance['target'] );
			
		}
		else {
		
			$target = '.fixed-footer';
	
		}	//end if( $instance['target'] )
		if( $instance['button'] ) {
		
			$button = trim( $instance['button'] );
			
		}
		else {
		
			$button = 'Learn More';
	
		}	//end if( $instance['button'] )
		if( $instance['top_content'] ) {
		
			$top_content = trim( base64_decode( $instance['top_content'] ) );
			
		}
		else {
		
			$top_content = '<span class="button-expand"><i class="fa fa-envelope-o"></i> Sign Up</span>';
	
		}	//end if( $instance['top_content'] )
		if( $instance['bottom_content'] ) {
		
			$bottom_content = trim( base64_decode( $instance['bottom_content'] ) );
			
		}
		else {
		
			$bottom_content = '<h3>Enter Your Email Address</h3>';
			
	
		}	//end if( $instance['bottom_content'] )
		
		echo $before_widget;
		
		// Display the widget
		
		//pass target array to js
		echo '<script type="text/javascript">
			tekserveStudioSlidingFooter = { "target":"' . $target . '","alttext":"' . $alttext . '" };
		</script>';
		 
		//link to display bottom content
		echo '
		<div class="tekserve-studio-sliding-footer-top">
			' . $top_content . '
		</div>';
		
		//the bottom content iteself
		echo '
		<div class="tekserve-studio-sliding-footer-bottom" style="display:none;">
			<div class="tekserve-studio-sliding-footer-bottom-content">
				<div class="tekserve-studio-sliding-footer-bottom-content-form">' . $bottom_content;
				if( function_exists( 'mailchimpSF_signup_form' ) ) {
				
					mailchimpSF_signup_form();
				
				}	//end if( function_exists( 'mailchimpSF_signup_form' ) )
				
				echo '
				</div>
				
			</div>
		</div>';
		echo '
		<div class="tekserve-studio-sliding-footer-buttons">
			<span class="button-expand sliding-footer-trigger">' .
			$button
			. '</span>
			<div class="tekserve-studio-sliding-footer-close">
				<i class="fa fa-times-circle"></i>
			</div>
		</div>';
		
		echo $after_widget;
		
	}	//end widget( $args, $instance )
	
	

 	public function form( $instance ) {
 	
		// outputs the options form on admin
		
		// check values
		if( $instance) {
		
			 $target = trim( $instance['target'] );
			 $button = trim( $instance['button'] );
			 $alttext = trim( $instance['alttext'] );
			 $bottom_content = trim( base64_decode( $instance['bottom_content'] ) );
			 $top_content = trim( base64_decode( $instance['top_content'] ) );
		
		}
		else {
		
			 $target = '.fixed-footer';
			 $button = 'Sign Up';
			 $alttext = '';
			 $top_content = 'Subscribe to our Mailing List';
			 $bottom_content = '<h3>Enter Your Email Address</h3>';
		
		}	//end if( $instance)
		
		//output form
		echo '
		<p>
			<label for="' . $this->get_field_id( 'target' ) . '">Enter the CSS selector for the element that will contain the content and slider</label>
			<input style="width: 100%" id="' . $this->get_field_id( 'target' ) . '" name="' . $this->get_field_name( 'target' ) . '" value="' . $target . '" />
		</p>';
		echo '
		<p>
			<label for="' . $this->get_field_id( 'button' ) . '">Enter text to be shown in button that expands and shirinks the slider</label>
			<input style="width: 100%" id="' . $this->get_field_id( 'button' ) . '" name="' . $this->get_field_name( 'button' ) . '" value="' . $button . '" />
		</p>';
		echo '
		<p>
			<label for="' . $this->get_field_id( 'alttext' ) . '">Optional - Enter text to be shown in button when slider is open</label>
			<input style="width: 100%" id="' . $this->get_field_id( 'alttext' ) . '" name="' . $this->get_field_name( 'alttext' ) . '" value="' . $alttext . '" />
		</p>';
		echo '
		<p>
			<label for="' . $this->get_field_id( 'top_content' ) . '">Enter html to be shown in the sliding area when it is closed</label>
			<textarea  style="width: 100%; min-height: 10em;" id="' . $this->get_field_id( 'top_content' ) . '" name="' . $this->get_field_name( 'top_content' ) . '" >' . $top_content . '</textarea>
		</p>';
		echo '
		<p>
			<label for="' . $this->get_field_id( 'bottom_content' ) . '">Enter html to be shown in the sliding area when it is expanded</label>
			<textarea  style="width: 100%; min-height: 10em;" id="' . $this->get_field_id( 'bottom_content' ) . '" name="' . $this->get_field_name( 'bottom_content' ) . '" >' . $bottom_content . '</textarea>
		</p>';
		
	}	//end form( $instance )




	public function update( $new_instance, $old_instance ) {

		// processes widget options to be saved
		$instance = $old_instance;
    	// Fields
    	$instance['target'] = trim( $new_instance['target'] );
    	$instance['button'] = trim( $new_instance['button'] );
    	$instance['alttext'] = trim( $new_instance['alttext'] );
    	$instance['top_content'] = base64_encode( trim( $new_instance['top_content'] ) );
    	$instance['bottom_content'] = base64_encode( trim( $new_instance['bottom_content'] ) );
    	return $instance;

	}	//end update( $new_instance, $old_instance )
	
	
	
	public function js() {
	
		// enqueues scripts if present
		if( is_active_widget( false, false, $this->id_base, true ) ) {
	   
		   wp_enqueue_script( 'tekservestudio_slidingfooter', get_stylesheet_directory_uri() . '/js/sliding-footer.js', array( 'jquery' ) );
		   wp_enqueue_style( 'tekservestudio_slidingfooter', get_stylesheet_directory_uri() . '/css/sliding-footer.css' );
	   
		}  //end if( is_active_widget( false, false, $this->id_base, true ) ) 
    
    } //end js()
    
    
	
} //end class Tekserve_Studio_Sliding_Footer_Widget



/***
	Widget Class for Simple Footer
***/

class Tekserve_Studio_Simple_Footer_Widget extends WP_Widget {



	public function __construct() {
	
		// widget actual processes
		parent::__construct(
			'tekserve_studio_simple_footer', // Base ID
			__('Simple Footer Form'), // Name
			array( 'description' => 'Shows a email sign up form, with preceding text, at the bottom of the page. Close button keeps hidden for user session; submission of form hides permanently.' ) // Args
		);
		
		// include js
		add_action( 'wp_enqueue_scripts', array( &$this, 'js' ) );
		
	}	//end __construct()
	
	

	public function widget( $args, $instance ) {
	
		extract( $args );
		
		$mailchimp = false;
		
		// these are the widget options
		if( $instance['button'] ) {
		
			$button = trim( $instance['button'] );
			
		}
		else {
		
			$button = 'Submit';
	
		}	//end if( $instance['button'] )
		if( $instance['text'] ) {
		
			$text = trim( base64_decode( $instance['text'] ) );
			
		}
		else {
		
			$text = '<span>We equip startups for growth. Join our mailing list for updates.</span>';
	
		}	//end if( $instance['text'] )
		
		echo $before_widget;
		
		// Display the widget
		 
		//left text
		echo '
		<div class="tekserve-studio-simple-footer-left">
			' . $text . '
		</div>';
		
		//the form content iteself
		echo '
		<div class="tekserve-studio-simple-footer-right">';
				if( function_exists( 'mailchimpSF_signup_form' ) ) {
				
					mailchimpSF_signup_form();
					$mailchimp = true;
				
				}
				else {
				
					echo '<form class="tekserve-studio-simple-footer-form">
						<input type="email" name="tekserve-studio-simple-footer-email" id="tekserve-studio-simple-footer-email" placeholder="email address" />
						<button class "btn btn-outline" type="submit">' . $button . '</button>
					</form>';
				
				}	//end if( function_exists( 'mailchimpSF_signup_form' ) )
				
		echo '
		</div>';
		
		//the close button
		echo '
		<div class="tekserve-studio-simple-footer-close">
			<i class="fa fa-times"></i>
		</div>';
		
		echo $after_widget;
		
	}	//end widget( $args, $instance )
	
	

 	public function form( $instance ) {
 	
		// outputs the options form on admin
		
		// check values
		if( $instance) {
		
			 $button = trim( $instance['button'] );
			 $text = trim( base64_decode( $instance['text'] ) );
		
		}
		else {
		
			 $button = 'Submit';
			 $text = '<span>We equip startups for growth. Join our mailing list for updates.</span>';
		
		}	//end if( $instance)
		
		//output form
		echo '
		<p>
			<label for="' . $this->get_field_id( 'button' ) . '">Enter text to be shown in submit button</label>
			<input style="width: 100%" id="' . $this->get_field_id( 'button' ) . '" name="' . $this->get_field_name( 'button' ) . '" value="' . $button . '" />
		</p>';
		echo '
		<p>
			<label for="' . $this->get_field_id( 'text' ) . '">Enter text to be shown to the left of the form</label>
			<input style="width: 100%" id="' . $this->get_field_id( 'text' ) . '" name="' . $this->get_field_name( 'text' ) . '" value="' . $text . '" />
		</p>';
		
	}	//end form( $instance )




	public function update( $new_instance, $old_instance ) {

		// processes widget options to be saved
		$instance = $old_instance;
    	// Fields
    	$instance['button'] = trim( $new_instance['button'] );
    	$instance['text'] = base64_encode( trim( $new_instance['text'] ) );
    	return $instance;

	}	//end update( $new_instance, $old_instance )
	
	
	
	public function js() {
	
		// enqueues scripts if present
		if( is_active_widget( false, false, $this->id_base, true ) ) {
	   
		   wp_enqueue_script( 'tekservestudio_simplefooter', get_stylesheet_directory_uri() . '/js/simple-footer.js', array( 'jquery' ) );
		   wp_enqueue_style( 'tekservestudio_simplefooter', get_stylesheet_directory_uri() . '/css/simple-footer.css' );
	   
		}  //end if( is_active_widget( false, false, $this->id_base, true ) ) 
    
    } //end js()
    
    
	
} //end class Tekserve_Studio_Sliding_Footer_Widget


/** register widgets with wp **/

add_action( 'widgets_init', function(){

     register_widget( 'Tekserve_Studio_Sliding_Footer_Widget' );
     register_widget( 'Tekserve_Studio_Simple_Footer_Widget' );
     
}); //end add_action( 'widgets_init', function()



/***
	Shortcode to Display Tribe Events
***/

//specific excerpt length
function tekserve_studio_excerpt_length( $length ) {

	return 30;

}


//check that tribe plugin is installed before creating shortcode
add_action( 'after_setup_theme', 'create_tribe_upcoming_events_shortcode' );
function create_tribe_upcoming_events_shortcode() {

	if( function_exists( 'tribe_event_featured_image' ) ) {
	
		add_shortcode( 'upcomingevents', 'tribe_upcoming_events' );
	
	}	//end if( function_exists( 'tribe_event_featured_image' ) )

}	//end create_tribe_upcoming_events_shortcode()

//code for the shortcode itself
function tribe_upcoming_events( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'number'	=> 3,
			'usecols'	=> true,
			'cols'		=> 3,
		), $atts )
	);

	// Code
	add_filter( 'excerpt_length', 'tekserve_studio_excerpt_length', 999 );
	$html = '<div id="tribe-events-content" class="tekserve-studio-tribe-events tribe-events-list tribe-events-photo">
		<div class="tribe-events-loop hfeed vcalendar tribe-clearfix" id="tribe-events-photo-events">';
	$args = array(

		'post_type'			=> 'tribe_events',
		'post_status'		=> 'publish',
		'posts_per_page'	=> $number,

	);
	$the_query = new WP_Query( $args );

	// The Loop
	if( $the_query->have_posts() ) {

	

		if( $usecols ) {

			if( intval( $cols ) > 12 ) {
		
				$cols = intval( $cols/12 );
		
			}	//end if( intval( $cols ) > 12 )
			$colsize= intval( 12/$cols );
			$coltag = '[column size="' . $colsize . '" col_class="md"]';
			$i = 0;
			while( $the_query->have_posts() ) {
		
				$post = $the_query->the_post();

				if( $i == 0 ) {
			
					$html .= '[row]';
			
				}	//end if( $i == 0 )

				$html .= $coltag;
			
				$html .= '<div class="tribe-events-photo-event-wrap">';
				$html .= tribe_event_featured_image( null, 'medium' );
				$html .= '<div class="tribe-events-event-details tribe-clearfix">';
				$html .= print_r( do_action( 'tribe_events_before_the_event_title' ), true );
				$html .= '<h3 class="tribe-events-list-event-title entry-title summary">';
				$html .= '<a class="url" href="' . esc_url( tribe_get_event_link() ) . '" title="' . the_title( '', '', false ) . '" rel="bookmark">';
				$html .= the_title( '', '', false );
				$html .= '</a></h3>';
				$html .= print_r( do_action( 'tribe_events_after_the_event_title' ), true);
				$html .= print_r( do_action( 'tribe_events_before_the_meta' ), true );
				$html .= '<div class="tribe-events-event-meta">
								<div class="updated published time-details">';
				if( ! empty( $post->distance ) ) {
			
				$html .= '<strong>[' . tribe_get_distance_with_unit( $post->distance ) . ']</strong>';
			
				}	//end if( ! empty( $post->distance ) )
				$html .=  tribe_events_event_schedule_details();
				$html .= '</div>
					</div>';
				$html .=  print_r( do_action( 'tribe_events_after_the_meta' ), true );
				$html .= print_r( do_action( 'tribe_events_before_the_content' ), true );
				$html .= '<div class="tribe-events-list-photo-description tribe-events-content entry-summary description">';
				$html .= tribe_events_get_the_excerpt();
				$html .= '</div>';
				$html .= print_r( do_action( 'tribe_events_after_the_content' ), true );
				$html .= '	</div>
				</div>';
			
				$html .= '[/column]';
				if( $i == intval( $cols-1 ) ) {
			
					$html .= '[/row]';
			
				}	//end if( $i == intval( $cols-1 ) )
				if( $i < $cols ) {
			
					$i++;

				}
				else {
			
					$i = 0;
			
				}	//end if( $i < $cols )
		
			}	//end while( $the_query->have_posts() )
	
		}
		else {
	
			while( $the_query->have_posts() ) {
		
				$post = $the_query->the_post();
		
				$html .= '<div class="tribe-events-photo-event-wrap">';
				$html .= tribe_event_featured_image( null, 'medium' );
				$html .= '<div class="tribe-events-event-details tribe-clearfix">';
				$html .= print_r( do_action( 'tribe_events_before_the_event_title' ), true );
				$html .= '<h3 class="tribe-events-list-event-title entry-title summary">';
				$html .= '<a class="url" href="' . esc_url( tribe_get_event_link() ) . '" title="' . the_title( '', '', false ) . '" rel="bookmark">';
				$html .= the_title( '', '', false );
				$html .= '</a></h3>';
				$html .= print_r( do_action( 'tribe_events_after_the_event_title' ), true);
				$html .= print_r( do_action( 'tribe_events_before_the_meta' ), true );
				$html .= '<div class="tribe-events-event-meta">
								<div class="updated published time-details">';
				if( ! empty( $post->distance ) ) {
			
				$html .= '<strong>[' . tribe_get_distance_with_unit( $post->distance ) . ']</strong>';
			
				}	//end if( ! empty( $post->distance ) )
				$html .=  tribe_events_event_schedule_details();
				$html .= '</div>
					</div>';
				$html .=  print_r( do_action( 'tribe_events_after_the_meta' ), true );
				$html .= print_r( do_action( 'tribe_events_before_the_content' ), true );
				$html .= '<div class="tribe-events-list-photo-description tribe-events-content entry-summary description">';
				$html .= tribe_events_get_the_excerpt();
				$html .= '</div>';
				$html .= print_r( do_action( 'tribe_events_after_the_content' ), true );
				$html .= '	</div>
				</div>';
		
			}	//end while( $the_query->have_posts() )

		}	//end if( $usecols )

	}
	else {

		return '<h3 class="error">There are currently no upcoming events.</h3>';

	}	//end if( $the_query->have_posts() )

	$html .= '	</div>
	</div>';
	return do_shortcode( $html );

}	//endtribe_upcoming_events( $atts )



/***
	Utility function to convert css color values
***/

if( !( function_exists( 'cAc_css2rgba' ) ) ) {

	//input a css color value as a string and optionally opacity as a string. Will convert hex to rgb, convert hex and rgb to rgba if opacity is given, and return either an array containing hex, rgb, and rgba values:
	//E.G. array( 'hex' => '000000', 'rgb' => 'rgb(0,0,0)', 'rgba' => 'rgba(0,0,0,1.0)' )
	//or, if passed an invalid value, return a string $default
	function cAc_css2rgba($color, $opacity = false) {
 
		$default = 'transparent';
 
		//Return default if no color provided
		if( empty( $color ) ) {
	
			  return $default; 
 
		 }	//end if( empty( $color ) )
	 
		//Sanitize $color if "#" is provided 
		if( $color[0] == '#' ) {
	
			$color = substr( $color, 1 );
	
		}	//end if( $color[0] == '#' )

		//Check if color has 6 or 3 characters and get values
		if( strlen( $color ) === 6 ) {
	
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	
		}
		elseif( strlen( $color ) === 3 ) {
	
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	
		} //end if( strlen( $color ) === 6 ) / elseif( strlen( $color ) === 3 )
	
		//if we have a valid hex array, create rgb array and output value for hex (always returs 6char hex, with '#')
		if( !empty( $hex ) ) {
	
			//Convert hexadec to rgb
			$rgb =  array_map( 'hexdec', $hex );
			$output_hex = '#' . implode($hex);
	
		}
		//if passed a rgba color value, set rgb array using color values, and pass its opacity to $opacity unless was given as argument (given opacity argument overrides passed alpha value in rgba)
		//generate hex output using rgb values in $rgb
		elseif( substr( $color, 0, 5 ) == 'rgba(') {
	
			$rgb = explode( ",", str_replace( ')', '', substr( $color, 5 ) ), 4 );
			if( $opacity == false ) {
		
				$opacity = array_pop( $rgb );
		
			}
			elseif( count( $rgb ) > 3 ) {
		
				array_pop( $rgb );
		
			}
			$hex =  array_map( function( $item ) { settype( $item, "int" ); return dechex( $item ); }, $rgb );
			$output_hex = '#' . implode($hex);
	
		}
		//if passed an rgb color value, set rgb array using color values
		//generate hex output using rgb values in $rgb
		elseif( substr( $color, 0, 4 ) == 'rgb(' ) {
	
			$rgb = explode( ",", str_replace( ')', '', substr( $color, 4 ) ), 3 );
			$hex =  array_map( function( $item ) { settype( $item, "int" ); return dechex( $item ); }, $rgb );
	
		}
		//we're not dealing with string color values; returns default value as a string ('transparent')
		else {
	
			return $default;
	
		}	//end ( !empty( $hex ) ) / elseif( substr( $color, 0, 5 ) == 'rgba(') / elseif( substr( $color, 0, 4 ) == 'rgb(' )

	

		//Check if opacity is set(rgba or rgb); default to solid color (1.0) in rgba if $opacity not set or invalid
		//generate output for rgb and rgba values using $rgb array and opacity
		if( $opacity ) {
	
			if( abs( $opacity ) > 1 ) {
		
				$opacity = 1.0;
		
			}	//end if( abs( $opacity ) > 1 )
			$output_rgb = 'rgb(' . implode( ",", $rgb ) . ')';
			$output_rgba = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		}
		else {
	
			$output_rgb = 'rgb(' . implode( ",", $rgb ) . ')';
			$output_rgba = 'rgba(' . implode( ",", $rgb ) . ',1.0)';
	
		}	//end if( $opacity )
		
		$output = array(
	
			'hex'	=> $output_hex,
			'rgb'	=> $output_rgb,
			'rgba'	=> $output_rgba,
	
		);

		//Return array with three strings
		return $output;

	}	//end cAc_css2rgba( $color, $opacity = false )å

}	//end if( !( function_exists( 'cAc_css2rgba' ) ) )