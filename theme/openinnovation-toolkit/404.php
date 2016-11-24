<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>
<div id="primary" class="content-area">
  <?php
  // Including page banner/slider
  echo gunaintl_page_banner();
  ?>
  
  <main id="main" class="site-main container" role="main">
    <section class="error-404 not-found">
      <header class="entry-header">
        <h1 class="entry-title"><?php _e( 'Error 404', 'twentysixteen' ); ?></h1>
      </header><!-- .page-header -->
      
      <div class="entry-content">
        <p>Sorry, the page you tried cannot be found. You may have typed the path incorrectly or you may have used an outdated link. Go back to our <a href="<?php echo SITE_URL;?>">home page</a> or go back to <a href="#back">previous page</a></p>
      </div><!-- .entry-content -->
    </section><!-- .error-404 -->
  </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer();