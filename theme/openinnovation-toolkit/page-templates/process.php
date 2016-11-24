<?php
/**
 * The template for process page
 * Template Name: Process
 */

get_header(); ?>
<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
  <?php
    // Start the loop.
    while ( have_posts() ) : the_post(); 
      // Include the page content template.
      get_template_part( 'template-parts/content', 'process' );
    endwhile; ?>
  </main><!-- .site-main -->
</div><!-- .content-area -->

<?php 
get_footer();
