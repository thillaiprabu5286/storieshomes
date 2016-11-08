<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyfifteen' ); ?></a>
        
	<div id="sidebar" class="sidebar">
            
	
		<header id="masthead" class="site-header" role="banner">
			<div class="site-branding">
				<?php
					if ( is_front_page() && is_home() ) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url( 'http://www.storieshomes.com/' ); ?>" rel="home"><!-- <?php bloginfo( 'name' ); ?>--><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/stories-logo.png" ></a></h1>
					<?php else : ?>
						<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><!-- <?php bloginfo( 'name' ); ?>--><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/stories-logo.png"></a></p>
					<?php endif;

					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo $description; ?></p>
					<?php endif;
				?>
				<button class="secondary-toggle"><?php _e( 'Menu and widgets', 'twentyfifteen' ); ?></button>
			</div><!-- .site-branding -->
		</header><!-- .site-header -->

		<?php get_sidebar(); ?>
	</div><!-- .sidebar -->

	<div id="content" class="site-content">
            <ul class="main-menu">
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
