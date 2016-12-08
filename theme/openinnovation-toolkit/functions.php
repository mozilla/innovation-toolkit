<?php
/**
 * Twenty Sixteen functions and definitions
 */

/**
 * Constant variables
 */
define( 'THEME_PATH', get_template_directory_uri() );
define( 'THEME_DIR', TEMPLATEPATH );
define( 'STYLESHEET_DIR', get_stylesheet_directory() );
define( 'SITE_URL', get_option('siteurl') );
define( 'SITE_NAME', esc_attr( get_bloginfo( 'name', 'display' ) ) );

// Website Specific
define( 'METHODS_PAGE_URL', SITE_URL.'/methods/' );
define( 'SUBMIT_PAGE_URL', SITE_URL.'/contribute/' );


function disable_json_api () {
  // Filters for WP-API version 1.x
  add_filter('json_enabled', '__return_false');
  add_filter('json_jsonp_enabled', '__return_false');

  // Filters for WP-API version 2.x
  add_filter('rest_enabled', '__return_false');
  add_filter('rest_jsonp_enabled', '__return_false');
}
add_action( 'after_setup_theme', 'disable_json_api' );

/*
 * GLOBAL VARIABLES
 */
global $OIT;
function oit_global_vars() {
  global $OIT;
  
  $args = array(
      'hierarchical' =>  0,
      'taxonomy' => 'process',
      'hide_empty' => false,
      'pad_counts' => false 
  );
  $processes = get_categories( $args );
  
//  var_dump($processes);
  $OIT['processes'] = $processes;
}
add_action( 'after_setup_theme', 'oit_global_vars' );



if ( ! function_exists( 'twentysixteen_setup' ) ) :
function twentysixteen_setup() {
	load_theme_textdomain( 'twentysixteen', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 9999 );
  if ( function_exists( 'add_image_size' ) ) {
    add_image_size( 'method_thumbnail', 336, 156, true);
  }

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'twentysixteen' ),
		'footer'  => __( 'Footer Menu', 'twentysixteen' ),
    'footer-secondary'  => __( 'Footer Secondary Menu', 'twentysixteen' ),
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
//	add_editor_style( array( 'css/editor-style.css', twentysixteen_fonts_url() ) );
}
endif; // twentysixteen_setup
add_action( 'after_setup_theme', 'twentysixteen_setup' );


function unregister_default_widgets() {
  unregister_widget('WP_Widget_Pages');
  unregister_widget('WP_Widget_Calendar');
  unregister_widget('WP_Widget_Links');
  unregister_widget('WP_Widget_Meta');
  unregister_widget('WP_Widget_Search');
  unregister_widget('WP_Widget_Text');
  unregister_widget('WP_Widget_RSS');
  unregister_widget('WP_Nav_Menu_Widget');
}
add_action('widgets_init', 'unregister_default_widgets', 11);



function oit_clean_content($content) {
  $domains = array(
      0 => get_bloginfo('url'),
      1 => 'https://toolkit.production.paas.mozilla.community',
      2 => 'https://toolkit.staging.paas.mozilla.community'
  );
  foreach($domains as $domain) {
    $content = str_replace(' src="'.$domain, ' src="', $content );
    $content = str_replace(' href="'.$domain, ' href="', $content );
    $content = str_replace(" src='".$domain, " src='", $content );
    $content = str_replace(" href='".$domain, " href='", $content );
  }
  
  //strip inline span and styles
  $content = strip_only_tags($content, array('span'));
  $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
  
  return $content;
}
add_filter('content_save_pre','oit_clean_content','99');
add_filter( 'the_content', 'oit_clean_content' );


function strip_only_tags($str, $tags, $stripContent=false) {
  $content = '';
  if(!is_array($tags)) {
    $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
    if(end($tags) == '') array_pop($tags);
  }
  foreach($tags as $tag) {
    if ($stripContent)
      $content = '(.+</'.$tag.'(>|\s[^>]*>)|)';
    $str = preg_replace('#</?'.$tag.'(>|\s[^>]*>)'.$content.'#is', '', $str);
  }
  return $str;
}



/**
 * Hide admin bar for logged in users except administrator
 */
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
//  if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
//  }
}


/**
 * Customization to login screen
 */
function twentysixteen_login_logo() {
	echo '<style type="text/css">
		h1 a { 
      background-image:url('.THEME_PATH.'/images/logo.png) !important;
      background-size: 240px 72px !important;
      width: 240px !important;
      height: 72px !important;
    }
    
    h1{
      display: block;
      width: 100% !important;
    }
  
    h1 a {
      background: none !important;
      width: auto !important;
      height: auto !important;
      font-family: helvetica, arial, sans-serif !important;
      font-size: 16px !important;
      font-weight: 300 !important;
      text-indent: 0 !important;
      color: #000 !important;
      
    }
  </style>"



	</style>';
}
add_action('login_head', 'twentysixteen_login_logo');

function twentysixteen_home_url() {
	return get_home_url();  // or return any other URL you want
}
add_filter('login_headerurl', 'twentysixteen_home_url');

function twentysixteen_login_title() {
	return get_option('blogname'); // or return any other title you want
}
add_filter('login_headertitle', 'twentysixteen_login_title');


/**
 * Adds favicon to the page
 */
