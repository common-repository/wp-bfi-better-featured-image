<?php

	if( !defined('ABSPATH') ) exit;

	/*
		Plugin Name:	WP BFI – Better Featured Image
		Plugin URI:		https://wp-bfi.verya.ca
		Description:	WP BFI lets you manage featured images in a more efficient way, by adding intuitive tools to your WordPress interface.
		Version:		0.8
		Author:			VERYA Inc.
		Author URI:		https://verya.ca
		Text Domain:	wp-bfi-better-featured-image
		Domain Path:	/langs/
		License:		GPLv3
		License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
	*/


	//	PLUGIN LOCALES
	//	----------------------------------------------------------------------------------------------------

		function wp_bfi_load_textdomain() {

			load_plugin_textdomain('wp-bfi-better-featured-image', false, dirname(plugin_basename(__FILE__)) . '/langs');

		} add_action('init', 'wp_bfi_load_textdomain');


	//	WP ADMIN MENU/SUBMENU
	//	----------------------------------------------------------------------------------------------------

		function wp_bfi_admin_menu() {

			add_submenu_page('upload.php', 'WP BFI - ' . __('Settings', 'wp-bfi-better-featured-image'), 'WP BFI - ' . __('Settings', 'wp-bfi-better-featured-image'), 'manage_options', 'wp_bfi_admin_settings', 'wp_bfi_admin_settings_page');

		} add_action('admin_menu', 'wp_bfi_admin_menu');

		function wp_bfi_admin_settings_page() {

			include_once('wp-bfi-admin-settings.php');

		}


	//	PLUGINS PAGE LINKS
	//	----------------------------------------------------------------------------------------------------

		function wp_bfi_plugin_action_links($links) {

			unset($links['edit']);

			return array_merge($links, array(
				'<a href="' . esc_url(get_admin_url(null, 'upload.php?page=wp_bfi_admin_settings')) . '">' . __('Settings', 'wp-bfi-better-featured-image') . '</a>',
				'<a href="https://www.buymeacoffee.com/veryaca" target="_blank">' . __('Buy us a coffee', 'wp-bfi-better-featured-image') . '</a>'
			));

		} add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_bfi_plugin_action_links');


	//	ENABLE FEATURED IMAGE SUPPORT
	//	----------------------------------------------------------------------------------------------------

		if( function_exists('add_theme_support') ):

			add_filter('manage_posts_columns', 'wp_bfi_columns', 5);
			add_action('manage_posts_custom_column', 'wp_bfi_custom_columns', 5, 2);
			add_filter('manage_pages_columns', 'wp_bfi_columns', 5);
			add_action('manage_pages_custom_column', 'wp_bfi_custom_columns', 5, 2);

		endif;

		function wp_bfi_columns($defaults) {

			if( !get_option('wp_bfi_disabled_' . get_post_type()) ):

				$defaults['wp_bfi_featured_image'] = __('Featured Image', 'wp-bfi-better-featured-image');

			endif;

			return $defaults;

		}

		function wp_bfi_custom_columns($column_name, $id) {

			$thumb_id = get_post_thumbnail_id();
			$thumb_url = wp_get_attachment_image_src($thumb_id, 'full');

			if( $column_name === 'wp_bfi_featured_image' ): ?>

				<div class="wp-bfi-wrapper"<?php if( !isset($thumb_url[0]) ): ?> style="display:none;"<?php endif; ?>>
					<div class="wp-bfi-image-wrapper">
						<?php echo get_the_post_thumbnail($id, array(360, null), array('class' => 'wp-bfi-featured-image', 'title' => get_post($thumb_id)->post_title)); ?>
						<div class="wp-bfi-featured-image-info"></div>
					</div>
					<div class="wp-bfi-image-buttons">
						<a class="button" href="#" data-action="info" title="<?php _e('Featured Image Information', 'wp-bfi-better-featured-image'); ?>"><i class="fa fa-info"></i></a>
						<a class="button" href="<?php if( isset($thumb_url[0]) ) echo $thumb_url[0]; ?>" data-action="view" title="<?php _e('View Featured Image', 'wp-bfi-better-featured-image'); ?>" target="_blank"><i class="fa fa-image"></i></a>
						<a class="button" href="#" data-action="replace" title="<?php _e('Replace Featured Image', 'wp-bfi-better-featured-image'); ?>"><i class="fa fa-exchange-alt"></i></a>
						<a class="button" href="#" data-action="unlink" title="<?php _e('Unlink Featured Image', 'wp-bfi-better-featured-image'); ?>"><i class="fa fa-trash-alt"></i></a>
					</div>
				</div>

				<a class="button button-primary"<?php if( isset($thumb_url[0]) ): ?> style="display:none;"<?php endif; ?> href="#" data-action="set" title="<?php _e('Set Featured Image', 'wp-bfi-better-featured-image'); ?>"><?php _e('Set Featured Image', 'wp-bfi-better-featured-image'); ?></a>

			<?php endif;

		}


	//	WP ADMIN STYLES AND SCRIPTS
	//	----------------------------------------------------------------------------------------------------

		function wp_bfi_admin_styles() {

			wp_enqueue_style('font-awesome', 'https://use.fontawesome.com/releases/v5.15.4/css/all.css', array(), '5.15.4');
			wp_enqueue_style('wp-bfi-better-featured-image', plugins_url('/css/wp-bfi-better-featured-image.css', __FILE__), array(), '0.7');

		} add_action('admin_print_styles', 'wp_bfi_admin_styles');

		function wp_bfi_admin_scripts() {

			wp_enqueue_media();

			wp_enqueue_script('wp-bfi-better-featured-image', plugins_url('/js/wp-bfi-better-featured-image.js', __FILE__), array('jquery'), '0.7');

			wp_localize_script('wp-bfi-better-featured-image', 'wp_bfi_lang', array(
				'featured_image'					=> __('Featured image', 'wp-bfi-better-featured-image'),
				'featured_image_replace'			=> __('Replace featured image', 'wp-bfi-better-featured-image'),
				'featured_image_replace_default'	=> __('Replace default featured image', 'wp-bfi-better-featured-image'),
				'featured_image_set'				=> __('Set featured image', 'wp-bfi-better-featured-image'),
				'featured_image_set_default'		=> __('Set default featured image', 'wp-bfi-better-featured-image'),
				'unlink_confirm'					=> __('Are you sure you want to unlink the featured image from this post/page? NOTE: It won\'t be deleted from the media library.', 'wp-bfi-better-featured-image'),
				'unlink_all_confirm'				=> __('Are you sure you want to unlink every featured image from this post type? NOTE: They won\'t be deleted from the media library.', 'wp-bfi-better-featured-image'),
				'unlink_default_confirm'			=> __('Are you sure you want to unlink the default featured image from this post type? NOTE: It won\'t be deleted from the media library.', 'wp-bfi-better-featured-image')
			));

		} add_action('admin_enqueue_scripts', 'wp_bfi_admin_scripts');


	//	WP AJAX CALLS
	//	----------------------------------------------------------------------------------------------------

		function wp_bfi_ajax_featured_image_unlink() {

			$post_id = intval($_POST['post-id']);
			if( !$post_id ) $post_id = '';

        	delete_post_thumbnail($post_id);

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_unlink', 'wp_bfi_ajax_featured_image_unlink');

		function wp_bfi_ajax_featured_image_unlink_default() {

			$post_type = sanitize_text_field($_POST['post-type']);
			if( !$post_type ) $post_type = '';

        	delete_option('wp_bfi_default_featured_image_' . $post_type);

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_unlink_default', 'wp_bfi_ajax_featured_image_unlink_default');

		function wp_bfi_ajax_featured_image_set() {

			$post_id = intval($_POST['post-id']);
			$attachment_id = intval($_POST['attachment-id']);
			if( !$post_id ) $post_id = '';
			if( !$attachment_id ) $attachment_id = '';

        	set_post_thumbnail($post_id, $attachment_id);

			$thumb_id = get_post_thumbnail_id($post_id);
			$thumb_url = wp_get_attachment_image_src($thumb_id, 'full');

			echo json_encode(array(
				'featured_image_title' => get_post(get_post_thumbnail_id($post_id))->post_title,
				'featured_image_url' => $thumb_url[0]
			));

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_set', 'wp_bfi_ajax_featured_image_set');

		function wp_bfi_ajax_featured_image_set_default() {

			$post_type = sanitize_text_field($_POST['post-type']);
			$attachment_id = intval($_POST['attachment-id']);
			if( !$post_type ) $post_type = '';
			if( !$attachment_id ) $attachment_id = '';

        	update_option('wp_bfi_default_featured_image_' . sanitize_text_field($_POST['post-type']), $attachment_id);

			$thumb_url = wp_get_attachment_image_src($attachment_id, 'full');

			echo json_encode(array(
				'featured_image_title' => get_the_title($attachment_id),
				'featured_image_url' => $thumb_url[0]
			));

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_set_default', 'wp_bfi_ajax_featured_image_set_default');

		function wp_bfi_ajax_featured_image_info() {

			$post_id = intval($_POST['post-id']);
			if( !$post_id ) $post_id = '';

			$id = get_post_thumbnail_id($post_id);

        	echo wp_bfi_get_image_information($id);

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_info', 'wp_bfi_ajax_featured_image_info');

		function wp_bfi_ajax_featured_image_info_default() {

			$post_type = sanitize_text_field($_POST['post-type']);
			if( !$post_type ) $post_type = '';

			echo wp_bfi_get_image_information(get_option('wp_bfi_default_featured_image_' . $post_type));

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_info_default', 'wp_bfi_ajax_featured_image_info_default');

		function wp_bfi_ajax_featured_image_unlink_all() {

        	global $wpdb;

			$post_type = sanitize_text_field($_POST['post-type']);

			$wpdb->query(
				$wpdb->prepare("DELETE $wpdb->postmeta FROM $wpdb->postmeta
					INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_type = %s AND $wpdb->postmeta.meta_key = '_thumbnail_id'", $post_type
				)
			);

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_featured_image_unlink_all', 'wp_bfi_ajax_featured_image_unlink_all');

		function wp_bfi_ajax_wp_bfi_disabled() {

			update_option('wp_bfi_disabled_' . sanitize_text_field($_POST['post-type']), intval($_POST['disabled']));

			wp_die();

		} add_action('wp_ajax_wp_bfi_ajax_wp_bfi_disabled', 'wp_bfi_ajax_wp_bfi_disabled');


	//	DEFAULT FEATURED IMAGE
	//	----------------------------------------------------------------------------------------------------

		function wp_bfi_set_default_featured_image($post_id) {

			if( !has_post_thumbnail($post_id) && get_option('wp_bfi_default_featured_image_' . get_post_type()) ):

				set_post_thumbnail($post_id, get_option('wp_bfi_default_featured_image_' . get_post_type()));

			endif;

		} add_action('save_post', 'wp_bfi_set_default_featured_image');

		function wp_bfi_featured_image_metabox($content) {

			if( !has_post_thumbnail() && get_option('wp_bfi_default_featured_image_' . get_post_type()) ):

				$content .= __('<p><i class="fa fa-exclamation-triangle"></i> There is a default featured image for this post type. It will be used if it isn\'t set here.</p>', 'wp-bfi-better-featured-image');

			endif;

			return $content;

		} add_filter('admin_post_thumbnail_html', 'wp_bfi_featured_image_metabox');


	//	CONVERT FILE SIZES
	//	----------------------------------------------------------------------------------------------------

	function wp_bfi_format_bytes($bytes, $precision = 2) {

	    $units = array(
	    	__('B', 'wp-bfi-better-featured-image'),
	    	__('KB', 'wp-bfi-better-featured-image'),
	    	__('MB', 'wp-bfi-better-featured-image'),
	    	__('GB', 'wp-bfi-better-featured-image'),
	    	__('TB', 'wp-bfi-better-featured-image')
		);

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];

	}


	//	GET FEATURED IMAGE INFORMATION
	//	----------------------------------------------------------------------------------------------------

	function wp_bfi_get_image_information($id) {

		$thumb_url = wp_get_attachment_image_src($id, 'full');

	    $tmp = curl_init($thumb_url[0]);

		curl_setopt($tmp, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($tmp, CURLOPT_HEADER, TRUE);
		curl_setopt($tmp, CURLOPT_NOBODY, TRUE);

		$data = curl_exec($tmp);

		$size = curl_getinfo($tmp, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		$type = curl_getinfo($tmp, CURLINFO_CONTENT_TYPE);

		curl_close($tmp);

		$dimensions = getimagesize($thumb_url[0]); ?>

		<?php echo $dimensions[0]; ?> x <?php echo $dimensions[1]; ?> | <?php echo wp_bfi_format_bytes($size); ?><br />
		<strong><?php echo basename($thumb_url[0]); ?></strong><br /><small>(<?php echo $type; ?>)</small>

	<?php }

?>
