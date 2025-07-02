<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage DEMOTHEME
 * @since Demo Theme 1.0
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Primary Menu', 'demotheme' ); ?>">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'primary',
							'menu_class'     => 'primary-menu',
						 ) );
					?>
				</nav><!-- .main-navigation -->
			<?php endif; ?>

			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'demotheme' ); ?>">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'social',
							'menu_class'     => 'social-links-menu',
							'depth'          => 1,
							'link_before'    => '<span class="screen-reader-text">',
							'link_after'     => '</span>',
						) );
					?>
				</nav><!-- .social-navigation -->
			<?php endif; ?>

                        <div class="site-info">
                                <?php
                                        /**
                                         * Fires before the demotheme footer text for footer customization.
                                         *
                                         * @since Demo Theme 1.0
                                         */
                                        do_action( 'demotheme_credits' );
                                ?>
                                <span class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>
                                <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'demotheme' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'demotheme' ), 'WordPress' ); ?></a>
                        </div><!-- .site-info -->
                    </footer><!-- .site-footer -->
                </div><!-- .site-inner -->
            </div><!-- .site -->

        <?php wp_footer(); ?>
    </body>
</html>