function twentysixteen_favicon() {
  print "\n<!-- Adding FavIcon -->\n";
	print "<link rel='shortcut icon' href='" . THEME_PATH . "/images/favicon.png' />\n";
}
add_action('wp_head', 'twentysixteen_favicon');
add_action('admin_head', 'twentysixteen_favicon');


/**
 * Handles JavaScript detection.
 */
function twentysixteen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
//add_action( 'wp_head', 'twentysixteen_javascript_detection', 0 );

/**
 * Enqueues scripts and styles.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_scripts() {
  global $post;
	// Add custom fonts, used in the main stylesheet.
//	wp_enqueue_style( 'twentysixteen-fonts', twentysixteen_fonts_url(), array(), null );
  
  wp_enqueue_style('font-mozilla-fira', 'https://code.cdn.mozilla.net/fonts/fira.css', array(), false, 'screen,projection,print');
  wp_enqueue_style('font-CooperHewitt', THEME_PATH.'/fonts/fonts.css', array(), false, 'screen,projection,print');
  
	// Theme stylesheet.
	wp_enqueue_style( 'twentysixteen-style', get_stylesheet_uri() );
  wp_enqueue_style( 'twentysixteen-styles', THEME_PATH.'/stylesheets/styles.css' );

  
  // Google Recaptcha.
  wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js?render=explicit', array('jquery'), '', false);
  
  // Loading javascripts and jquery plugins
  wp_enqueue_script('jquery-easing', THEME_PATH . '/js/jquery.easing.1.3.js', array('jquery'), '1.3');
  wp_enqueue_script('jquery-showLoading', THEME_PATH . '/js/jquery.showLoading.min.js', array('jquery'), '1.0', false);
  wp_enqueue_script( 'imagesloaded', THEME_PATH.'/js/imagesloaded.pkgd.min.js', array( 'jquery' ), '3.1.8', false );
  wp_enqueue_script( 'jquery-form', THEME_PATH . '/js/jquery.form.js', array('jquery'), '2.67', false );

  // Loading bootstap javascripts
  wp_enqueue_script('bootstrap-tooltip', THEME_PATH . '/js/tooltip.js', array('jquery'), '3.3.6');
  wp_enqueue_script('bootstrap-popover', THEME_PATH . '/js/popover.js', array('jquery'), '3.3.6');
  wp_enqueue_script('bootstrap-modal', THEME_PATH . '/js/modal.js', array('jquery'), '3.3.6');
  
  
  // Loading bxslider jquery plugin
  wp_enqueue_script( 'jquery-bxslider', THEME_PATH . '/js/jquery.bxslider.min.js', array(), 'v4.1.2', false );
  wp_enqueue_style( 'bxslider', THEME_PATH .'/css/jquery.bxslider.css' );
  
	wp_enqueue_script( 'twentysixteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery', 'recaptcha' ), '20151204', true );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_scripts' );


// Apply filter
add_filter('body_class', 'oit_body_classes');

function oit_body_classes($classes) {
  global $post;
  $process = get_the_terms( $post->ID , 'process' );
  $classes[] = $process[0]->slug;

  $classes[] = "pushmenu-push";
  return $classes;
}


add_action( 'template_redirect', 'oit_redirect_post' );
function oit_redirect_post() {
  $redirect_post_types = array('post', 'question', 'term', 'resource', 'image', 'attachment');
  $queried_post_type = get_query_var('post_type');
  if ( is_single() && in_array($queried_post_type, $redirect_post_types) ) {
    wp_redirect( SITE_URL.'/404/', 301 );
    exit;
  }
}


function get_custom_post_title() {
  global $post;
  $custom_post_title = get_field('custom_post_title', $post->ID);
  return (!($custom_post_title)) ? $post->post_title : $custom_post_title;
}



function oit_page_banner($o=null) {
  global $post;
  if(is_404()) {
    $header_type = 'banner';
  } else {
    if(is_null($o)) {
      $o = get_post_meta($post->ID);
    }
    $header_type = $o['header_type'][0];
  }
  
  $return = '';
  switch($header_type) {
    case 'slider':
      $slides = get_field('banner_slides', $post->ID);
//      var_dump($slides);
      if($slides) {
        $return .= '<ul class="page-slider">';
        foreach($slides as $slide_id) {
          $slide_image = get_field('slide_image', $slide_id);
          $slide_text = get_field('slide_text', $slide_id);
          $return .= '<li style="background-image: url('.$slide_image.');"><div class="container"><div class="banner-content"><span>'.$slide_text.'</span></div></div></li>';
        }
        $return .= '</ul>';
      }
      break;
    
    case 'banner':
      if(is_404()) {
        $banner_image = THEME_PATH.'/images/slider1.jpg';
        $banner_text = "Oops! That page can&rsquo;t be found.";
      } else {
        $banner_image = get_field('banner_image', $post->ID);
        $banner_text = $o['banner_text'][0];
      }
      
      $return .= '<div class="page-banner" style="background-image: url('.$banner_image.');">';
        $return .= '<div class="container"><div class="banner-content animated fadeInDown"><span>'.$banner_text.'</span></div></div>';
      $return .= '</div>';
      break;
    
    default:
      break;
  }
  return $return;
}


/* Custom ajax loader */
add_filter('wpcf7_ajax_loader', 'my_wpcf7_ajax_loader');
function my_wpcf7_ajax_loader () {
	return  get_bloginfo('stylesheet_directory') . '/images/loader-light.gif';
}

