<?php
/**
 * Plugin Name: LH QR Codes
 * Plugin URI: https://lhero.org/portfolio/lh-qr-codes/
 * Description: Creates an api that gives every user a qr code that points to a url (which can then be redirected)
 * Version: 1.06
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com/
*/

if (!class_exists('LH_QR_codes_plugin')) {

class LH_QR_codes_plugin {
    
    private static $instance;
    
    
public static function lh_qr_code_return_qr_url($text){

$src = add_query_arg( 'text', $text, site_url('/lh_qr_codes/') );

return $src;


}


	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars 
	*/
	public function add_query_vars($vars){
		$vars[] = '__lh-qr-codes';
		return $vars;
	}

/** Add API Endpoint
	*	This is where the magic happens - brush up on your regex skillz
	*	@return void
	*/

	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__lh-qr-api'] is set, we kill WP and serve up qr awesomeness
	*	@return die if API request
	*/
public function sniff_requests(){
		global $wp;
if(isset($wp->query_vars['__lh-qr-codes'])){

include('phpqrcode/qrlib.php'); 

$text = $_GET['text'] ?: '';
$format = $_GET['format'] ?: 'svg';
$outfile = $_GET['outfile'] ?: false;
$level = $_GET['level'] ?: 'QR_ECLEVEL_L';
$size = $_GET['size'] ?: '3';
$margin = $_GET['margin'] ?: '3';
$saveandprint = $_GET['saveandprint'] ?: false;

$back_color = $_GET['back_color'] ?: 0xFFFFFF; 
if (!is_numeric($back_color)){  $back_color = hexdec($back_color); }

$fore_color = $_GET['fore_color'] ?: 0x000000;
if (!is_numeric($fore_color)){  $fore_color = hexdec($fore_color); }


if (isset($format) and ($format == 'png')){

$Code = QRcode::png($text, $outfile, $level, $size, $margin, $saveandprint, $back_color, $fore_color);


} else {


$Code = QRcode::svg($text, $outfile, $level, $size, $margin, $saveandprint, $back_color, $fore_color);

}
    


echo $Code; 
exit;


}
	}

public function add_endpoint(){

add_rewrite_rule('lh_qr_codes/?','index.php?__lh-qr-codes=1','top');


}



public function register_shortcodes(){

add_shortcode('lh_qr_code', array($this,"lh_qr_code_short_output"));

}






public function lh_qr_code_short_output($attributes,$content = null)  {

if (isset($attributes['text'])){

$text = $attributes['text'];

} elseif (isset($attributes['short'])){
    
$text = wp_get_shortlink();
    
    
} else {

$text = get_permalink();

}


if (isset($attributes['urlencode']) and ($attributes['urlencode'] == 1)){

$text = urlencode($text);

}

$src = self::lh_qr_code_return_qr_url($text);

if (isset($attributes['format'])){
if (($attributes['format'] == 'png') or ($attributes['format'] == 'svg')){

$format = $attributes['format'];

$src = add_query_arg( 'format', $format, $src );

} 
}

if (isset($attributes['fore_color'])){

$fore_color = $attributes['fore_color'];

$src = add_query_arg( 'fore_color', $fore_color, $src );

} 

if (isset($attributes['back_color'])){

$back_color = $attributes['back_color'];

$src = add_query_arg( 'back_color', $back_color, $src );

}

if (isset($attributes['margin'])){

$margin = $attributes['margin'];

$src = add_query_arg( 'margin', $margin, $src );

} 

if (!isset($attributes['size'])){

$size = "150";


} else {

$size = $attributes['size'];

}


ob_start();

$src = apply_filters( 'lh_qr_codes_shortcode_src', $src);


echo '<img src="'.$src.'" height="'.$size.'" width="'.$size.'" />';

$return_string = ob_get_contents();
ob_end_clean();


return $return_string;


}

public function on_activate($network_wide) {

    if ( is_multisite() && $network_wide ) { 

        global $wpdb;

        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);
wp_clear_scheduled_hook( 'lh_qr_codes_flush' ); 
wp_schedule_single_event(time(), 'lh_qr_codes_flush');
            restore_current_blog();
        } 

    } else {

flush_rewrite_rules();


}




}

public function flush_rules(){

flush_rewrite_rules();
wp_clear_scheduled_hook( 'lh_qr_codes_flush' ); 

}

    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }


public function __construct() {

add_filter('query_vars', array($this, 'add_query_vars'), 0);
add_action('parse_request', array($this, 'sniff_requests'), 0);
add_action('init', array($this, 'add_endpoint'), 0);

add_action( 'init', array($this,"register_shortcodes"));


add_action('lh_qr_codes_flush', array($this,"flush_rules"));

}


}

$lh_qr_codes_plugin_instance = LH_QR_codes_plugin::get_instance();
register_activation_hook(__FILE__, array($lh_qr_codes_plugin_instance, 'on_activate') , 10, 1);

}

function the_post_qrcode( $size = '150', $attr = '' ) {
    
$src = LH_QR_codes_plugin::lh_qr_code_return_qr_url(get_permalink());

if ($fore_color){

$src = add_query_arg( 'fore_color', $fore_color, $src );

}

if ($back_color){

$src = add_query_arg( 'back_color', $back_color, $src );

}

if ($margin){

$src = add_query_arg( 'margin', $margin, $src );

}


$src = apply_filters( 'lh_qr_codes_template_src', $src);


echo '<img src="'.$src.'" height="'.$size.'" width="'.$size.'" />';


}

add_action( 'widgets_init', 'lh_qr_codes_widget_init' );
 
function lh_qr_codes_widget_init() {
    register_widget( 'lh_qr_codes_widget' );
}
 
class lh_qr_codes_widget extends WP_Widget {
 
    public function __construct()    {
        $widget_details = array(
            'classname' => 'lh_qr_codes_widget',
            'description' => 'add text that you wish to be displayed as a QR Code'
        );
 
        parent::__construct( 'lh_qr_codes_widget', 'LH QR Codes Widget', $widget_details );
 
    }
 
    public function form( $instance ) {
        // Backend Form


   $text = '';
    if( !empty( $instance['text'] ) ) {
        $text = $instance['text'];
    }
 

    ?>
 


    <p>
        <label for="<?php echo $this->get_field_name( 'text' ); ?>"><?php _e( 'Text:' ); ?></label>
        <textarea class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="text" ><?php echo esc_attr( $text ); ?></textarea>
    </p>
 
 




 
    <div class='mfc-text'>
         
    </div>
 
    <?php
 
    echo $args['after_widget'];


    }
 
    public function update( $new_instance, $old_instance ) {  
        return $new_instance;
    }
 
    public function widget( $args, $instance ) {
        // Frontend display HTML

$title = apply_filters( 'widget_title', $instance['title'] );

	// before and after widget arguments are defined by themes

	echo $args['before_widget'];

	if ( ! empty( $title ) ){

	echo $args['before_title'] . $title . $args['after_title'];

}

$text = $instance['text'];

	// This is where you run the code and display the output

	echo do_shortcode('[lh_qr_code text='.$text.']');


	echo $args['after_widget'];




}
 
}



?>