<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>
    
  </div><!-- .site-content -->

  <footer class="site-footer" role="contentinfo">
    <div id="colophon">
      <a href="#top" id="goTop" title="Scroll to Top">Top<span></span></a>
      <div class="container">
        <?php if ( has_nav_menu( 'footer' ) ) : ?>
          <nav id="footer-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Primary Menu', 'twentysixteen' ); ?>">
            <?php
              wp_nav_menu( array(
                'theme_location' => 'footer',
                'menu_class'     => 'footer-menu',
               ) );
            ?>
          </nav><!-- .main-navigation -->
        <?php endif; ?>

        <div class="site-info">
          <div class="footer-logo">
            <a href="https://mozilla.org/" target="_blank"><img src="<?php echo THEME_PATH;?>/images/logo-mozilla.png" alt="Link to Mozilla.org"></a>
          </div>
          <p>Mozilla is a global non-profit dedicated to putting you in control of your online experience and shaping the future of the Web for the public good. Visit us at <a href="https://mozilla.org/" target="_blank">mozilla.org</a></p>
        </div><!-- .site-info -->
      </div>
    </div><!-- #colophon -->
  
    
  </footer><!-- .site-footer -->
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