//add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );


//if(!function_exists('iuttu_wpcf7_form_response_output_filter')){
//	function iuttu_wpcf7_form_response_output_filter($output, $class, $content, $this){
//		return '<div class="' . $class . ' ">' . $content . '</div>';
//	}
//	add_filter( 'wpcf7_form_response_output', 'iuttu_wpcf7_form_response_output_filter', 10, 4);
//}


//// Redirect users if not login...
//function custom_login_redirect() {
//  global $pagenow;
//  if ( !is_user_logged_in() && $pagenow != 'wp-login.php' ){
//    wp_redirect( wp_login_url(site_url()), 302 );
//    exit();
//  }
//}
//add_action( 'wp', 'custom_login_redirect' );


function shortcodeSocialLinks($atts = null, $content = null) {
  extract(shortcode_atts(array(
      'title' => '',
  ), $atts));
  
  $return = '';
  $return .= '<ul class="social-links">';
    $return .= '<li><a href="https://twitter.com/#" target="_blank"><i class="genericon genericon-twitter" title="Twitter"></i></a></li>';
    $return .= '<li><a href="https://www.facebook.com/#" target="_blank"><i class="genericon genericon-facebook-alt" title="Facebook"></i></a></li>';
    $return .= '<li><a href="https://plus.google.com/#" target="_blank"><i class="genericon genericon-googleplus" title="Google+"></i></a></li>';
    $return .= '<li><a href="https://linkedin.com/#" target="_blank"><i class="genericon genericon-linkedin" title="Google+"></i></a></li>';
  $return .= '</ul>';
  $return .= '<div class="clear"></div>';
  return $return;
}
add_shortcode('theme_social_links','shortcodeSocialLinks');



add_shortcode('oit_subpages', 'shortcodeGunaintlSubpages');
function shortcodeGunaintlSubpages() {
  global $post;
  $return = '';
  $args = array(
      'child_of' => $post->ID,
      'parent' => $post->ID,
      'post_type' => 'page',
      'post_status' => 'publish',
      'sort_column' => 'menu_order'
  );
  $results = get_pages($args);
  if($results) {
    $return .= '<div class="accordion subpages-accordion">';
    foreach($results as $result) {
      $return .= '<div class="item">';
        $return .= '<h3 class="item-title">'.$result->post_title.'</h3>';
        $return .= '<div class="item-content">'.apply_filters('the_content', $result->post_content).'</div>';
      $return .= '</div>';
    }
    $return .= '</div>';
  }
  return $return;
}

add_shortcode('highlighted', 'shortcodeGunaintlHighlighted');
function shortcodeGunaintlHighlighted($atts = null, $content = null) {
  extract(shortcode_atts(array(
      'font' => 'sans-serif',
      'size' => 'medium',
  ), $atts));
  
  return '<div class="highlighted '.$size.' '.$font.'">'.$content.'</div>';
}



add_shortcode('oit_testimonials','shortcodeGunaintlTestimonials');
function shortcodeGunaintlTestimonials() {
  $return = '';
  $args=array(
      'post_type' => 'testimonial',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'caller_get_posts'=> 1
  );
  $results = query_posts($args);
  
  if($results) {
    $return .= '<div id="testimonials">';
    $return .= '<span class="quote-start">&ldquo;</span>';
    $return .= '<ul>';
    foreach($results as $result) {
      $return .= '<li><div class="quote">'.apply_filters('the_content', $result->post_content).'</div><h4 class="quote-by">'.$result->post_title.'</h4></li>';
    }
    $return .= '</ul>';
    $return .= '</div>';
  }
  wp_reset_query();
  return $return;
}


add_shortcode('oit_services', 'shortcodeGunaintlServices');
function shortcodeGunaintlServices() {
  $return = '';
  $args = array(
      'child_of' => SERVICES_PAGE_ID,
      'parent' => SERVICES_PAGE_ID,
      'post_type' => 'page',
      'post_status' => 'publish',
      'sort_column' => 'menu_order'
  );
  $results = get_pages($args);
  if($results) {
    $return .= '<div class="row">';
    foreach($results as $result) {
      $short_description = get_field('short_description', $result->ID);
      $icon_image = get_field('icon_image', $result->ID);
      $return .= '<div class="col-md-6 col-sm-12">';
        $return .= '<div class="item">';
          $return .= '<img src="'.$icon_image.'" alt="'.$result->post_title.'" />';
          $return .= '<h3 class="item-title">'.$result->post_title.'</h3>';
          $return .= '<div class="item-content">'.apply_filters('the_content', $short_description).'</div>';
        $return .= '</div>';
      $return .= '</div>';
    }
    $return .= '</div>';
  }
  return $return;
}


