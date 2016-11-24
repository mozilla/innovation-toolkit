<?php
/**
 * The template used for displaying about page content
 */

$o = get_post_meta($post->ID);
$banner_image = get_field('banner_image');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <section class="section-about">
    <div class="container">
      <div class="row">
        <div class="col-md-10 col-xs-12 col-md-offset-1">
          <header class="entry-header">
            <h1 class="entry-title heading1"><?php echo get_custom_post_title();?></h1>
          </header><!-- .entry-header -->
        </div>
      </div>

      <div class="row">
        <div class="col-sm-7 col-xs-12 col-md-offset-1">
          <div class="entry-content">
            <?php
            the_content();
            ?>
          </div><!-- .entry-content -->


        </div>
        <div class="col-sm-3 col-xs-12">
          <blockquote>
            <p><span class="quote-start">&ldquo;</span><?php echo $o['blockquote_text'][0];?>&rdquo;</p>
          </blockquote>
        </div>
      </div>
    </div><!-- .container -->
  </section>
  
  <section class="section-image" data-bgimg="<?php echo $banner_image;?>"></section>
  
  <section class="section-philosophy">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12 col-md-push-5">
          <div class="entry-content">
            <h2 class="heading2">Our Philosophy</h2>
            <?php
              echo apply_filters('the_content', $o['philosophy_content'][0]);
            ?>
          </div><!-- .entry-content -->
        </div>
        
        <div class="col-md-4 col-xs-12 col-md-pull-5">
          <h2 class="heading2">&nbsp;</h2>
          <?php
            echo apply_filters('the_content', $o['contact_details'][0]);
          ?>
        </div>
      </div>
    </div><!-- .container -->
  </section>
</article><!-- #post-## -->
