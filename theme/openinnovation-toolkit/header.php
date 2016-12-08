<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
  <?php if(is_page(1259)) :
    echo '<script src="https://www.google.com/recaptcha/api.js?onload=loadCaptcha&render=explicit"></script>';
  endif; ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
  <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentysixteen' ); ?></a>

  <header id="masthead" class="site-header" role="banner">
    <div class="container1">
      <div class="site-branding">
        <?php
          if ( is_front_page() && is_home() ) : ?>
            <h1 class="site-title"><a href="<?php echo SITE_URL; ?>" title="<?php echo SITE_NAME; ?>" rel="home"><img src="https://toolkit.mozilla.org/wp-content/uploads/2016/09/logo.png" alt="<?php echo SITE_NAME; ?>" class="img-responsive" /></a></h1>
          <?php else : ?>
            <p class="site-title"><a href="<?php echo SITE_URL; ?>" title="<?php echo SITE_NAME; ?>" rel="home"><img src="https://toolkit.mozilla.org/wp-content/uploads/2016/09/logo.png" alt="<?php echo SITE_NAME; ?>" class="img-responsive" /></a></p>
          <?php endif; 
        ?>
      </div><!-- .site-branding -->
      
      <button id="menu-toggle" class="menu-toggle"><span><?php _e( 'Primary Menu', 'twentysixteen' ); ?></span></button>
      <div class="site-navigation">
        <?php if ( has_nav_menu( 'primary' ) ) : ?>
          <div id="site-header-menu" class="site-header-menu">
            <?php if ( has_nav_menu( 'primary' ) ) : ?>
              <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'twentysixteen' ); ?>">
                <?php
                  wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'primary-menu',
                   ) );
                ?>
              </nav><!-- .main-navigation -->
            <?php endif; ?>
          </div><!-- .site-header-menu -->
        <?php endif; ?>
          
        <?php echo oit_search_form();?>
      </div>
    </div>
  </header><!-- .site-header -->

  <div id="content" class="site-content">