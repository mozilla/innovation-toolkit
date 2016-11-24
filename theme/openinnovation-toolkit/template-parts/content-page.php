<?php
/**
 * The template used for displaying page content
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
    <h1 class="entry-title heading1"><?php echo get_custom_post_title();?></h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		the_content();
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
