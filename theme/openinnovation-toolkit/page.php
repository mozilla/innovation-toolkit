<?php
/**
 * The template for displaying pages
 * This is the template that displays all pages by default.
 */

get_header(); ?>
<div id="primary" class="content-area">
  <main id="main" class="site-main page-default" role="main">
    <div class="container">
      <div class="row">
        <div class="col-md-10 col-xs-12 col-md-offset-1">
        <?php
          // Start the loop.
          while ( have_posts() ) : the_post();
            // Include the page content template.
            get_template_part( 'template-parts/content', 'page' );
          endwhile;
        ?>
        </div>
      </div>
    </div>
  </main><!-- .site-main -->
</div><!-- .content-area -->

<?php 
get_footer();