add_shortcode('oit_clients','shortcodeGunaintlClients');
function shortcodeGunaintlClients($atts = null, $content = null) {
  extract(shortcode_atts(array(
      'slider' => false,
  ), $atts));
  
  $return = '';
  $args=array(
      'post_type' => 'client',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'caller_get_posts'=> 1
  );
  $results = query_posts($args);
  
  if($results) {
    $return .= '<div id="clients">';
    $return .= ($slider) ? '<ul class="clients-slider">' : '<ul>';
    foreach($results as $result) {
      $client_logo = get_field('client_logo', $result->ID);
      $return .= '<li><img src="'.$client_logo.'" alt="'.$result->post_title.'" /></li>';
    }
    $return .= '</ul>';
    $return .= '</div>';
  }
  wp_reset_query();
  return $return;
}



function oit_methods($process=null, $num=6) {
  $return = '';
  
  if(is_null($process)) {
    $args = array(
        'hierarchical' =>  0,
        'taxonomy' => 'process',
        'hide_empty' => false,
        'pad_counts' => false 
    );
    $processes = get_categories( $args );
    if($processes) {
      $args=array(
          'post_type' => 'method',
          'post_status' => 'publish',
          'posts_per_page' => 1
      );
    }
    foreach($processes as $process) {
      $args['tax_query'] = array(
          array(
              'taxonomy' => 'process',
              'field' => 'id',
              'terms' => $process->term_id
          )
      );
      
      $results = query_posts($args);
      foreach($results as $result) {
        $return .= '<div class="col-sm-4 col-xs-12">';
          $return .= oit_display_method_card($result);
        $return .= '</div>';
      }
      wp_reset_query();
    }
  } else {
    
    
    $args=array(
        'post_type' => 'method',
        'post_status' => 'publish',
        'posts_per_page' => $num,
        'tax_query' => array(
            array(
                'taxonomy' => 'process',
                'field' => 'slug',
                'terms' => $process
            )
        )
    );
    
    $results = query_posts($args);
    foreach($results as $result) {
      $return .= '<div class="col-sm-4 col-xs-12">';
      $return .= oit_display_method_card($result);
      $return .= '</div>';
    }
    $return .= '<div class="clear"></div>';
    wp_reset_query();
  }
  return $return;
}


function oit_all_methods($args=null) {
  $page_size = 99;
  $args=array(
      'post_type' => 'method',
      'post_status' => 'publish',
      'posts_per_page' => $page_size,
  );
  
  $process_slug = get_query_var('action');
  if(!empty($process_slug)) {
    if($process_slug === 'search') {
      $search_text = filter_input(INPUT_POST, 'search_text');
      $search_text = sanitize_text_field($search_text);
      $search_text = preg_replace('/[^a-zA-Z0-9-_ \.]/','', $search_text);
      $args['s'] = $search_text;
    } else {
      $args['tax_query'] = array(
        array(
            'taxonomy' => 'process',
            'field' => 'slug',
            'terms' => $process_slug
        )
      );
    }
  }
  $return = "";


  $the_query = new WP_Query( $args );
  global $post;
  // The Loop

  $return .= '<div class="methods-wrapper">';
  if ( $the_query->have_posts() ) {
    while ( $the_query->have_posts() ) {
      $the_query->the_post();
      $return .= '<div class="method-card-wrapper">';
        $return .= oit_display_method_card($post, FALSE);
      $return .= '</div>';
    }
    wp_reset_postdata();
  } else {
    $return .= '<p class="no-results">';
    if($process_slug === 'search') {
      $return .= 'Woops, look like we don&rsquo;t have anything listed for that search term. Try again -- we suggest checking for typos or using a different search term.';
    } else {
      $return .= 'Woops, look like we don&rsquo;t have anything listed for that filters slected.';
    }
    $return .= '</div>';
  }
  $return .= '</div>';

  $return .= '<div class="clear"></div>';
  
  $total_count = $the_query->found_posts;
  if($total_count > $page_size) {
    $return .= '<a href="#" class="load-more-button">Loading Methods</a>';
  }

  wp_reset_query();
  return $return;
}


function oit_display_method_card($result) {
  $return = '';
  $o = get_post_meta($result->ID);
  $process = get_the_terms( $result->ID , 'process' );
  $process_slug = $process[0]->slug;

  $link_url = get_permalink($result->ID);
  $image = get_field('method_image', $result->ID);

  if(!($image)) {
    $image = oit_method_image($process[0]->term_id);
  }
  $image_url = $image['sizes']['method_thumbnail'];

  $return .= '<a class="method-card item-'.$result->ID.' '.$process_slug.'" href="'.$link_url.'">';
    $return .= '<header class="item-header">';
      $return .= '<img src="'.$image_url.'" alt="'.$result->post_title.'" class="img-fluid" />';
      $return .= '<i class="item-icon" title=""></i>';
    $return .= '</header>';

    $return .= '<div class="item-content">';
      $return .= '<h3>'.$result->post_title.'</h3>';
      $return .= apply_filters('the_content', $o['excerpt'][0]);
    $return .= '</div>';
    
    $return .= '<footer class="item-footer">';
      $return .= '<div class="difficulty-level '.$o['difficulty_level'][0].'">'.ucfirst($o['difficulty_level'][0]).'</div>';
      $return .= '<div class="duration">'.oit_method_duration($o).'</div>';
    $return .= '</footer>';
  $return .= '</a>';
  
  return $return;
}



/* 
 * Functions related to questions listing
 */
function oit_get_questions() {
  $args=array(
      'post_type' => 'question',
      'post_status' => 'publish',
      'posts_per_page' => -1,
  );
  $questions = query_posts($args);
  wp_reset_query();
  return $questions;
}

