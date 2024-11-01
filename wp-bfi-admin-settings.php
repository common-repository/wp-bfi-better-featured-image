<?php

	if( !defined('ABSPATH')) exit;

	//	WP BFI SETTINGS PAGE
	//	----------------------------------------------------------------------------------------------------

	$types = get_post_types();

	function wp_bfi_number_posts_by_type($post_type) {

		$total = 0;

		$num = wp_count_posts($post_type);

		foreach( $num as $key => $value ):
			$total += $value;
		endforeach;

		return $total;

	}

	function wp_bfi_number_posts_with_featured_image($post_type) {

		$num = get_posts(array(
			'posts_per_page' => -1,
			'post_type' => $post_type,
			'meta_key' => '_thumbnail_id'
		));

		return count($num);

	}

?>

<div class="wrap wp-bfi">

	<h1>WP BFI - <?php _e('Settings', 'wp-bfi-better-featured-image'); ?></h1>
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col"><?php _e('Post Type', 'wp-bfi-better-featured-image'); ?></th>
				<th scope="col"><?php _e('Featured Image', 'wp-bfi-better-featured-image'); ?></th>
				<th class="column-wp_bfi_featured_image" scope="col"><?php _e('Default Featured Image', 'wp-bfi-better-featured-image'); ?></th>
				<th scope="col"><?php _e('Disable WP BFI', 'wp-bfi-better-featured-image'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $types as $t):
				$object = get_post_type_object($t);
				if( post_type_supports($t, 'thumbnail') ): ?>
					<tr data-post-type="<?php echo $object->name; ?>">
						<td>
							<a href="edit.php?post_type=<?php echo $object->name; ?>"><strong><?php echo $object->label; ?></strong></a> (<?php echo $object->name; ?>)<br />
							<?php echo sprintf(__('<small>Out of <span class="num-posts">%d</span> posts, <span class="num-with-featured-image">%d</span> have a featured image.</small>', 'wp-bfi-better-featured-image'), wp_bfi_number_posts_by_type($object->name), wp_bfi_number_posts_with_featured_image($object->name)); ?>
						</td>
						<td>
							<a class="button" href="#" data-action="unlink-all" title="<?php _e('Unlink All', 'wp-bfi-better-featured-image'); ?>"><?php _e('Unlink All', 'wp-bfi-better-featured-image'); ?></a> <span class="spinner"></span>
						</td>
						<td class="column-wp_bfi_featured_image">
							<?php $thumb_url = wp_get_attachment_image_src(get_option('wp_bfi_default_featured_image_' . $object->name), 'full'); ?>
							<div class="wp-bfi-wrapper"<?php if( !isset($thumb_url[0]) ): ?> style="display:none;"<?php endif; ?>>
								<div class="wp-bfi-image-wrapper">
									<?php
										$featured_image_data = get_post(get_option('wp_bfi_default_featured_image_' . $object->name));
										$title = !empty($featured_image_data) ? $featured_image_data->post_title : '';
										echo wp_get_attachment_image(get_option('wp_bfi_default_featured_image_' . $object->name), array(360, null), null, array('class' => 'wp-bfi-featured-image', 'title' => $title));
									?>
									<div class="wp-bfi-featured-image-info"></div>
								</div>
								<div class="wp-bfi-image-buttons">
									<a class="button" href="#" data-action="info-default" title="<?php _e('Default Featured Image Information', 'wp-bfi-better-featured-image'); ?>"><i class="fas fa-info"></i></a>
									<a class="button" href="<?php if( isset($thumb_url[0]) ) echo $thumb_url[0]; ?>" data-action="view-default" title="<?php _e('View Default Featured Image', 'wp-bfi-better-featured-image'); ?>" target="_blank"><i class="fas fa-image"></i></a>
									<a class="button" href="#" data-action="replace-default" title="<?php _e('Replace Default Featured Image', 'wp-bfi-better-featured-image'); ?>"><i class="fas fa-exchange-alt"></i></a>
									<a class="button" href="#" data-action="unlink-default" title="<?php _e('Unlink Default Featured Image', 'wp-bfi-better-featured-image'); ?>"><i class="fas fa-trash"></i></a>
								</div>
							</div>
							<a class="button button-primary"<?php if( isset($thumb_url[0]) ): ?> style="display:none;"<?php endif; ?> href="#" data-action="set-default" title="<?php _e('Set Default Featured Image', 'wp-bfi-better-featured-image'); ?>"><?php _e('Set Default Featured Image', 'wp-bfi-better-featured-image'); ?></a>
						</td>
						<td>
							<input class="wp-bfi-featured-image-disabled" type="checkbox" <?php if( get_option('wp_bfi_disabled_' . $t) ): ?>checked<?php endif; ?> />
						</td>
					</tr>
				<?php endif;
			endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col"><?php _e('Post Type', 'wp-bfi-better-featured-image'); ?></th>
				<th scope="col"><?php _e('Featured Image', 'wp-bfi-better-featured-image'); ?></th>
				<th class="column-wp_bfi_featured_image" scope="col"><?php _e('Default Featured Image', 'wp-bfi-better-featured-image'); ?></th>
				<th scope="col"><?php _e('Disable WP BFI', 'wp-bfi-better-featured-image'); ?></th>
			</tr>
		</tfoot>
	</table>

	<h2><i class="fas fa-sm fa-exclamation-triangle"></i> Important</h2>
	<ul class="with-bullets">
		<li><?php _e('Unlinking featured images from all posts is done regardless of its status (<em>publish</em>, <em>future</em>, <em>draft</em>, <em>pending</em>, <em>private</em>, <em>trash</em> and <em>auto-draft</em>).', 'wp-bfi-better-featured-image'); ?></li>
		<li><?php _e('Setting a default featured image will only be effective to new posts (or updated ones) for the selected type.', 'wp-bfi-better-featured-image'); ?></li>
	</ul>

	<h2>Support</h2>
	<ul class="with-bullets">
		<li><?php echo sprintf(__('Having issues? Create a <a href="%s" target="_blank">new topic</a> on the official support forum of WordPress.org, giving as much detail as possible.', 'wp-bfi-better-featured-image'), 'https://wordpress.org/support/plugin/wp-bfi-better-featured-image'); ?></li>
	</ul>

	<h2><?php _e('You like this plugin?', 'wp-bfi-better-featured-image'); ?></h2>
	<ul class="with-bullets">
		<li><?php _e('Share your experience', 'wp-bfi-better-featured-image'); ?>: <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//wp-bfi.verya.ca/" target="_blank"><i class="fab fa-lg fa-facebook-square"></i></a> <a href="https://twitter.com/home?status=https%3A//wp-bfi.verya.ca/" target="_blank"><i class="fab fa-lg fa-twitter-square"></i></a> <a href="https://www.linkedin.com/shareArticle?mini=true&url=https%3A//wp-bfi.verya.ca/&title=WP%20BFI%20%E2%80%93%20Better%20Featured%20Image&summary=&source=" target="_blank"><i class="fab fa-lg fa-linkedin"></i></a></li>
		<li><?php echo sprintf(__('Support this plugin, <a href="%s" target="_blank">buy us a coffee</a>!', 'wp-bfi-better-featured-image'), 'https://www.buymeacoffee.com/veryaca'); ?></li>
		<li><?php echo sprintf(__('Have an idea for this plugin? Send me an email: <a href="%s">info@verya.ca</a>', 'wp-bfi-better-featured-image'), 'mailto:info@verya.ca'); ?></li>
		<li><?php echo sprintf(__('Don\'t forget to rate this plugin on <a href="%s" target="_blank">WordPress.org</a>.', 'wp-bfi-better-featured-image'), esc_url('https://wp-bfi.verya.ca/rate')); ?></li>
		<li><?php echo sprintf(__('<a href="%s" target="_blank">Translate this plugin in other languages</a>, so other users can benefit from it.', 'wp-bfi-better-featured-image'), esc_url('https://wp-bfi.verya.ca/translate')); ?></li>
	</ul>

</div>
