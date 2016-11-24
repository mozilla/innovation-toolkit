<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();
      $o = get_post_meta($post->ID);
      echo '<pre>';
        var_dump($o);
      echo '</pre>';
			// Include the method post content template.
			get_template_part( 'template-parts/content', 'method' );
			// End of the loop.
		endwhile;
		?>

	</main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