function oit_questions_list($questions=null) {
  if(is_null($questions)) {
    $questions = oit_get_questions();
  }
  
  $return = '';
  if($questions) {
    foreach($questions as $question) {
      $return .= '<div class="question-item question-item-'.$question->ID.'">';
        $return .= '<h3 class="item-header">'.$question->post_title.'</h3>';
        $return .= '<div class="item-content">';
          $return .= apply_filters('the_content', $question->post_content);
        $return .= '</div>';
      $return .= '</div>';
    }
  }
  
  return $return;
}

function oit_questions_dropdown($questions=null) {
  if(is_null($questions)) {
    $questions = oit_get_questions();
  }
  
  $return = '';
  if($questions) {
    $return .= '<div class="questions-dropdown">';
      $return .= '<h3 class="dropdown-header"><span class="heading3 hidden-md-up">Commonly Asked Questions</span><span class="oit_questions_dropdown hidden-sm-down">Select Questions</span></h3>';
      $return .= '<a href="#" class="dropdown-toggle"></a>';
      $return .= '<ul class="dropdown-list">';
      foreach($questions as $question) {
        $return .= '<li><a href="'.SITE_URL.'/questions/#'.$question->ID.'" data-id="'.$question->ID.'">'.$question->post_title.'</a></li>';
    }
    $return .= '</ul>';
    $return .= '</div>';
  }
  return $return;
}



/* 
 * Functions related to glossary term slisting
 */
function oit_get_terms() {
  $args=array(
      'post_type' => 'term',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC'
  );
  $terms = query_posts($args);
  wp_reset_query();
  return $terms;
}

function oit_terms_list($terms=null) {
  if(is_null($terms)) {
    $terms = oit_get_terms();
  }
  
  $return = '';
  if($terms) {
    foreach($terms as $term) {
      $return .= '<div class="term-item item-'.$term->ID.' letter-'.strtoupper(substr($term->post_title, 0, 1)).'">';
        $return .= '<h3 class="item-header">'.$term->post_title.'</h3>';
        $return .= apply_filters('the_content', $term->post_content);
      $return .= '</div>';
    }
  }
  
  return $return;
}

function oit_term_letters($terms=null) {
  if(is_null($terms)) {
    $terms = oit_get_terms();
  }
  
  $return = '';
  $letters = array();
  if($terms) {
    foreach($terms as $term) {
      $letter = strtoupper(substr($term->post_title, 0, 1));
      if(!(in_array($letter, $letters))) {
        $letters[] = $letter;
      }
    }
    
    $return .= '<ul class="glossary-letters">';
    foreach (range('A', 'Z') as $char) {
      $return .= '<li>';
      $return .= (in_array($char, $letters)) ? '<a href="#letter-'.$char.'">'.$char.'</a>' : $char;
      $return .= '</li>';
    }
    $return .= '</ul>';
  }
  return $return;
}


function oit_process_pages() {
  $pages = get_posts(array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'meta_key' => '_wp_page_template',
      'orderby' => 'menu_order',
      'order' => 'ASC',
      'meta_value' => 'page-templates/process.php'
  ));
  
  wp_reset_query();
  return $pages;
}

function oit_home_processes() {
  $processes = oit_process_pages();
  if($processes) {
    $counter = 0;
    $illustrations = '<ul class="process-illustration switch-process">';
    $text_links = '<ul class="pager-links switch-process">';
    $process_content = '';
    $active_slug = '';
    foreach($processes as $process) {
      $slug = $process->post_name;
      if($counter===0) {
        $active = ' active';
        $active_slug = $slug;
      } else {
        $active = '';
      }
      
      $illustrations .= '<li class="process-'.$slug.$active.'"><a href="#'.$slug.$active.'" data-class="'.$slug.'">'.$process->post_title.'</a></li>';
      $text_links .= '<li class="process-'.$slug.$active.'"><a href="'.$slug.$active.'" data-class="'.$slug.'">'.$process->post_title.'</a></li>';
      
      $process_content .= '<div class="process-content '.$slug.$active.'">';
        $process_content .= '<h2 class="heading2"><a href="'.SITE_URL.'/'.$slug.'/">'.$process->post_title.'</a></h2>';
        $process_content .= apply_filters('the_content', $process->post_content);
        $process_content .= '<a href="'.SITE_URL.'/'.$slug.'/" class="learn-more-link">Learn more</a>';
      $process_content .= '</div>'; 
      
      $counter++;
    }
    $illustrations .= '</ul>';
    $text_links .= '</ul>';
  }
  
  
  $return = '<section id="section-processes" class="'.$active_slug.'">';
    $return .= '<a href="#prev" class="prev">Previous</a>';
    $return .= '<a href="#next" class="next">Next</a>';
  
  
    $return .= '<div class="container">';
      $return .= '<div class="row">';
        $return .= '<div class="col-md-10 col-xs-12 col-md-offset-1">';
          $return .= '<div class="row">';
            $return .= '<div class="col-lg-6 col-xs-12">';
              $return .= '<div class="home-illustration-wrapper">'.$illustrations.'</div>';
            $return .= '</div>';

            $return .= '<div class="col-lg-6 col-xs-12 illustration-content-wrapper">';
              $return .= '<div class="process-illustration-content">'.$process_content.'</div>';
            $return .= '</div>';
          $return .= '</div>';
        $return .= '</div>';
      $return .= '</div>';
      $return .= $text_links;
    $return .= '</div>';
  $return .= '</section>';
  
  return $return;
}


