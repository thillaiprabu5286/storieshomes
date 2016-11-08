<?php
/**
 * The sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) || is_active_sidebar( 'sidebar-1' )  ) : ?>
	<div id="secondary" class="secondary">
            <h2 id="main-manu" class="widget-title"><a href="javascript:void(0);" onclick="menushowhide();">Navigate To Stories Homes</a></h2>
            <ul class="main-menu1" id="m">
            <li>
                <a class="level0 has-children" 
                   href="http://www.storieshomes.com/furniture.html">
                    Furniture
                </a>
            </li>
            <li>
                <a class="level0 has-children" 
                   href="http://www.storieshomes.com/accessories.html">
                    Accessories
                </a>
            </li>
            <li>
                <a class="level0 has-children" 
                   href="http://www.storieshomes.com/furnish.html">
                    Furnish
                </a>
            </li>
            <li>
                <a class="level0 has-children" 
                   href="http://www.storieshomes.com/light.html">
                    Light
                </a>
            </li>
            <li>
                <a class="level0 has-children" 
                   href="http://www.storieshomes.com/our-story">
                    Our Story
                </a>
            </li>
            <li>
                <a class="level0 has-children" 
                   href="http://www.storieshomes.com/customer-service">
                    Support
                </a>
            </li>
            
        </ul>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav id="site-navigation" class="main-navigation" role="navigation">
				<?php
					// Primary navigation menu.
					wp_nav_menu( array(
						'menu_class'     => 'nav-menu',
						'theme_location' => 'primary',
					) );
				?>
			</nav><!-- .main-navigation -->
		<?php endif; ?>

		<?php if ( has_nav_menu( 'social' ) ) : ?>
			<nav id="social-navigation" class="social-navigation" role="navigation">
				<?php
					// Social links navigation menu.
					wp_nav_menu( array(
						'theme_location' => 'social',
						'depth'          => 1,
						'link_before'    => '<span class="screen-reader-text">',
						'link_after'     => '</span>',
					) );
				?>
			</nav><!-- .social-navigation -->
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
			<div id="widget-area" class="widget-area" role="complementary">
				<?php dynamic_sidebar( 'sidebar-1' ); ?>
			</div><!-- .widget-area -->
		<?php endif; ?>

	</div><!-- .secondary -->
        <script>
            function menushowhide(){
                if(document.getElementById('m').style.display == ""){
                    document.getElementById('m').style.display = 'block';
                }else{
                    document.getElementById('m').style.display = "";
                }
            }
        </script>
<?php endif; ?>
