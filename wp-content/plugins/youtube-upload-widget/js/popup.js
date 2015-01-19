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
(function($) {
	if (!window.YT) {
		window.YT = {};
	}

	YT.Popup = function()  {
		function switchTabs(whichTab) {
			$('.yt-tabs li a').removeClass('current');
			$('#yt-tab-'+ whichTab +' a').addClass('current');
			$('.yt-content').hide();
			$('#yt-content-' + whichTab).show();
		}

		return {
			init: function() {
				$('#yt-tab-browser a').click(function() {
					switchTabs('browser');
					return false;
				});
				$('#yt-tab-uploader a').click(function() {
					switchTabs('uploader');
					return false;
				});
				$('#yt-content-browser-form').on( 'submit.wp-youtube', function(e) {
					$('#yt-content-browser-form, #yt-videos').hide();
					$('#yt-content-browser .ytloader').show();
					var data = {
						action: 'youtube_videos',
						yt_search: $('#yt-search').val()
					};

					$.post(parent.ajaxurl, data, function(response) {
						$('#yt-videos').html( response );
						$('#yt-content-browser-form, #yt-videos').show();
						$('#yt-content-browser .ytloader').hide();
					});
					return false;
				});
				$('.yt-contents').on( 'click.wp-youtube', '.insert-video', function() {
					window.parent.send_to_editor("\nhttp://www.youtube.com/watch?v=" + $(this).data('video') + "\n" );
					return false;
				});
				$('#yt-content-uploader').on( 'click.wp-youtube', '#ytloadwidget', function() {
					$('#form').hide();
					$('#yt-content-uploader .ytloader').show();
					widget = new YT.UploadWidget('widget', {
						width: 635,
						events: {
							'onApiReady': onApiReady,
							'onUploadSuccess': onUploadSuccess,
							'onProcessingComplete': onProcessingComplete
						}
					});
					return false;
				});
			}
		};
	}();
})(jQuery);