function oit_process_slider() {
  global $post;
  $processes = oit_process_pages();
  $processList = array();
  if($processes) {
    foreach($processes as $process) {
      $processList[$process->post_name] = $process->post_title;
    }
    
    $counter = 0;
    $current = 0;
    $slides = array();
    $return = '<section id="process-slider">';
    $return .= '<ul>';
    foreach($processes as $process) {
      $slug = $process->post_name;
      
      if($slug===$post->post_name) {
        $current = $counter;
      }
      
      $slide = '<li class="process-slide">';
        $slide .= '<div class="process-illustration-wrapper">';
          $slide .= '<div class="container">';
            $slide .= '<div class="col-md-10 col-xs-12 col-md-offset-1">';
              $slide .= '<div class="row">';
                $slide .= '<div class="col-lg-6 col-xs-12">';
                  $slide .= '<ul class="process-illustration">';
                    foreach($processList as $key=>$val) {
                      $slide .= '<li class="process-'.$key.(($key==$process->post_name) ? ' active': ' hidden-md-down').'">'.$val.'</li>';
                    }
                  $slide .= '</ul>';
                $slide .= '</div>';

                $slide .= '<div class="col-lg-6 col-xs-12 position-static">';
                  $slide .= '<div class="process-illustration-content">';
                    $slide .= '<h2 class="heading2">'.$process->post_title.'</h2>';
                    $slide .= apply_filters('the_content', $process->post_content);
                  $slide .= '</div>';
                $slide .= '</div>';
              $slide .= '</div>';
            $slide .= '</div>';
          $slide .= '</div>';
        $slide .= '</div>';
        
        
        $slide .= '<div class="process-methods">';
          $slide .= '<div class="container">';
            $slide .= '<div class="heading-wrapper">';
              $slide .= '<h3 class="heading3">Methods : '.$process->post_title.'</h3>';
              $slide .= '<a href="'.SITE_URL.'/methods/" class="quick-link link-left hidden-xs-down">View all</a>';
              $slide .= '<a href="'.SUBMIT_PAGE_URL.'/method/" class="quick-link link-right hidden-xs-down">Submit a method</a>';
            $slide .= '</div>';
            $slide .= '<div class="row">';
              $slide .= oit_methods($slug, -1);
            $slide .= '</div>';
            $slide .= '<div class="footer-quick-links hidden-sm-up">';
              $slide .= '<a href="'.SITE_URL.'/methods/" class="quick-link link-left">View all</a><br /><a href="'.SUBMIT_PAGE_URL.'/method/" class="quick-link">Submit a method</a>';
            $slide .= '</div>';
            
          $slide .= '</div>';
        $slide .= '</div>';
        
      $slide .= '</li>';
      
      $slides[$counter] = $slide;
      $counter++;
    }
    
    $totSlides = count($slides);
    for($i=$current; $i<$totSlides; $i++) {
      $return .= $slides[$i];
    }
    for($i=0; $i<$current; $i++) {
      $return .= $slides[$i];
    }
    $return .= '</ul>';
    $return .= '</section>';
  }
  return $return;
}



function oit_method_details($o) {
  $return = '';
  
  
  $return .= '<div class="row">';
    $return .= '<div class="col-sm-4 col-xs-12">';
      $return .= '<div class="detail-item time-wrapper">';
        $return .= '<h4>Time</h4>';
        $return .= oit_method_duration($o);
      $return .= '</div>';
    $return .= '</div>';

    $return .= '<div class="col-sm-4 col-xs-12">';
      $return .= '<div class="detail-item participants-wrapper">';
        $return .= '<h4>Participants</h4>';
        $return .= $o['participants'][0];
//        $return .= (int)$o['participants'][0] . ' person'.(((int)$o['participants'][0] > 1) ? 's': '');

      $return .= '</div>';
    $return .= '</div>';
    
    $return .= '<div class="col-sm-4 col-xs-12">';
      $return .= '<div class="detail-item difficulty-wrapper '.$o['difficulty_level'][0].'">';
        $return .= '<h4>Difficulty</h4>';
        if($o['difficulty_level_tooltip'][0]) {
          $return .= '<a href="#tooltip" data-toggle="tooltip">'.ucfirst($o['difficulty_level'][0]).'</a>';
          $return .= '<div class="tooltip-text">'.$o['difficulty_level_tooltip'][0].'</div>';
        } else {
          $return .= ucfirst($o['difficulty_level'][0]);
        }
      $return .= '</div>';
    $return .= '</div>';
  $return .= '</div>';
  
  $return .= '<div class="detail-item materials-wrapper">';
    $return .= '<h4>Materials</h4>';
    $return .= $o['materials'][0];
  $return .= '</div>';
  
  $return .= '<div class="clear"></div>';
  
  return $return;
}


function oit_method_examples($data) {
  $return = '';
  $examples = $data['method_examples'];
  if($examples) {
    $return .= '<div class="method-examples">';
    foreach ($examples as $example) {
      $return .= oit_display_method_examples($example);
    }
    $return .= '</div>';
  }
  
  return $return;
}



