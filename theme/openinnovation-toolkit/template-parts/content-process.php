<?php
/**
 * The template used for displaying page content
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <?php echo oit_process_slider();?>
</article><!-- #post-## -->
