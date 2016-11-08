<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package tecblogger
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>


			<!-- Sidebar -->
			<div class="col-md-4">
				<div class="sidebar-area">	
					<div id="secondary" class="widget-area" role="complementary">
						<?php dynamic_sidebar( 'sidebar-1' ); ?>
					</div><!-- End sidebar -->
				</div>
			</div>
		</div>
	</div>
</div>