function oit_method_examples_v1($method_id) {
  $return = '';
  $serialized_id = serialize((string)$method_id);
  $examples = oit_get_method_examples($serialized_id);
  
  if($examples) {
    $return .= '<div class="method-examples">';
    foreach ($examples as $example) {
      $return .= oit_display_method_examples($example);
    }
    $return .= '</div>';
  }
  
  return $return;
}

function oit_get_method_examples($serialized_id) {
  $examples = get_posts(array(
    'post_type' => 'example',
    'post_status' => 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC',

    'meta_query' => array(
      'relation' => 'OR',
      array(
        'key' => 'related_methods',
        'value' => $serialized_id,
        'compare' => 'LIKE'
      ),
    )
  ));
  
  wp_reset_query();
  return $examples;
}

function oit_display_method_examples($example) {
  $return = '';
  
  $o = get_post_meta($example->ID);
  
  $project_link = $o['project_link'][0];
  if(!empty($project_link)) {
    $project_link_url = addhttp($project_link);
  }
  
  $image = get_field('project_image', $example->ID);
  $return .= '<div class="example-item">';
    $return .= '<h4 class="example-title">'.$example->post_title.'</h4>';
    $return .= '<div class="example-meta">';
      $return .= $o['org_ind_name'][0];
      $return .= (!empty($project_link)) ? '<span class="hidden-sm-down">, </span><br class="hidden-md-up" /><a href="'.$project_link_url.'" target="_blank">'.$project_link.'</a>' : '';
    $return .= '</div>';
    $return .= '<div class="example-image"><img src="'.$image.'" alt="" class="img-fluid" /></div>';
    $return .= '<div class="example-content">'.apply_filters('the_content', $example->post_content).'</div>';
  $return .= '</div>';
  return $return;
}


function oit_method_resources($data) {
  $return = '';
  $resources = $data['method_resources'];
  if($resources) {
    $return .= '<div class="method-resources">';
    foreach ($resources as $resource) {
      $return .= '<div class="resource-item">';
        $return .= '<h4 class="resource-title"><a href="'.$resource['resource_url'].'" target="_blank">'.$resource['resource_name'].'</a></h4>';
      $return .= '</div>';
    }
    $return .= '</div>';
  }
  return $return;
}


function oit_method_resources_v1($method_id) {
  $return = '';
  $serialized_id = serialize((string)$method_id);
  $resources = oit_get_method_resources($serialized_id);
  
  if($resources) {
    $return .= '<div class="method-resources">';
    foreach ($resources as $resource) {
      $return .= oit_display_method_resources($resource);
    }
    $return .= '</div>';
  }
  
  return $return;
}

function oit_get_method_resources($serialized_id) {
  $resources = get_posts(array(
    'post_type' => 'resource',
    'post_status' => 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC',

    'meta_query' => array(
      'relation' => 'OR',
      array(
        'key' => 'related_methods',
        'value' => $serialized_id,
        'compare' => 'LIKE'
      ),
    )
  ));
  
  wp_reset_query();
  return $resources;
}

function oit_display_method_resources($resource) {
  $return = '';
  $o = get_post_meta($resource->ID);
  $return .= '<div class="resource-item">';
    $return .= '<h4 class="resource-title"><a href="'.$o['link_url'][0].'" target="_blank">'.$resource->post_title.'</a></h4>';
  $return .= '</div>';
  return $return;
}


add_filter('query_vars', 'oit_add_custom_var', 0, 1);
function oit_add_custom_var($vars){
  $vars[] = 'action';
  return $vars;
}
add_rewrite_rule('^methods/([^/]+)/?$','index.php?pagename=methods&action=$matches[1]','top');
add_rewrite_rule('^contribute/([^/]+)/?$','index.php?pagename=contribute&action=$matches[1]','top');
add_rewrite_rule('^json/([^/]+)/?$','index.php?pagename=json&action=$matches[1]','top');
add_rewrite_rule('^ajax/([^/]+)/?$','index.php?pagename=ajax&action=$matches[1]','top');


