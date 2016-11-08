<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package tecblogger
 */

?>

<div class="footer">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="site-info">
					<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'tecblogger' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'tecblogger' ), 'WordPress' ); ?></a>
					<span class="sep"> | </span>
					<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'tecblogger' ), 'tecblogger', '<a href="" rel="designer">themepoints</a>' ); ?>
				</div><!-- .site-info -->
			</div>	
		</div>
	</div>
</div>	


<?php wp_footer(); ?>

</body>
</html>
