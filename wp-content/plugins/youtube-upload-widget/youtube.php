<?php
/*
Plugin Name: YouTube Upload Widget
Plugin URI: http://wordpress.org/extend/plugins/youtube-upload-widget/
Description: The YouTube Upload Widget adds video to your blog posts!  You can now create a video through your webcam, upload it directly to your own YouTube channel, and share it in your post without ever leaving WordPress. All the work is done securely through the <a href="https://developers.google.com/youtube/youtube_upload_widget">YouTube APIs</a>.
Version: 1.1
Author: YouTube
Author URI: http://www.youtube.com/dev/
License: Apache v2
Text Domain: wp-youtube
*/

/*
 * Copyright 2013 Google Inc. All Rights Reserved.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * Disclaimer: This project is not a Google product.
 */

class Google_WP_YouTube {
	public static $instance;

	public function __construct() {
		self::$instance = $this;
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		// Load translations
		load_plugin_textdomain( 'wp-youtube', false, basename( dirname( __FILE__ ) ) . '/languages' );

		// Actions
		add_action( 'media_buttons',                   array( $this, 'media_buttons' ), 11 );
		add_action( 'admin_print_styles-post.php',     array( $this, 'print_css'     )     );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'print_css'     )     );
		add_action( 'wp_ajax_youtube_popup',           array( $this, 'popup'         )     );
		add_action( 'wp_ajax_youtube_videos',          array( $this, 'videos'        )     );
		add_action( 'admin_init',                      array( $this, 'admin_init'    )     );
	}

	/**
	 * Adds the YouTube button under the media buttons.
	 */
	public function media_buttons() {
		global $post_ID, $temp_ID;
		$iframe_post_id = (int) ( 0 == $post_ID ? $temp_ID : $post_ID );
		?><a href="<?php echo esc_url( admin_url( '/admin-ajax.php?post_id=' . $iframe_post_id . '&amp;youtube=popup&amp;action=youtube_popup&amp;TB_iframe=true&amp;width=768' ) ); ?>" class="yt-button thickbox" title="<?php esc_attr_e( 'YouTube', 'wp-youtube' ); ?>"></a><?php
	}

	/**
	 * Produces the content for the YouTube ThickBox popup.
	 */
	public function popup() {
		require 'yt-popup.php';
		die();
	}

	/**
	 * Ajax handler for the search feature. Takes a search term and returns videos that match the search term.
	 */
	public function videos() {
		if ( !empty( $_POST['yt_search'] ) ) {
			$yt_search = sanitize_text_field( $_POST['yt_search'] );
			update_user_meta( get_current_user_id(), 'yt_search', $yt_search );
		} else {
			$yt_search = get_user_meta( get_current_user_id(), 'yt_search', true );
		}

		$url = add_query_arg( array( 'q' => urlencode($yt_search) ), 'http://gdata.youtube.com/feeds/api/videos?v=2&alt=jsonc&client=youtube-wordpress-plugin' );
		$response = wp_remote_get( $url );
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			$videos = $response->data->items;
			if ( ! empty( $videos ) ) {
				foreach ( $videos as $video ) : ?>
<div class="videos">
	<div class="video" style="float: left; margin-right: 10px;">
		<iframe width="300" src="<?php echo esc_url( 'http://www.youtube.com/embed/' . $video->id . '?controls=2' ); ?>" frameborder="0" allowfullscreen></iframe>
	</div>
	<div class="video-meta">
		<h2><?php echo esc_html( $video->title ); ?></h2>
		<p><a href="#" class="insert-video" data-video="<?php echo esc_attr( $video->id ) ;?>">Insert into Post</a></p>
	</div>
	<div class="clear"></div>
</div><?php endforeach;
			} else {
				?>
				<div class="updated">
					<p><?php _e( 'Sorry, no videos were returned for your search.', 'wp-youtube' ) ?></p>
				</div>
				<?php
			}
		}
		exit;
	}

	/**
	 * Registers the JavaScript for the YouTube ThickBox popup.
	 */
	public function admin_init() {
		wp_register_script( 'youtube-popup', plugins_url( 'js/popup.js', __FILE__ ), array( 'jquery' ), '20130401' );
	}

	/**
	 * Prints the CSS for the YouTube button for the add new post and the edit post pages.
	 */
	public function print_css() {
		?><style type="text/css">.yt-button { vertical-align: middle; background: url('<?php echo plugins_url( "img/yt-icon.png", __FILE__ ); ?>') no-repeat top left; width:26px; height: 16px; display:inline-block; text-indent:-9999px; overflow:hidden; } .yt-button:hover { background-image: url('<?php echo plugins_url( "img/yt-icon-color.png", __FILE__ ); ?>'); }</style><?php
	}
}

new Google_WP_YouTube;