function oit_contribute_form() {
  global $post;
  
  $return = '';
  $contribute_type = get_query_var('action');
  $contribute_types = array(
      'method' => 'Submit a method',
      'question' => 'Submit a question',
      'example' => 'Submit an example',
      'resource' => 'Submit a resource'
  );
  
  
  
  $return .= '<div class="form-wrapper">';
    $return .= '<div class="custom-form">';
      $return .= '<form name="frmContribute" enctype="multipart/form-data" method="post" action="'.SITE_URL.'/ajax/contribute" class="ajax-form">';

        $return .= '<div class="ajax-msg"></div>';

        $return .= '<div class="form-fields">';
        $return .= '<div class="field-group">';
          $return .= '<label for="cmb_type" class="hidden-sm-down hidden-xs-up">Contribution Type</label>';
          $return .= '<select type="text" id="cmb_type" name="cmb_type" class="custom-dropdown">';
          
          foreach($contribute_types as $key=>$val) {
            $return .= '<option value="'.$key.'"'.(($key==$contribute_type) ? ' selected="selected"' : '').'>'.$val.'</option>';
          }
          $return .= '</select>';
        $return .= '</div>';
      
      
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_name">Name</label>';
          $return .= '<input type="text" id="txt_name" name="txt_name" class="field-input" />';
        $return .= '</div>';
        
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_email">Email Address (required)</label>';
          $return .= '<input type="text" id="txt_email" name="txt_email" class="field-input required email" />';
        $return .= '</div>';
        
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_message">Message (required)</label>';
          $return .= '<textarea id="txt_message" name="txt_message" class="field-input required"></textarea>';
        $return .= '</div>';
        
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_attachment">Image Attachment</label>';

          $return .= '<input type="text" id="txt_filename" name="txt_filename" readonly="readonly" class="field-input" />';
          $return .= '<div class="input-file-wrapper">';
            $return .= '<input type="file" id="txt_attachment" name="txt_attachment" />';
            $return .= 'Browse';
          $return .= '</div>';
          $return .= '<div class="clear"></div>';
        $return .= '</div>';
        
        $return .= '<div class="field-group field-group-terms">';
          $return .= '<input type="checkbox" id="chk_terms" name="chk_terms" class="required" /> &nbsp; I agree to the <a href="#" data-toggle="modal" data-target="#modalSubmissionTerms">Toolkit Submission Terms</a>';
        $return .= '</div>';
        
//        $return .= '<div class="field-group field-group-recaptcha">';
//          $return .= '<div class="g-recaptcha" data-sitekey="6LcuJSUTAAAAAGfyCSFI4zN_o0TKkPTokmvH0qt3"></div>';
//        $return .= '</div>';
        
        $return .= '<div class="field-group field-group-action">';
          $return .= '<input type="hidden" name="http_referer" value="'.$_SERVER['HTTP_REFERER'].'" />';
          $return .= '<input type="submit" class="field-submit" value="Submit" />';
        $return .= '</div>';
        $return .= '</div><!-- .form-fields -->';
      $return .= '</form>';
    $return .= '</div>';
  $return .= '</div>';
  
  $return .= '<div id="modalSubmissionTerms" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Toolkit Submission Terms</h4>
      </div>
      <div class="modal-body">'.apply_filters('the_content', get_field('submission_terms', $post->ID)).'</div>
    </div>
  </div>
</div>';
  
  return $return;
}



function oit_method_duration($o) {
  $return = '';

  $time_type = $o['method_time'][0];
  if($time_type=='hour') {
    $min = (float)$o['time_min_hours'][0];
    $max = (float)$o['time_max_hours'][0];
    $minRatio = ($min == '0.5' OR $min == '.5' ) ? '1/2' : $min;
    $maxRatio = ($max == '0.5' OR $max == '.5' ) ? '1/2' : $max;
  } else {
    $min = (float)$o['time_min'][0];
    $max = (float)$o['time_max'][0];
    $minRatio = ($min < 1) ? oit_get_ratio($min) : $min;
    $maxRatio = ($max < 1) ? oit_get_ratio($max) : $max;
  }

  if($max > 0) {
    $return .= $minRatio . ' - ' . $maxRatio . ' '. $time_type .(($max > 1) ? 's': '');
  } else {
    $return .= $minRatio . ' '.$time_type.(($min > 1) ? 's': '');
  }


  
  
  // if($max > 0) {
  //   $return .= $minDays . ' - ' . $maxDays . ' Day'.(($max > 1) ? 's': '');
  // } else {
  //   $return .= $minDays . ' Day'.(($max > 1) ? 's': '');
  // }
  return $return;
}

function oit_get_ratio($val) {
  $var1=$val * 100;
  $var2=100;

  for($x=$var2;$x>1;$x--) {
    if(($var1%$x)==0 && ($var2 % $x)==0) {
      $var1 = $var1/$x;
      $var2 = $var2/$x;
    }
  }
  return "{$var1}/{$var2}";
}


function addhttp($url) {
  if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "http://" . $url;
  }
  return $url;
}


function oit_search_form() {
  $return = '';
  $search_text = '';

  $process_slug = get_query_var('action');
  if(!empty($process_slug)) {
    if($process_slug === 'search') {
      $search_text = filter_input(INPUT_POST, 'search_text');
      $search_text = sanitize_text_field($search_text);
      $search_text = preg_replace('/[^a-zA-Z0-9-_ \.]/','', $search_text);
    }
  }

  $return .= '<form role="search" method="post" id="searchform" class="searchform" action="'.SITE_URL.'/methods/search/">';
    $return .= '<div>';
      $return .= '<label class="screen-reader-text" for="search_text">Search for:</label>';
        $return .= '<input type="text" value="'.$search_text.'" name="search_text" id="search_text" />';
        $return .= '<input type="submit" id="searchsubmit" value="Search" />';
      $return .= '</div>';
    $return .= '</form>';

  return $return;
}


function oit_method_image($process_term_id) {
  return get_field('process_default_image', 'process_'.$process_term_id);
}


function oit_message_slider() {
  global $post;
  $return = '';
  
  $messages = get_field('message_text', $post->ID);
  if($messages) {
    $return .= '<ul>';
    foreach($messages as $message) {
      $return .= '<li><span>'.$message['message_text'].'</span></li>';
    }
    $return .= '</ul>';
  }
  return $return;
}

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );