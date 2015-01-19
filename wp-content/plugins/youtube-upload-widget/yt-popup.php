<!DOCTYPE html>
<!--
Copyright 2013 Google Inc. All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

Disclaimer: This project is not a Google product.
-->
<html>
<head>
<?php wp_print_scripts( array( 'jquery', 'youtube-popup' ) ); ?>
<?php wp_print_styles( array( 'global', 'media', 'wp-admin', 'colors' ) ); ?>
<script>
jQuery(document).ready(function() {
	YT.Popup.init();
});
</script>
<style type="text/css">
p,h1,form,button{border:0;margin:0;padding:0}.yt-metadata{width:620px;padding:14px}.yt-metadata h1{font-size:14px;font-weight:bold;margin-bottom:8px}.yt-metadata p{font-size:11px;margin-bottom:20px;padding-bottom:10px}.yt-metadata label{display:block;font-weight:bold;width:100px;float:left;margin-top:6px}.yt-metadata input,.yt-metadata textarea{float:left;font-size:12px;padding:4px 2px;width:450px;margin:2px 0 20px 10px}.yt-metadata button{clear:both;margin-left:150px;width:125px;height:31px;text-align:center;line-height:31px;color:#fff;font-size:11px;font-weight:bold}div.updated{margin-bottom:15px}.yt-metadata #ytloadwidget{width:150px}.ytloader{width:100%;display: none;position:relative;}.ytloader img{margin:auto;position:absolute;left:0;right:0;}
</style>
</head>
<body id="media-upload">
	<div id="media-upload-header">
		<ul id="sidemenu" class="yt-tabs">
			<li id="yt-tab-uploader"><a class="current" href="#"><?php _e( 'Upload to YouTube', 'wp-youtube' ); ?></a></li>
			<li id="yt-tab-browser"><a href="#"><?php _e( 'Browse YouTube Videos','wp-youtube' ); ?></a></li>
		</ul>
	</div>
	<div class="yt-contents" style="margin: 15px;">
		<div id="yt-content-browser" class="yt-content" style="display: none;">
			<div class="ytloader" style="display:none;"><img src="<?php echo esc_url( plugins_url( 'img/loading.gif', __FILE__ ) ); ?>" /></div>
			<form id="yt-content-browser-form">
				<p>
					Search: <input type="text" name="yt_search" id="yt-search"/>
					<?php submit_button( 'Find Videos', 'primary', 'submit', false ); ?>
				</p>
			</form>
			<div id="yt-videos"></div>
		</div>

		<div id="yt-content-uploader" class="yt-content">
			<div id="message" class="updated" style="display:none;"></div>
			<div class="ytloader" style="display:none;"><img src="<?php echo esc_url( plugins_url( 'img/loading.gif', __FILE__ ) ); ?>" /></div>
			<div id="widget"></div>
			<form id="form" name="form" method="post" action="" class="yt-metadata">
				<h1>YouTube Video Metadata</h1>
				<label>Title</label>
				<input type="text" name="title" id="title" value="" />

				<label>Description</label>
				<textarea name="desc" id="desc"><?php if ( ! empty( $_GET['post_id'] ) ) echo esc_url( get_permalink( absint( $_GET['post_id'] ) ) ); ?></textarea>

				<label>Keywords</label>
				<input type="text" name="keywords" id="keywords" palceholder="WordPress, Plugin, YouTube" />

				<?php submit_button( 'Record from webcam', 'primary', 'ytloadwidget', true ); ?>

			</form>
		</div>
    	<div id="player"></div>
    	<?php submit_button( 'Insert', 'primary', 'submit', true, array( 'style' => 'display:none;' ) );?>
	</div>

<script>
var tag = document.createElement('script');
tag.src = "//www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var widget;
var player;

function onYouTubeIframeAPIReady() {
	// Don't load the widget until the user adds the video meta data and clicks on the "Record from webcam" button because we can't attach the meta data after the widget is initialized.
}

function onApiReady(event) {
	jQuery('#yt-content-uploader .ytloader').hide();
	widget.setVideoTitle( jQuery('#title').val() );
	widget.setVideoDescription( jQuery('#desc').val() );
	widget.setVideoKeywords( jQuery('#keywords').val() );
}

function onUploadSuccess(event) {
	jQuery('#widget').hide();
	jQuery('.yt-metadata').hide();
	jQuery('#message').html('<p>The video was uploaded and is currently being processed. A preview of your video will appear shortly.</p><p><a href="#" class="insert-video" data-video="' + event.data.videoId + '">Insert without reviewing video</a>.</p><p>YouTube link: <a href="http://www.youtube.com/watch?v=' + event.data.videoId + '">http://www.youtube.com/watch?v=' + event.data.videoId + '</a></p>' );
	jQuery('#message').show();
	jQuery('#yt-content-uploader .ytloader').show();
}

function onProcessingComplete(event) {
	jQuery('#yt-content-uploader .ytloader').hide();
	jQuery('#message').html( '<p>The video was been processed. <a href="#" class="insert-video" data-video="' + event.data.videoId + '">Insert into post</a>.</p>' );
	player = new YT.Player('player', {
		height: 390,
		width: 640,
		videoId: event.data.videoId,
		events: {}
	});
}
</script>
</body>
</html>