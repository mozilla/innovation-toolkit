<?php
/**
 * The template used for displaying contribution page content
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-xs-12 col-md-offset-1">
        <header class="entry-header">
          <h1 class="entry-title heading1"><?php echo get_custom_post_title();?></h1>
        </header><!-- .entry-header -->
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-5 col-xs-12 col-md-offset-1">
        <?php echo oit_contribute_form();?>
      </div>
      <div class="col-md-4 col-xs-12 col-md-offset-1">
        <div class="entry-content">
          <?php
          the_content();
          ?>
        </div><!-- .entry-content -->
      </div>
    </div>
  </div><!-- .container -->
</article><!-- #post-## -->