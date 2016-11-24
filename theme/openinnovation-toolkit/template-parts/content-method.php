<?php
/**
 * The template used for displaying page content
 */

$o = get_post_meta($post->ID);

$process = get_the_terms( $post->ID , 'process' );
$process_slug = $process[0]->slug;
$link_url = get_permalink($post->ID);
$image = get_field('method_image', $post->ID);
$sections = get_field('method_sections');
?>

<div id="method-wrapper">
  <div class="method-sidebar secondary">
    <div class="sidebar-toggle hidden-md-up">Overview</div>
    <ul>
      <li><a href="#overview" class="active">Overview</a></li>
      <?php 
        foreach($sections as $section) {
          echo '<li><a href="#'.sanitize_title($section['section_title']).'">'.$section['section_title'].'</a></li>';
        }
      ?>
    </ul>
  </div>
  <div id="method-content">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <?php
        if($image) {
          echo '<div class="entry-banner" data-bgimg="'.$image['url'].'"></div>';
        } else {
          $image = oit_method_image($process[0]->term_id);
          echo '<div class="entry-banner" data-bgimg="'.$image['url'].'"></div>';
        }
      ?>

      <div class="entry-breadcrumb"><?php echo '<a href="'.METHODS_PAGE_URL.'">Methods</a>';?> <span class="separator">&gt;</span> <?php echo '<a href="'.METHODS_PAGE_URL.'/'.$process_slug.'">'.$process[0]->name.'</a>';?> <span class="separator">&gt;</span> <?php echo $post->post_title;?></div>
      
      
      <div id="overview" class="wrap">
        <div class="heading-wrapper">
          <?php the_title( '<h1 class="entry-title heading1">', '</h1>' ); ?>
          <a href="<?php echo SUBMIT_PAGE_URL;?>/example/" class="quick-link link-right hidden-sm-down">Submit an example</a>
        </div>

        <div class="entry-content">
          <?php 
            the_content();
          ?>
        </div>
      </div>
      
      <?php 
        
        foreach($sections as $section) {
          echo '<div id="'.sanitize_title($section['section_title']).'" class="method-section" data-bgcolor="'.$section['bg_color'].'">';
            echo '<div class="wrap">';
            switch($section['content_type']) {
              case 'details':
                echo oit_method_details($o);
                break;
                
              case 'resources': 
                echo '<div class="method-resources-header">';
                  echo '<h3 class="heading3">'.$section['section_title'].'</h3>';
                  echo '<a href="'.SUBMIT_PAGE_URL.'/resource/" class="quick-link link-right">Submit a resource</a>';
                echo '</div>';
                echo oit_method_resources($section);
                break;
                
              case 'examples':
                echo '<div class="method-examples-header">';
                  echo '<h3 class="heading3">'.$section['section_title'].'</h3>';
                  echo '<a href="'.SUBMIT_PAGE_URL.'/example/" class="quick-link link-right">Submit an example</a>';
                echo '</div>';
                echo oit_method_examples($section);
                break;
              case 'content':
              default:
                echo '<h3 class="heading3">'.$section['section_title'].'</h3>';
                echo apply_filters('the_content', $section['section_content']);
                break;
            }
            echo '</div>';
          echo '</div>';
        }
      ?>
    </article><!-- #post-## -->
  </div>
  <div class="clear"></div>
</div><!-- .container -->

