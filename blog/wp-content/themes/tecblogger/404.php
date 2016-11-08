<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package tecblogger
 */

get_header(); ?>
<div class="content-area">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<div id="primary" class="content-area">
					<main id="main" class="site-main" role="main">

						<section class="error-404 not-found">
							<header class="page-header">
								<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'tecblogger' ); ?></h1>
							</header><!-- .page-header -->

							<div class="page-content">
								<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'tecblogger' ); ?></p>

								<?php get_search_form(); ?>


							</div><!-- .page-content -->
						</section><!-- .error-404 -->

					</main><!-- #main -->
				</div><!-- #primary -->
			</div>
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
