<?php
/**
 * Template part for displaying posts.
 *
 * @package tecblogger
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php tecblogger_post_thumbnail(); ?>
	<?php if ( 'post' == get_post_type() ) : ?>
	<header class="entry-header">
		<div class="entry-meta">
			<?php tecblogger_posted_on(); ?>
			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<li><?php tecblogger_post_comments(); ?></li>
			<?php endif; ?>			
		</div><!-- .entry-meta -->	
	
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>




	</header><!-- .entry-header -->
	<?php endif; ?>
	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'tecblogger' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tecblogger' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	
	<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
	<footer class="entry-footer">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'tecblogger' ) );
			if ( $categories_list && tecblogger_categorized_blog() ) :
		?>
		<span class="cat-links">
			<?php printf( __( 'Posted in %1$s', 'tecblogger' ), $categories_list ); ?>
		</span>
		<?php endif; // End if categories ?>
	
		<?php
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', __( ', ', 'tecblogger' ) );
			if ( $tags_list ) :
		?>
		<span class="tags-links">
			<?php printf( __( 'Tagged %1$s', 'tecblogger' ), $tags_list ); ?>
		</span>
		<?php endif; // End if $tags_list ?>

		<?php edit_post_link( __( 'Edit', 'tecblogger' ), '<span class="edit-link">', '</span>' ); ?>		
	</footer><!-- .entry-footer -->
	<?php endif; // End if 'post' == get_post_type() ?>
</article><!-- #post-## -->
