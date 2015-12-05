<?php  
////////////////FOR BOOTSTRAP MENU////////////////////////////

require_once('wp_bootstrap_navwalker.php');


////////////////ENQUEUE SCRIPTS////////////////////////////

function load_my_scripts() {  
		wp_enqueue_style( 'bootstrap_css', get_template_directory_uri().'/inc/bootstrap.min.css');
		wp_enqueue_style( 'basestylesheet', get_stylesheet_uri() );
		wp_enqueue_style( 'fontawesome_css', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
		wp_enqueue_style('googleFonts', 'http://fonts.googleapis.com/css?family=Roboto:400,700,400italic');
        wp_deregister_script('jquery');  
        wp_enqueue_script('jquery', 'http://code.jquery.com/jquery.js');  
        wp_enqueue_script('jquery');  
        wp_enqueue_script('bootstrap', 'http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
    }  
add_action('wp_enqueue_scripts', 'load_my_scripts');  


////////////////MENU SUPPORT////////////////////////////

add_theme_support('menus');
add_action( 'init', 'register_my_menus' );
function register_my_menus() {
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'themedomain' ),
		)
	);
}


////////////////THUMBNAIL SUPPORT////////////////////////////

add_theme_support('post-thumbnails');


////////////////FEED LINKS AND TITLE TAG SUPPORT////////////////////////////

add_theme_support( 'automatic-feed-links' );
add_theme_support("title-tag");


/////////////////////////REGISTER SIDEBARS/////////////////////////

function theme_sidebar() {
register_sidebar(array(
  'name' => __( 'Right Sidebar', 'themename' ),
  'id' => 'rightsidebar',
  'description' => __( 'Widgets in this area will be shown on the right sidebar', 'themename' ),
));
}
add_action( 'widgets_init', 'theme_sidebar' );


////////////////SHORTEN EXCERPT////////////////////////////

function myexcerpt($count){
$excerpt = get_the_content();
$excerpt = strip_shortcodes($excerpt);
$excerpt = strip_tags($excerpt);
$the_str = substr($excerpt, 0, $count);
return $the_str;
}


/////////////////////////CUSTOM POST TYPE REGISTER/////////////////////////

function create_posttype() {
	
		register_post_type( 'slider',
		// CPT Options
			array(
				'labels' => array(
					'name' => __( 'Slider' ),
					'singular_name' => __( 'Slide' ),
	
				),
				'public' => true,
				'has_archive' => true,
				'rewrite' => array('slug' => 'slider'),
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			)
		);
	
	}
add_action( 'init', 'create_posttype' );


/////////////////////////FORMAT SEARCH WIDGET FOR BOOTSTRAP/////////////////////////

add_filter( 'get_search_form' , 'searchwidget' , 2 ) ;
function searchwidget( $markup ) {
    $markup = str_replace( 'class="search-form"' , 'class="search-form row"' , $markup ) ;
    $markup = str_replace( '<label' , '<i class="fa fa-search""></i> &nbsp;<label' , $markup ) ;
    $markup = str_replace( '<input type="text"' , '<input type="text" class="form-control" placeholder="type and hit enter"' , $markup ) ;
    $markup = preg_replace( '/(<span class="screen-reader-text">.*?>)/' , '' , $markup ) ;
    $markup = preg_replace( '/(<input type="submit".*?>)/' , '<br />' , $markup ) ;
    return $markup;
}

/////////////////////////CUSTOM CONTACT FORM/////////////////////////
class Custom_contact_form {
    private $form_errors = array();
    function __construct() {
        // Register a new shortcode: [dm_contact_form]
        add_shortcode('cc_form', array($this, 'shortcode'));
    }
    static public function form() {
        echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
        echo '<p>';
        echo 'Your Name (required) <br/>';
        echo '<input type="text" name="your-name" value="' . $_POST["your-name"] . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Email (required) <br/>';
        echo '<input type="text" name="your-email" value="' . $_POST["your-email"] . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Subject (required) <br/>';
        echo '<input type="text" name="your-subject" value="' . $_POST["your-subject"] . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Message (required) <br/>';
        echo '<textarea rows="10" cols="35" name="your-message">' . $_POST["your-message"] . '</textarea>';
        echo '</p>';
        echo '<p><input type="submit" name="form-submitted" value="Send"></p>';
		echo '</form>';
    }
    public function validate_form( $name, $email, $subject, $message ) {
    	
        // If any field is left empty, add the error message to the error array
        if ( empty($name) || empty($email) || empty($subject) || empty($message) ) {
            array_push( $this->form_errors, 'No field should be left empty' );
        }
		
        // if the name field isn't alphabetic, add the error message
        if ( strlen($name) < 4 ) {
            array_push( $this->form_errors, 'Name should be at least 4 characters' );
        }
        // Check if the email is valid
        if ( !is_email($email) ) {
            array_push( $this->form_errors, 'Email is not valid' );
        }
    }
    public function send_email($name, $email, $subject, $message) {
        	
        // Ensure the error array ($form_errors) contain no error
        if ( count($this->form_errors) < 1 ) {
            // sanitize form values
            $name = sanitize_text_field($name);
            $email = sanitize_email($email);
            $subject = sanitize_text_field($subject);
            $message = esc_textarea($message);
            
			// get the blog administrator's email address
            $to = get_option('admin_email');
			
            $headers = "From: $name <$email>" . "\r\n";
            // If email has been process for sending, display a success message
            if ( wp_mail($to, $subject, $message, $headers) )
                echo '<div style="background: #3b5998; color:#fff; padding:2px;margin:2px">';
                echo 'Thanks for contacting me, expect a response soon.';
                echo '</div>';
        }
    }
    public function process_functions() {
        if ( isset($_POST['form-submitted']) ) {
			
			// call validate_form() to validate the form values
            $this->validate_form($_POST['your-name'], $_POST['your-email'], $_POST['your-subject'], $_POST['your-message']);
            
            // display form error if it exist
            if (is_array($this->form_errors)) {
                foreach ($this->form_errors as $error) {
                    echo '<div>';
                    echo '<strong>ERROR</strong>:';
                    echo $error . '<br/>';
                    echo '</div>';
                }
            }
        }
        $this->send_email( $_POST['your-name'], $_POST['your-email'], $_POST['your-subject'], $_POST['your-message'] );
        self::form();
    }
    public function shortcode() {
        ob_start();
        $this->process_functions();
        return ob_get_clean();
    }
}
new Custom_contact_form;
?>

